<?php

declare(strict_types=1);

namespace App\Controladores;

use App\Modelos\Medico;

/**
 * Controlador del modulo de medicos.
 *
 * Traduce la peticion HTTP (el parametro `accion` y los datos del formulario)
 * en llamadas al modelo y presenta el resultado a traves de las vistas.
 */
final class MedicoControlador
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
     * Campos del formulario de medicos, en el orden de la tabla.
     */
    private const CAMPOS = [
        'identificacion',
        'nombres',
        'apellidos',
        'especialidad',
        'telefono',
        'correo',
        'usuarios_id_usuario',
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
     * Presenta el formulario de alta de medicos.
     *
     * @return void
     */
    public function mostrarRegistro(): void
    {
        $mensajeError = null;
        $valores = $this->valoresIniciales();

        require RUTA_VISTAS . '/medicos/registro.php';
    }

    /**
     * Registra un medico a partir de los datos enviados por el formulario.
     *
     * @return void
     * @throws \PDOException Si la base de datos no esta disponible.
     */
    public function registrar(): void
    {
        $valores = $this->valoresIniciales();

        try {
            $valores = $this->leerFormulario();
            $medico = $this->construirMedico($valores);

            if (Medico::existeIdentificacion($medico->getIdentificacion())) {
                throw new \InvalidArgumentException(
                    'Ya existe un medico registrado con ese numero de identificacion.'
                );
            }

            if (!$medico->agregarMedico()) {
                throw new \RuntimeException('No fue posible registrar el medico.');
            }

            $mensajeExito = 'El medico fue registrado correctamente.';
            $accionRealizada = 'Registro de medico';

            require RUTA_VISTAS . '/medicos/exito.php';
        } catch (\InvalidArgumentException $error) {
            $mensajeError = $error->getMessage();

            require RUTA_VISTAS . '/medicos/registro.php';
        } catch (\PDOException $error) {
            $this->registrarError('registrar', $error);
            $mensajeError = 'No fue posible registrar el medico. Intente nuevamente.';

            require RUTA_VISTAS . '/medicos/registro.php';
        }
    }

    /**
     * Presenta el formulario de edicion con los datos del medico.
     *
     * @return void
     * @throws \PDOException Si la base de datos no esta disponible.
     */
    public function mostrarEdicion(): void
    {
        $idMedico = $this->obtenerIdSolicitado();
        $mensajeError = null;
        $valores = $this->valoresIniciales();

        if ($idMedico === null) {
            $this->mostrarListadoConError('No se indico el medico que se desea editar.');

            return;
        }

        try {
            $medico = Medico::buscarPorId($idMedico);
        } catch (\PDOException $error) {
            $this->registrarError('mostrarEdicion', $error);
            $this->mostrarListadoConError('No fue posible consultar el medico solicitado.');

            return;
        }

        if ($medico === null) {
            $this->mostrarListadoConError('El medico solicitado no existe en la base de datos.');

            return;
        }

        $valores = $this->valoresDesdeMedico($medico);
        $idMedico = $medico->getIdMedico();

        require RUTA_VISTAS . '/medicos/edicion.php';
    }

    /**
     * Actualiza un medico a partir de los datos enviados por el formulario.
     *
     * @return void
     * @throws \PDOException Si la base de datos no esta disponible.
     */
    public function actualizar(): void
    {
        $idMedico = $this->obtenerIdSolicitado();
        $valores = $this->valoresIniciales();

        try {
            $valores = $this->leerFormulario();

            if ($idMedico === null) {
                throw new \InvalidArgumentException(
                    'No se indico el medico que se desea actualizar.'
                );
            }

            $medico = $this->construirMedico($valores);

            $yaExiste = Medico::existeIdentificacion($medico->getIdentificacion(), $idMedico);

            if ($yaExiste) {
                throw new \InvalidArgumentException(
                    'Ya existe otro medico registrado con ese numero de identificacion.'
                );
            }

            if (!$medico->actualizarMedico($idMedico)) {
                throw new \RuntimeException('No fue posible actualizar el medico.');
            }

            $mensajeExito = 'Los datos del medico fueron actualizados correctamente.';
            $accionRealizada = 'Edicion de medico';

            require RUTA_VISTAS . '/medicos/exito.php';
        } catch (\InvalidArgumentException $error) {
            $mensajeError = $error->getMessage();

            if ($idMedico === null) {
                $this->mostrarListadoConError($mensajeError);

                return;
            }

            require RUTA_VISTAS . '/medicos/edicion.php';
        } catch (\PDOException $error) {
            $this->registrarError('actualizar', $error);
            $mensajeError = 'No fue posible actualizar el medico. Intente nuevamente.';

            if ($idMedico === null) {
                $this->mostrarListadoConError($mensajeError);

                return;
            }

            require RUTA_VISTAS . '/medicos/edicion.php';
        }
    }

    /**
     * Elimina un medico, previa confirmacion del usuario.
     *
     * @return void
     * @throws \PDOException Si la base de datos no esta disponible.
     */
    public function eliminar(): void
    {
        $idMedico = $this->obtenerIdSolicitado();

        try {
            if ($idMedico === null) {
                throw new \InvalidArgumentException(
                    'No se indico el medico que se desea eliminar.'
                );
            }

            $medico = Medico::buscarPorId($idMedico);

            if ($medico === null) {
                throw new \InvalidArgumentException(
                    'El medico solicitado no existe en la base de datos.'
                );
            }

            if (!$this->esSolicitudPost()) {
                $mensajeConfirmacion = 'Confirme la eliminacion del medico registrado.';
                $valores = $this->valoresDesdeMedico($medico);
                $idMedico = $medico->getIdMedico();

                require RUTA_VISTAS . '/medicos/confirmacion.php';

                return;
            }

            if (!Medico::eliminarMedico($idMedico)) {
                throw new \RuntimeException('No fue posible eliminar el medico.');
            }

            $mensajeExito = 'El medico fue eliminado correctamente.';
            $accionRealizada = 'Eliminacion de medico';

            require RUTA_VISTAS . '/medicos/exito.php';
        } catch (\InvalidArgumentException $error) {
            $this->mostrarListadoConError($error->getMessage());
        } catch (\PDOException $error) {
            $this->registrarError('eliminar', $error);
            $this->mostrarListadoConError(
                'No fue posible eliminar el medico. Intente nuevamente.'
            );
        }
    }

    /**
     * Presenta el listado de medicos registrados.
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
     * Presenta el listado de medicos con un mensaje de error opcional.
     *
     * @param string|null $mensajeError Mensaje que se mostrara al usuario.
     * @return void
     */
    private function mostrarListadoConError(?string $mensajeError): void
    {
        $filas = [];

        try {
            $filas = Medico::listarMedicos();
        } catch (\PDOException $error) {
            $this->registrarError('mostrarListado', $error);
            $mensajeError = 'No fue posible consultar el listado de medicos.';
        }

        require RUTA_VISTAS . '/medicos/listado.php';
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
     * Obtiene el identificador del medico solicitado.
     *
     * @return int|null Identificador del medico o `null` si no es valido.
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
     * Lee y sanea los datos del formulario de medicos.
     *
     * @return array<string, mixed> Valores del formulario listos para validar.
     */
    private function leerFormulario(): array
    {
        $valores = [];

        foreach (self::CAMPOS as $campo) {
            if ($campo === 'usuarios_id_usuario') {
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
     * Construye el modelo del medico con los datos del formulario.
     *
     * @param array<string, mixed> $valores Datos del formulario.
     * @return Medico Medico validado por el modelo.
     * @throws \InvalidArgumentException Si algun dato no cumple las reglas del modelo.
     */
    private function construirMedico(array $valores): Medico
    {
        $usuariosIdUsuario = is_int($valores['usuarios_id_usuario'])
            && $valores['usuarios_id_usuario'] > 0
            ? $valores['usuarios_id_usuario']
            : null;

        return new Medico(
            $valores['identificacion'],
            $valores['nombres'],
            $valores['apellidos'],
            $valores['especialidad'],
            $valores['telefono'] !== '' ? $valores['telefono'] : null,
            $valores['correo'] !== '' ? $valores['correo'] : null,
            $usuariosIdUsuario
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
            $valores[$campo] = $campo === 'usuarios_id_usuario' ? null : '';
        }

        return $valores;
    }

    /**
     * Devuelve los valores del formulario tomados de un medico.
     *
     * @param Medico $medico Medico consultado.
     * @return array<string, mixed> Valores del formulario.
     */
    private function valoresDesdeMedico(Medico $medico): array
    {
        return [
            'identificacion' => $medico->getIdentificacion(),
            'nombres' => $medico->getNombres(),
            'apellidos' => $medico->getApellidos(),
            'especialidad' => $medico->getEspecialidad(),
            'telefono' => (string) ($medico->getTelefono() ?? ''),
            'correo' => (string) ($medico->getCorreo() ?? ''),
            'usuarios_id_usuario' => $medico->getUsuariosIdUsuario(),
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
            '[medicos] operacion=%s error=%s mensaje=%s',
            $operacion,
            $error::class,
            $error->getMessage()
        ));
    }
}
