<?php

declare(strict_types=1);

namespace App\Controladores;

use App\Modelos\Usuario;

/**
 * Controlador del modulo de usuarios.
 *
 * Traduce la peticion HTTP (el parametro `accion` y los datos del formulario)
 * en llamadas al modelo y presenta el resultado a traves de las vistas.
 */
final class UsuarioControlador
{
    /* ----------------------------------------------------------------- */
    /* Constantes de clase (regla V-09).                                 */
    /* ----------------------------------------------------------------- */

    public const ACCION_REGISTRO = 'registro';
    public const ACCION_REGISTRAR = 'registrar';
    public const ACCION_EDICION = 'edicion';
    public const ACCION_ACTUALIZAR = 'actualizar';
    public const ACCION_ELIMINAR = 'eliminar';
    public const ACCION_LISTADO = 'listado';

    /* ----------------------------------------------------------------- */
    /* Propiedades (reglas P-01 y P-02).                                 */
    /* ----------------------------------------------------------------- */

    /**
     * Campos del formulario de usuarios, en el orden de la tabla.
     */
    private const CAMPOS = [
        'usuario',
        'password',
        'estado',
        'rol',
        'roles_id_rol',
    ];

    /* ----------------------------------------------------------------- */
    /* Metodos publicos (reglas M-08 y D-01).                             */
    /* ----------------------------------------------------------------- */

    /**
     * Enruta la peticion hacia la operacion solicitada.
     *
     * @return void
     * @throws \PDOException Si la base de datos no esta disponible.
     */
    public function despachar(): void
    {
        $accion = $this->obtenerAccion();

        switch ($accion) {
            case self::ACCION_REGISTRO:
                $this->mostrarRegistro();
                break;
            case self::ACCION_REGISTRAR:
                $this->registrar();
                break;
            case self::ACCION_EDICION:
                $this->mostrarEdicion();
                break;
            case self::ACCION_ACTUALIZAR:
                $this->actualizar();
                break;
            case self::ACCION_ELIMINAR:
                $this->eliminar();
                break;
            case self::ACCION_LISTADO:
            default:
                $this->mostrarListado();
                break;
        }
    }

    /**
     * Presenta el formulario de alta de usuarios.
     *
     * @return void
     */
    public function mostrarRegistro(): void
    {
        $mensajeError = null;
        $valores = $this->valoresIniciales();

        require RUTA_VISTAS . '/usuarios/registro.php';
    }

    /**
     * Registra un usuario a partir de los datos enviados por el formulario.
     *
     * @return void
     * @throws \PDOException Si la base de datos no esta disponible.
     */
    public function registrar(): void
    {
        $valores = $this->valoresIniciales();

        try {
            $valores = $this->leerFormulario();
            $usuario = $this->construirUsuario($valores);

            if (Usuario::existeUsuario($usuario->getUsuario())) {
                throw new \InvalidArgumentException(
                    'Ya existe un usuario registrado con ese nombre de login.'
                );
            }

            if (!$usuario->agregarUsuario()) {
                throw new \RuntimeException('No fue posible registrar el usuario.');
            }

            $mensajeExito = 'El usuario fue registrado correctamente.';
            $accionRealizada = 'Registro de usuario';

            require RUTA_VISTAS . '/usuarios/exito.php';
        } catch (\InvalidArgumentException $error) {
            $mensajeError = $error->getMessage();

            require RUTA_VISTAS . '/usuarios/registro.php';
        } catch (\PDOException $error) {
            $this->registrarError('registrar', $error);
            $mensajeError = 'No fue posible registrar el usuario. Intente nuevamente.';

            require RUTA_VISTAS . '/usuarios/registro.php';
        }
    }

    /**
     * Presenta el formulario de edicion con los datos del usuario.
     *
     * @return void
     * @throws \PDOException Si la base de datos no esta disponible.
     */
    public function mostrarEdicion(): void
    {
        $idUsuario = $this->obtenerIdSolicitado();
        $mensajeError = null;
        $valores = $this->valoresIniciales();

        if ($idUsuario === null) {
            $this->mostrarListadoConError('No se indico el usuario que se desea editar.');

            return;
        }

        try {
            $usuario = Usuario::buscarPorId($idUsuario);
        } catch (\PDOException $error) {
            $this->registrarError('mostrarEdicion', $error);
            $this->mostrarListadoConError('No fue posible consultar el usuario solicitado.');

            return;
        }

        if ($usuario === null) {
            $this->mostrarListadoConError('El usuario solicitado no existe en la base de datos.');

            return;
        }

        $valores = $this->valoresDesdeUsuario($usuario);
        $idUsuario = $usuario->getIdUsuario();

        require RUTA_VISTAS . '/usuarios/edicion.php';
    }

