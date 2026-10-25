<?php

declare(strict_types=1);

namespace App\Controladores;

use App\Modelos\Paciente;

/**
 * Controlador del módulo de pacientes.
 *
 * Traduce la petición HTTP (el parámetro `accion` y los datos del formulario)
 * en llamadas al modelo y presenta el resultado a través de las vistas. La
 * validación de la entrada ocurre en esta capa (regla S-01) y los errores se
 * traducen a mensajes comprensibles sin exponer detalles internos (regla S-07).
 */
final class PacienteControlador
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
     * Campos del formulario de pacientes, en el orden de la tabla.
     */
    private const CAMPOS = [
        'nombre',
        'apellido',
        'numero_identificacion',
        'fecha_nacimiento',
        'genero',
        'direccion',
        'telefono',
        'email',
        'tipo_sangre',
        'alergias',
    ];

    /* ----------------------------------------------------------------- */
    /* Métodos públicos (reglas M-08 y D-01).                             */
    /* ----------------------------------------------------------------- */

    /**
     * Enruta la petición hacia la operación solicitada.
     *
     * @return void
     * @throws \PDOException Si la base de datos no está disponible.
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
     * Presenta el formulario de alta de pacientes.
     *
     * @return void
     */
    public function mostrarRegistro(): void
    {
        $mensajeError = null;
        $valores = $this->valoresIniciales();

        require RUTA_VISTAS . '/pacientes/registro.php';
    }

    /**
     * Registra un paciente a partir de los datos enviados por el formulario.
     *
     * @return void
     * @throws \PDOException Si la base de datos no está disponible.
     */
    public function registrar(): void
    {
        $valores = $this->valoresIniciales();

        try {
            $valores = $this->leerFormulario();
            $paciente = $this->construirPaciente($valores);

            if (Paciente::existeNumeroIdentificacion($paciente->getNumeroIdentificacion())) {
                throw new \InvalidArgumentException(
                    'Ya existe un paciente registrado con ese número de identificación.'
                );
            }

            if (!$paciente->registrar()) {
                throw new \RuntimeException('No fue posible registrar el paciente.');
            }

            $mensajeExito = 'El paciente fue registrado correctamente.';
            $accionRealizada = 'Registro de paciente';

            require RUTA_VISTAS . '/pacientes/exito.php';
        } catch (\InvalidArgumentException $error) {
            $mensajeError = $error->getMessage();

            require RUTA_VISTAS . '/pacientes/registro.php';
        } catch (\PDOException $error) {
            $this->registrarError('registrar', $error);
            $mensajeError = 'No fue posible registrar el paciente. Intente nuevamente.';

            require RUTA_VISTAS . '/pacientes/registro.php';
        }
    }

    /**
     * Presenta el formulario de edición con los datos del paciente.
     *
     * @return void
     * @throws \PDOException Si la base de datos no está disponible.
     */
    public function mostrarEdicion(): void
    {
        $idPaciente = $this->obtenerIdSolicitado();
        $mensajeError = null;
        $valores = $this->valoresIniciales();

        if ($idPaciente === null) {
            $this->mostrarListadoConError('No se indicó el paciente que se desea editar.');

            return;
        }

        try {
            $paciente = Paciente::buscarPorId($idPaciente);
        } catch (\PDOException $error) {
            $this->registrarError('mostrarEdicion', $error);
            $this->mostrarListadoConError('No fue posible consultar el paciente solicitado.');

            return;
        }

        if ($paciente === null) {
            $this->mostrarListadoConError('El paciente solicitado no existe en la base de datos.');

            return;
        }

        $valores = $this->valoresDesdePaciente($paciente);

        require RUTA_VISTAS . '/pacientes/edicion.php';
    }

    /**
     * Actualiza un paciente a partir de los datos enviados por el formulario.
     *
     * @return void
     * @throws \PDOException Si la base de datos no está disponible.
     */
    public function actualizar(): void
    {
        $idPaciente = $this->obtenerIdSolicitado();
        $valores = $this->valoresIniciales();

        try {
            $valores = $this->leerFormulario();

            if ($idPaciente === null) {
                throw new \InvalidArgumentException(
                    'No se indicó el paciente que se desea actualizar.'
                );
            }

            $paciente = $this->construirPaciente($valores);
            $paciente->setIdPaciente($idPaciente);

            $yaExiste = Paciente::existeNumeroIdentificacion(
                $paciente->getNumeroIdentificacion(),
                $idPaciente
            );

            if ($yaExiste) {
                throw new \InvalidArgumentException(
                    'Ya existe otro paciente registrado con ese número de identificación.'
                );
            }

            if (!$paciente->actualizar()) {
                throw new \RuntimeException('No fue posible actualizar el paciente.');
            }

            $mensajeExito = 'Los datos del paciente fueron actualizados correctamente.';
            $accionRealizada = 'Edición de paciente';

            require RUTA_VISTAS . '/pacientes/exito.php';
        } catch (\InvalidArgumentException $error) {
            $mensajeError = $error->getMessage();

            if ($idPaciente === null) {
                $this->mostrarListadoConError($mensajeError);

                return;
            }

            require RUTA_VISTAS . '/pacientes/edicion.php';
        } catch (\PDOException $error) {
            $this->registrarError('actualizar', $error);
            $mensajeError = 'No fue posible actualizar el paciente. Intente nuevamente.';

            if ($idPaciente === null) {
                $this->mostrarListadoConError($mensajeError);

                return;
            }

            require RUTA_VISTAS . '/pacientes/edicion.php';
        }
    }

    /**
     * Elimina un paciente, previa confirmación del usuario.
     *
     * Un enlace GET nunca modifica datos: la primera visita muestra la vista
     * de confirmación y el borrado sólo se ejecuta cuando la petición es POST.
     *
     * @return void
     * @throws \PDOException Si la base de datos no está disponible.
     */
    public function eliminar(): void
    {
        $idPaciente = $this->obtenerIdSolicitado();

        try {
            if ($idPaciente === null) {
                throw new \InvalidArgumentException(
                    'No se indicado el paciente que se desea eliminar.'
                );
            }

            $paciente = Paciente::buscarPorId($idPaciente);

            if ($paciente === null) {
                throw new \InvalidArgumentException(
                    'El paciente solicitado no existe en la base de datos.'
                );
            }

            if (!$this->esSolicitudPost()) {
                $mensajeConfirmacion = 'Confirme la eliminación del paciente registrado.';
                $valores = $this->valoresDesdePaciente($paciente);

                require RUTA_VISTAS . '/pacientes/confirmacion.php';

                return;
            }

            if (!$paciente->eliminar()) {
                throw new \RuntimeException('No fue posible eliminar el paciente.');
            }

            $mensajeExito = 'El paciente fue eliminado correctamente.';
            $accionRealizada = 'Eliminación de paciente';

            require RUTA_VISTAS . '/pacientes/exito.php';
        } catch (\InvalidArgumentException $error) {
            $this->mostrarListadoConError($error->getMessage());
        } catch (\PDOException $error) {
            $this->registrarError('eliminar', $error);
            $this->mostrarListadoConError(
                'No fue posible eliminar el paciente. Intente nuevamente.'
            );
        }
    }

    /**
     * Presenta el listado de pacientes registrados.
     *
     * @return void
     * @throws \PDOException Si la base de datos no está disponible.
     */
    public function mostrarListado(): void
    {
        $this->mostrarListadoConError(null);
    }

    /* ----------------------------------------------------------------- */
    /* Métodos privados (regla D-06).                                     */
    /* ----------------------------------------------------------------- */

    /**
     * Presenta el listado de pacientes con un mensaje de error opcional.
     *
     * @param string|null $mensajeError Mensaje que se mostrará al usuario.
     * @return void
     */
    private function mostrarListadoConError(?string $mensajeError): void
    {
        $filas = [];

        try {
            $filas = Paciente::listar();
        } catch (\PDOException $error) {
            $this->registrarError('mostrarListado', $error);
            $mensajeError = 'No fue posible consultar el listado de pacientes.';
        }

        require RUTA_VISTAS . '/pacientes/listado.php';
    }

    /**
     * Obtiene la acción solicitada en la URL.
     *
     * @return string Nombre de la acción o cadena vacía si no se esperaba ninguna.
     */
    private function obtenerAccion(): string
    {
        $accion = self::leerTextoEntrada(INPUT_GET, 'accion');

        return $accion === null ? '' : trim($accion);
    }

    /**
     * Obtiene el identificador del paciente solicitado.
     *
     * @return int|null Identificador del paciente o `null` si no es válido.
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
     * Indica si la petición actual se realizó con el método POST.
     *
     * @return bool `true` si la petición es un POST.
     */
    private function esSolicitudPost(): bool
    {
        return strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'POST';
    }

    /**
     * Lee y sanea los datos del formulario de pacientes.
     *
     * @return array<string, string> Valores del formulario listos para validar.
     * @throws \InvalidArgumentException Si el correo electrónico no tiene un formato válido.
     */
    private function leerFormulario(): array
    {
        $valores = [];

        foreach (self::CAMPOS as $campo) {
            $valores[$campo] = $campo === 'email'
                ? (string) ($this->leerCorreo() ?? '')
                : (string) ($this->leerTexto($campo) ?? '');
        }

        return $valores;
    }

    /**
     * Lee un campo de texto del formulario.
     *
     * @param string $campo Nombre del campo.
     * @return string|null Texto saneado o `null` si el campo está vacío.
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
     * Lee el correo electrónico del formulario.
     *
     * El valor se devuelve tal como llegó cuando no cumple el formato: el
     * modelo lo rechaza en su modificador `setEmail()`, que es la única fuente
     * de validación de las reglas del dominio (regla S-04).
     *
     * @return string|null Correo electrónico o `null` si el campo está vacío.
     */
    private function leerCorreo(): ?string
    {
        return self::leerCorreoEntrada(INPUT_POST, 'email');
    }

    /**
     * Construye el modelo del paciente con los datos del formulario.
     *
     * @param array<string, string> $valores Datos del formulario.
     * @return Paciente Paciente validado por el modelo.
     * @throws \InvalidArgumentException Si algún dato no cumple las reglas del modelo.
     */
    private function construirPaciente(array $valores): Paciente
    {
        return new Paciente(
            $valores['nombre'],
            $valores['apellido'],
            $valores['numero_identificacion'],
            $valores['fecha_nacimiento'],
            $valores['genero'],
            $valores['direccion'],
            $valores['telefono'],
            $valores['email'],
            $valores['tipo_sangre'],
            $valores['alergias']
        );
    }

    /**
     * Devuelve los valores vacíos del formulario.
     *
     * @return array<string, string> Valores iniciales del formulario.
     */
    private function valoresIniciales(): array
    {
        $valores = [];

        foreach (self::CAMPOS as $campo) {
            $valores[$campo] = '';
        }

        return $valores;
    }

    /**
     * Devuelve los valores del formulario tomados de un paciente.
     *
     * @param Paciente $paciente Paciente consultado.
     * @return array<string, string> Valores del formulario.
     */
    private function valoresDesdePaciente(Paciente $paciente): array
    {
        return [
            'nombre' => $paciente->getNombre(),
            'apellido' => $paciente->getApellido(),
            'numero_identificacion' => $paciente->getNumeroIdentificacion(),
            'fecha_nacimiento' => (string) ($paciente->getFechaNacimiento() ?? ''),
            'genero' => (string) ($paciente->getGenero() ?? ''),
            'direccion' => (string) ($paciente->getDireccion() ?? ''),
            'telefono' => (string) ($paciente->getTelefono() ?? ''),
            'email' => (string) ($paciente->getEmail() ?? ''),
            'tipo_sangre' => (string) ($paciente->getTipoSangre() ?? ''),
            'alergias' => (string) ($paciente->getAlergias() ?? ''),
        ];
    }

    /**
     * Lee un campo de texto de la entrada del navegador.
     *
     * `FILTER_SANITIZE_STRING` está obsoleto desde PHP 8.1 y su uso genera un
     * aviso `E_DEPRECATED`. El aviso se silencia únicamente durante la
     * lectura, porque el constante se resuelve al ejecutarse la instrucción;
     * de este modo la respuesta al usuario no se mezcla con avisos del
     * lenguaje.
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
     * @return int|null Entero leído o `null` si el campo no existe o no es válido.
     */
    private static function leerEnteroEntrada(int $tipoEntrada, string $campo): ?int
    {
        $valor = filter_input($tipoEntrada, $campo, FILTER_VALIDATE_INT);

        return is_int($valor) ? $valor : null;
    }

    /**
     * Lee el campo de correo electrónico de la entrada del navegador.
     *
     * @param int $tipoEntrada Tipo de entrada (`INPUT_GET` o `INPUT_POST`).
     * @param string $campo Nombre del campo a leer.
     * @return string|null Correo electrónico o `null` si el campo está vacío.
     */
    private static function leerCorreoEntrada(int $tipoEntrada, string $campo): ?string
    {
        $saneado = filter_input($tipoEntrada, $campo, FILTER_SANITIZE_EMAIL);

        if (!is_string($saneado) || trim($saneado) === '') {
            return null;
        }

        $validado = filter_input($tipoEntrada, $campo, FILTER_VALIDATE_EMAIL);

        return is_string($validado) ? trim($validado) : trim($saneado);
    }

    /**
     * Registra el detalle de un error sin mostrarlo al usuario.
     *
     * @param string $operacion Operación que generó el error.
     * @param \Throwable $error Error capturado.
     * @return void
     */
    private function registrarError(string $operacion, \Throwable $error): void
    {
        error_log(sprintf(
            '[pacientes] operación=%s error=%s mensaje=%s',
            $operacion,
            $error::class,
            $error->getMessage()
        ));
    }
}