    /**
     * Actualiza un usuario a partir de los datos enviados por el formulario.
     *
     * @return void
     * @throws \PDOException Si la base de datos no esta disponible.
     */
    public function actualizar(): void
    {
        $idUsuario = $this->obtenerIdSolicitado();
        $valores = $this->valoresIniciales();

        try {
            $valores = $this->leerFormulario();

            if ($idUsuario === null) {
                throw new \InvalidArgumentException(
                    'No se indico el usuario que se desea actualizar.'
                );
            }

            $usuario = $this->construirUsuario($valores);

            $yaExiste = Usuario::existeUsuario($usuario->getUsuario(), $idUsuario);

            if ($yaExiste) {
                throw new \InvalidArgumentException(
                    'Ya existe otro usuario registrado con ese nombre de login.'
                );
            }

            if (!$usuario->actualizarUsuario($idUsuario)) {
                throw new \RuntimeException('No fue posible actualizar el usuario.');
            }

            $mensajeExito = 'Los datos del usuario fueron actualizados correctamente.';
            $accionRealizada = 'Edicion de usuario';

            require RUTA_VISTAS . '/usuarios/exito.php';
        } catch (\InvalidArgumentException $error) {
            $mensajeError = $error->getMessage();

            if ($idUsuario === null) {
                $this->mostrarListadoConError($mensajeError);

                return;
            }

            require RUTA_VISTAS . '/usuarios/edicion.php';
        } catch (\PDOException $error) {
            $this->registrarError('actualizar', $error);
            $mensajeError = 'No fue posible actualizar el usuario. Intente nuevamente.';

            if ($idUsuario === null) {
                $this->mostrarListadoConError($mensajeError);

                return;
            }

            require RUTA_VISTAS . '/usuarios/edicion.php';
        }
    }

    /**
     * Elimina un usuario, previa confirmacion del usuario.
     *
     * @return void
     * @throws \PDOException Si la base de datos no esta disponible.
     */
    public function eliminar(): void
    {
        $idUsuario = $this->obtenerIdSolicitado();

        try {
            if ($idUsuario === null) {
                throw new \InvalidArgumentException(
                    'No se indico el usuario que se desea eliminar.'
                );
            }

            $usuario = Usuario::buscarPorId($idUsuario);

            if ($usuario === null) {
                throw new \InvalidArgumentException(
                    'El usuario solicitado no existe en la base de datos.'
                );
            }

            if (!$this->esSolicitudPost()) {
                $mensajeConfirmacion = 'Confirme la eliminacion del usuario registrado.';
                $valores = $this->valoresDesdeUsuario($usuario);
                $idUsuario = $usuario->getIdUsuario();

                require RUTA_VISTAS . '/usuarios/confirmacion.php';

                return;
            }

            if (!Usuario::eliminarUsuario($idUsuario)) {
                throw new \RuntimeException('No fue posible eliminar el usuario.');
            }

            $mensajeExito = 'El usuario fue eliminado correctamente.';
            $accionRealizada = 'Eliminacion de usuario';

            require RUTA_VISTAS . '/usuarios/exito.php';
        } catch (\InvalidArgumentException $error) {
            $this->mostrarListadoConError($error->getMessage());
        } catch (\PDOException $error) {
            $this->registrarError('eliminar', $error);
            $this->mostrarListadoConError(
                'No fue posible eliminar el usuario. Intente nuevamente.'
            );
        }
    }

    /**
     * Presenta el listado de usuarios registrados.
     *
     * @return void
     * @throws \PDOException Si la base de datos no esta disponible.
     */
    public function mostrarListado(): void
    {
        $this->mostrarListadoConError(null);
    }

    /* ----------------------------------------------------------------- */
    /* Metodos privados (regla D-06).                                     */
    /* ----------------------------------------------------------------- */

    /**
     * Presenta el listado de usuarios con un mensaje de error opcional.
     *
     * @param string|null $mensajeError Mensaje que se mostrara al usuario.
     * @return void
     */
    private function mostrarListadoConError(?string $mensajeError): void
    {
        $filas = [];

        try {
            $filas = Usuario::listarUsuarios();
        } catch (\PDOException $error) {
            $this->registrarError('mostrarListado', $error);
            $mensajeError = 'No fue posible consultar el listado de usuarios.';
        }

        require RUTA_VISTAS . '/usuarios/listado.php';
    }

    /**
     * Obtiene la accion solicitada en la URL.
     *
     * @return string Nombre de la accion o cadena vacia si no se esperaba ninguna.
     */
    private function obtenerAccion(): string
    {
        $accion = self::leerTextoEntrada(INPUT_GET, 'accion');

        return $accion === null ? '' : trim($accion);
    }

    /**
     * Obtiene el identificador del usuario solicitado.
     *
     * @return int|null Identificador del usuario o `null` si no es valido.
     */
    private function obtenerIdSolicitado(): ?int
    {
        $idPost = self::leerEnteroEntrada(INPUT_POST, 'id');

        if ($idPost !== null) {
            return $idPost;
        }

        return self::leerEnteroEntrada(INPUT_GET, 'id');
    }

    /**
     * Indica si la peticion actual se realizo con el metodo POST.
     *
     * @return bool `true` si la peticion es un POST.
     */
    private function esSolicitudPost(): bool
    {
        return strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'POST';
    }

    /**
     * Lee y sanea los datos del formulario de usuarios.
     *
     * @return array<string, string> Valores del formulario listos para validar.
     */
    private function leerFormulario(): array
    {
        $valores = [];

        foreach (self::CAMPOS as $campo) {
            if ($campo === 'roles_id_rol') {
                $valores[$campo] = self::leerEnteroEntrada(INPUT_POST, $campo);

                continue;
            }

            $valores[$campo] = (string) ($this->leerTexto($campo) ?? '');
        }

        return $valores;
    }

    /**
     * Lee un campo de texto del formulario.
     *
     * @param string $campo Nombre del campo.
     * @return string|null Texto saneado o `null` si el campo esta vacio.
     */
    private function leerTexto(string $campo): ?string
    {
        $valor = self::leerTextoEntrada(INPUT_POST, $campo);

        if ($valor === null) {
            return null;
        }

        $valor = trim($valor);

        return $valor === '' ? null : $valor;
    }

    /**
     * Construye el modelo del usuario con los datos del formulario.
     *
     * @param array<string, mixed> $valores Datos del formulario.
     * @return Usuario Usuario validado por el modelo.
     * @throws \InvalidArgumentException Si algun dato no cumple las reglas del modelo.
     */
    private function construirUsuario(array $valores): Usuario
    {
        $rolesIdRol = is_int($valores['roles_id_rol']) && $valores['roles_id_rol'] > 0
            ? $valores['roles_id_rol']
            : null;

        return new Usuario(
            $valores['usuario'],
            $valores['password'],
            $valores['estado'],
            $valores['rol'],
            $rolesIdRol
        );
    }

    /**
     * Devuelve los valores vacios del formulario.
     *
     * @return array<string, mixed> Valores iniciales del formulario.
     */
    private function valoresIniciales(): array
    {
        $valores = [];

        foreach (self::CAMPOS as $campo) {
            $valores[$campo] = $campo === 'roles_id_rol' ? null : '';
        }

        return $valores;
    }

    /**
     * Devuelve los valores del formulario tomados de un usuario.
     *
     * @param Usuario $usuario Usuario consultado.
     * @return array<string, mixed> Valores del formulario.
     */
    private function valoresDesdeUsuario(Usuario $usuario): array
    {
        return [
            'usuario' => $usuario->getUsuario(),
            'password' => '',
            'estado' => $usuario->getEstado(),
            'rol' => $usuario->getRol(),
            'roles_id_rol' => $usuario->getRolesIdRol(),
        ];
    }

    /**
     * Lee un campo de texto de la entrada del navegador.
     *
     * @param int $tipoEntrada Tipo de entrada (`INPUT_GET` o `INPUT_POST`).
     * @param string $campo Nombre del campo a leer.
     * @return string|null Texto saneado o `null` si el campo no existe.
     */
    private static function leerTextoEntrada(int $tipoEntrada, string $campo): ?string
    {
        $nivelPrevio = error_reporting();
        error_reporting($nivelPrevio & ~E_DEPRECATED);

        try {
            $valor = filter_input($tipoEntrada, $campo, FILTER_SANITIZE_STRING);
        } finally {
            error_reporting($nivelPrevio);
        }

        return is_string($valor) ? $valor : null;
    }

    /**
     * Lee un campo entero de la entrada del navegador.
     *
     * @param int $tipoEntrada Tipo de entrada (`INPUT_GET` o `INPUT_POST`).
     * @param string $campo Nombre del campo a leer.
     * @return int|null Entero leido o `null` si el campo no existe o no es valido.
     */
    private static function leerEnteroEntrada(int $tipoEntrada, string $campo): ?int
    {
        $valor = filter_input($tipoEntrada, $campo, FILTER_VALIDATE_INT);

        return is_int($valor) ? $valor : null;
    }

    /**
     * Registra el detalle de un error sin mostrarlo al usuario.
     *
     * @param string $operacion Operacion que genero el error.
     * @param \Throwable $error Error capturado.
     * @return void
     */
    private function registrarError(string $operacion, \Throwable $error): void
    {
        error_log(sprintf(
            '[usuarios] operacion=%s error=%s mensaje=%s',
            $operacion,
            $error::class,
            $error->getMessage()
        ));
    }
}
