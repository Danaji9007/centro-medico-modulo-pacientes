<?php

declare(strict_types=1);

namespace App\Modelos;

use App\Infraestructura\BaseDeDatos;

/**
 * Modelo del paciente del centro médico.
 *
 * Concentra los datos del paciente y las operaciones de persistencia sobre la
 * tabla `PACIENTE`. Todas las reglas de validación viven en los modificadores
 * (`setX()`), de modo que ningún objeto puede quedar en un estado inválido
 * (regla EN-04 del estándar de codificación).
 */
final class Paciente
{
    /* ----------------------------------------------------------------- */
    /* Constantes de clase (regla V-09).                                 */
    /* ----------------------------------------------------------------- */

    public const GENERO_MASCULINO = 'M';
    public const GENERO_FEMENINO = 'F';
    public const GENERO_OTRO = 'O';

    public const LONGITUD_MAXIMA_NOMBRE = 100;
    public const LONGITUD_MAXIMA_APELLIDO = 100;
    public const LONGITUD_MAXIMA_IDENTIFICACION = 20;
    public const LONGITUD_MAXIMA_DIRECCION = 200;
    public const LONGITUD_MAXIMA_TELEFONO = 20;
    public const LONGITUD_MAXIMA_EMAIL = 100;
    public const LONGITUD_MAXIMA_TIPO_SANGRE = 5;

    public const FORMATO_FECHA = 'Y-m-d';
    public const PATRON_TELEFONO = '/^[0-9+\-\s()]{7,20}$/';

    /* ----------------------------------------------------------------- */
    /* Propiedades (reglas P-01, P-02 y P-03).                           */
    /* ----------------------------------------------------------------- */

    /**
     * Identificador del paciente; cero mientras el registro no está persistido.
     */
    private int $idPaciente = 0;

    private string $nombre = '';
    private string $apellido = '';
    private string $numeroIdentificacion = '';
    private ?string $fechaNacimiento = null;
    private ?string $genero = null;
    private ?string $direccion = null;
    private ?string $telefono = null;
    private ?string $email = null;
    private ?string $tipoSangre = null;
    private ?string $alergias = null;

    /* ----------------------------------------------------------------- */
    /* Constructor (reglas M-05 y D-03).                                 */
    /* ----------------------------------------------------------------- */

    /**
     * Construye un paciente con sus datos personales.
     *
     * La asignación se delega en los modificadores para no duplicar las
     * reglas de validación: si un dato es inválido, la excepción se produce
     * durante la construcción del objeto (regla D-03).
     *
     * @param string $nombre Nombre del paciente.
     * @param string $apellido Apellido del paciente.
     * @param string $numeroIdentificacion Número de identificación del paciente.
     * @param string|null $fechaNacimiento Fecha de nacimiento en formato `Y-m-d`.
     * @param string|null $genero Género: `M`, `F` u `O`.
     * @param string|null $direccion Dirección de residencia.
     * @param string|null $telefono Número de teléfono de contacto.
     * @param string|null $email Correo electrónico de contacto.
     * @param string|null $tipoSangre Grupo sanguíneo.
     * @param string|null $alergias Alergias registradas del paciente.
     * @throws \InvalidArgumentException Si algún dato no cumple las reglas del modelo.
     */
    public function __construct(
        string $nombre,
        string $apellido,
        string $numeroIdentificacion,
        ?string $fechaNacimiento = null,
        ?string $genero = null,
        ?string $direccion = null,
        ?string $telefono = null,
        ?string $email = null,
        ?string $tipoSangre = null,
        ?string $alergias = null
    ) {
        $this->setNombre($nombre);
        $this->setApellido($apellido);
        $this->setNumeroIdentificacion($numeroIdentificacion);
        $this->setFechaNacimiento($fechaNacimiento);
        $this->setGenero($genero);
        $this->setDireccion($direccion);
        $this->setTelefono($telefono);
        $this->setEmail($email);
        $this->setTipoSangre($tipoSangre);
        $this->setAlergias($alergias);
    }

    /* ----------------------------------------------------------------- */
    /* Consultores (regla M-02).                                          */
    /* ----------------------------------------------------------------- */

    /**
     * Obtiene el identificador del paciente.
     *
     * @return int Identificador del paciente.
     */
    public function getIdPaciente(): int
    {
        return $this->idPaciente;
    }

    /**
     * Obtiene el nombre del paciente.
     *
     * @return string Nombre del paciente.
     */
    public function getNombre(): string
    {
        return $this->nombre;
    }

    /**
     * Obtiene el apellido del paciente.
     *
     * @return string Apellido del paciente.
     */
    public function getApellido(): string
    {
        return $this->apellido;
    }

    /**
     * Obtiene el número de identificación del paciente.
     *
     * @return string Número de identificación.
     */
    public function getNumeroIdentificacion(): string
    {
        return $this->numeroIdentificacion;
    }

    /**
     * Obtiene la fecha de nacimiento del paciente.
     *
     * @return string|null Fecha de nacimiento en formato `Y-m-d`.
     */
    public function getFechaNacimiento(): ?string
    {
        return $this->fechaNacimiento;
    }

    /**
     * Obtiene el género del paciente.
     *
     * @return string|null Género: `M`, `F` u `O`.
     */
    public function getGenero(): ?string
    {
        return $this->genero;
    }

    /**
     * Obtiene la dirección de residencia del paciente.
     *
     * @return string|null Dirección de residencia.
     */
    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    /**
     * Obtiene el teléfono de contacto del paciente.
     *
     * @return string|null Número de teléfono.
     */
    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    /**
     * Obtiene el correo electrónico de contacto del paciente.
     *
     * @return string|null Correo electrónico.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Obtiene el grupo sanguíneo del paciente.
     *
     * @return string|null Grupo sanguíneo.
     */
    public function getTipoSangre(): ?string
    {
        return $this->tipoSangre;
    }

    /**
     * Obtiene las alergias registradas del paciente.
     *
     * @return string|null Alergias del paciente.
     */
    public function getAlergias(): ?string
    {
        return $this->alergias;
    }

    /* ----------------------------------------------------------------- */
    /* Modificadores con validación (reglas M-02 y EN-04).                 */
    /* ----------------------------------------------------------------- */

    /**
     * Asigna el identificador del paciente.
     *
     * @param int $idPaciente Identificador del paciente.
     * @return void
     * @throws \InvalidArgumentException Si el identificador no es un entero positivo.
     */
    public function setIdPaciente(int $idPaciente): void
    {
        if ($idPaciente <= 0) {
            throw new \InvalidArgumentException(
                'El identificador del paciente debe ser un número positivo.'
            );
        }

        $this->idPaciente = $idPaciente;
    }

    /**
     * Asigna el nombre del paciente.
     *
     * @param string $nombre Nombre del paciente.
     * @return void
     * @throws \InvalidArgumentException Si el nombre está vacío o excede la longitud máxima.
     */
    public function setNombre(string $nombre): void
    {
        $valor = trim($nombre);

        if ($valor === '') {
            throw new \InvalidArgumentException('El nombre del paciente es obligatorio.');
        }

        if (mb_strlen($valor) > self::LONGITUD_MAXIMA_NOMBRE) {
            throw new \InvalidArgumentException(
                'El nombre del paciente no puede superar los '
                . self::LONGITUD_MAXIMA_NOMBRE . ' caracteres.'
            );
        }

        $this->nombre = $valor;
    }

    /**
     * Asigna el apellido del paciente.
     *
     * @param string $apellido Apellido del paciente.
     * @return void
     * @throws \InvalidArgumentException Si el apellido está vacío o excede la longitud máxima.
     */
    public function setApellido(string $apellido): void
    {
        $valor = trim($apellido);

        if ($valor === '') {
            throw new \InvalidArgumentException('El apellido del paciente es obligatorio.');
        }

        if (mb_strlen($valor) > self::LONGITUD_MAXIMA_APELLIDO) {
            throw new \InvalidArgumentException(
                'El apellido del paciente no puede superar los '
                . self::LONGITUD_MAXIMA_APELLIDO . ' caracteres.'
            );
        }

        $this->apellido = $valor;
    }

    /**
     * Asigna el número de identificación del paciente.
     *
     * @param string $numeroIdentificacion Número de identificación.
     * @return void
     * @throws \InvalidArgumentException Si la identificación está vacía o es demasiado larga.
     */
    public function setNumeroIdentificacion(string $numeroIdentificacion): void
    {
        $valor = trim($numeroIdentificacion);

        if ($valor === '') {
            throw new \InvalidArgumentException(
                'El número de identificación del paciente es obligatorio.'
            );
        }

        if (mb_strlen($valor) > self::LONGITUD_MAXIMA_IDENTIFICACION) {
            throw new \InvalidArgumentException(
                'El número de identificación no puede superar los '
                . self::LONGITUD_MAXIMA_IDENTIFICACION . ' caracteres.'
            );
        }

        $this->numeroIdentificacion = $valor;
    }

    /**
     * Asigna la fecha de nacimiento del paciente.
     *
     * @param string|null $fechaNacimiento Fecha de nacimiento en formato `Y-m-d`.
     * @return void
     * @throws \InvalidArgumentException Si la fecha no existe o no tiene el formato esperado.
     */
    public function setFechaNacimiento(?string $fechaNacimiento): void
    {
        if ($fechaNacimiento === null) {
            $this->fechaNacimiento = null;

            return;
        }

        $valor = trim($fechaNacimiento);

        if ($valor === '') {
            $this->fechaNacimiento = null;

            return;
        }

        $fecha = \DateTimeImmutable::createFromFormat(self::FORMATO_FECHA, $valor);

        if ($fecha === false || $fecha->format(self::FORMATO_FECHA) !== $valor) {
            throw new \InvalidArgumentException(
                'La fecha de nacimiento debe tener el formato AAAA-MM-DD y ser una fecha existente.'
            );
        }

        $this->fechaNacimiento = $valor;
    }

    /**
     * Asigna el género del paciente.
     *
     * @param string|null $genero Género: `M`, `F` u `O`.
     * @return void
     * @throws \InvalidArgumentException Si el género no corresponde a un valor permitido.
     */
    public function setGenero(?string $genero): void
    {
        if ($genero === null) {
            $this->genero = null;

            return;
        }

        $valor = strtoupper(trim($genero));

        if ($valor === '') {
            $this->genero = null;

            return;
        }

        $permitidos = [self::GENERO_MASCULINO, self::GENERO_FEMENINO, self::GENERO_OTRO];

        if (!in_array($valor, $permitidos, true)) {
            throw new \InvalidArgumentException(
                'El género debe ser M (masculino), F (femenino) u O (otro).'
            );
        }

        $this->genero = $valor;
    }

    /**
     * Asigna la dirección de residencia del paciente.
     *
     * @param string|null $direccion Dirección de residencia.
     * @return void
     * @throws \InvalidArgumentException Si la dirección excede la longitud máxima.
     */
    public function setDireccion(?string $direccion): void
    {
        if ($direccion === null) {
            $this->direccion = null;

            return;
        }

        $valor = trim($direccion);

        if ($valor === '') {
            $this->direccion = null;

            return;
        }

        if (mb_strlen($valor) > self::LONGITUD_MAXIMA_DIRECCION) {
            throw new \InvalidArgumentException(
                'La dirección no puede superar los '
                . self::LONGITUD_MAXIMA_DIRECCION . ' caracteres.'
            );
        }

        $this->direccion = $valor;
    }

    /**
     * Asigna el teléfono de contacto del paciente.
     *
     * @param string|null $telefono Número de teléfono.
     * @return void
     * @throws \InvalidArgumentException Si el teléfono no cumple el formato admitido.
     */
    public function setTelefono(?string $telefono): void
    {
        if ($telefono === null) {
            $this->telefono = null;

            return;
        }

        $valor = trim($telefono);

        if ($valor === '') {
            $this->telefono = null;

            return;
        }

        if (preg_match(self::PATRON_TELEFONO, $valor) !== 1) {
            throw new \InvalidArgumentException(
                'El teléfono sólo admite números y los signos + - ( ) '
                . 'con una longitud entre 7 y 20 caracteres.'
            );
        }

        $this->telefono = $valor;
    }

    /**
     * Asigna el correo electrónico de contacto del paciente.
     *
     * @param string|null $email Correo electrónico.
     * @return void
     * @throws \InvalidArgumentException Si el correo no tiene un formato válido.
     */
    public function setEmail(?string $email): void
    {
        if ($email === null) {
            $this->email = null;

            return;
        }

        $valor = trim($email);

        if ($valor === '') {
            $this->email = null;

            return;
        }

        if (mb_strlen($valor) > self::LONGITUD_MAXIMA_EMAIL) {
            throw new \InvalidArgumentException(
                'El correo electrónico no puede superar los '
                . self::LONGITUD_MAXIMA_EMAIL . ' caracteres.'
            );
        }

        if (filter_var($valor, FILTER_VALIDATE_EMAIL) === false) {
            throw new \InvalidArgumentException(
                'El correo electrónico no tiene un formato válido.'
            );
        }

        $this->email = $valor;
    }

    /**
     * Asigna el grupo sanguíneo del paciente.
     *
     * @param string|null $tipoSangre Grupo sanguíneo.
     * @return void
     * @throws \InvalidArgumentException Si el grupo sanguíneo excede la longitud máxima.
     */
    public function setTipoSangre(?string $tipoSangre): void
    {
        if ($tipoSangre === null) {
            $this->tipoSangre = null;

            return;
        }

        $valor = strtoupper(trim($tipoSangre));

        if ($valor === '') {
            $this->tipoSangre = null;

            return;
        }

        if (mb_strlen($valor) > self::LONGITUD_MAXIMA_TIPO_SANGRE) {
            throw new \InvalidArgumentException(
                'El tipo de sangre no puede superar los '
                . self::LONGITUD_MAXIMA_TIPO_SANGRE . ' caracteres.'
            );
        }

        $this->tipoSangre = $valor;
    }

    /**
     * Asigna las alergias registradas del paciente.
     *
     * @param string|null $alergias Alergias del paciente.
     * @return void
     */
    public function setAlergias(?string $alergias): void
    {
        if ($alergias === null) {
            $this->alergias = null;

            return;
        }

        $valor = trim($alergias);

        $this->alergias = $valor === '' ? null : $valor;
    }

    /* ----------------------------------------------------------------- */
    /* Operaciones de persistencia (reglas M-07, B-06 y B-08).            */
    /* ----------------------------------------------------------------- */

    /**
     * Registra el paciente en la base de datos.
     *
     * @return bool `true` si el registro fue insertado.
     * @throws \PDOException Si la inserción falla; la transacción se revierte.
     */
    public function registrar(): bool
    {
        $baseDeDatos = BaseDeDatos::obtenerInstancia();
        $baseDeDatos->iniciarTransaccion();

        try {
            $filas = $baseDeDatos->ejecutar(
                $this->construirSqlInsercion(),
                $this->construirParametros()
            );
            $baseDeDatos->confirmarTransaccion();
        } catch (\Throwable $error) {
            $baseDeDatos->revertirTransaccion();

            throw $error;
        }

        if ($filas > 0) {
            $this->idPaciente = $baseDeDatos->ultimoId();
        }

        return $filas > 0;
    }

    /**
     * Busca un paciente por su identificador.
     *
     * El objeto se construye a través de los modificadores, de manera que la
     * validación del modelo es la única fuente de verdad (regla EN-04).
     *
     * @param int $idPaciente Identificador del paciente.
     * @return Paciente|null Paciente encontrado o `null` si no existe.
     * @throws \PDOException Si la consulta falla.
     * @throws \InvalidArgumentException Si un dato almacenado no cumple las reglas del modelo.
     */
    public static function buscarPorId(int $idPaciente): ?Paciente
    {
        if ($idPaciente <= 0) {
            return null;
        }

        $fila = BaseDeDatos::obtenerInstancia()->obtenerFila(
            self::construirSqlConsulta(),
            ['id' => $idPaciente]
        );

        if ($fila === null) {
            return null;
        }

        return self::construirDesdeFila($fila);
    }

    /**
     * Actualiza los datos del paciente en la base de datos.
     *
     * @return bool `true` si el registro fue actualizado.
     * @throws \InvalidArgumentException Si el paciente aún no ha sido registrado.
     * @throws \PDOException Si la actualización falla; la transacción se revierte.
     */
    public function actualizar(): bool
    {
        if ($this->idPaciente <= 0) {
            throw new \InvalidArgumentException(
                'El paciente no está registrado: no se puede actualizar.'
            );
        }

        $baseDeDatos = BaseDeDatos::obtenerInstancia();
        $baseDeDatos->iniciarTransaccion();

        try {
            $filas = $baseDeDatos->ejecutar(
                $this->construirSqlActualizacion(),
                array_merge($this->construirParametros(), ['id' => $this->idPaciente])
            );
            $baseDeDatos->confirmarTransaccion();
        } catch (\Throwable $error) {
            $baseDeDatos->revertirTransaccion();

            throw $error;
        }

        return $filas > 0;
    }

    /**
     * Elimina el paciente de la base de datos.
     *
     * @return bool `true` si el registro fue eliminado.
     * @throws \InvalidArgumentException Si el paciente aún no ha sido registrado.
     * @throws \PDOException Si la eliminación falla; la transacción se revierte.
     */
    public function eliminar(): bool
    {
        if ($this->idPaciente <= 0) {
            throw new \InvalidArgumentException(
                'El paciente no está registrado: no se puede eliminar.'
            );
        }

        $baseDeDatos = BaseDeDatos::obtenerInstancia();
        $baseDeDatos->iniciarTransaccion();

        try {
            $filas = $baseDeDatos->ejecutar(
                $this->construirSqlEliminacion(),
                ['id' => $this->idPaciente]
            );
            $baseDeDatos->confirmarTransaccion();
        } catch (\Throwable $error) {
            $baseDeDatos->revertirTransaccion();

            throw $error;
        }

        return $filas > 0;
    }

    /**
     * Lista todos los pacientes registrados.
     *
     * @return array<int, array<string, mixed>> Filas de la tabla `PACIENTE`.
     * @throws \PDOException Si la consulta falla.
     */
    public static function listar(): array
    {
        return BaseDeDatos::obtenerInstancia()->consultar(self::construirSqlListado());
    }

    /**
     * Verifica si un número de identificación ya está registrado.
     *
     * @param string $numeroIdentificacion Número de identificación buscado.
     * @param int|null $excluirId Identificador que se excluye de la búsqueda,
     *                              para no comparar el paciente con sí mismo.
     * @return bool `true` si el número de identificación ya existe.
     * @throws \PDOException Si la consulta falla.
     */
    public static function existeNumeroIdentificacion(
        string $numeroIdentificacion,
        ?int $excluirId = null
    ): bool
    {
        $numero = trim($numeroIdentificacion);

        if ($numero === '') {
            return false;
        }

        $sql = 'SELECT COUNT(*) AS total FROM PACIENTE WHERE numero_identificacion = :numero';
        $parametros = ['numero' => $numero];

        if ($excluirId !== null) {
            $sql .= ' AND id_paciente <> :excluir';
            $parametros['excluir'] = $excluirId;
        }

        $fila = BaseDeDatos::obtenerInstancia()->obtenerFila($sql, $parametros);

        return $fila !== null && (int) $fila['total'] > 0;
    }

    /* ----------------------------------------------------------------- */
    /* Métodos privados (reglas D-06 y B-09).                             */
    /* ----------------------------------------------------------------- */

    /**
     * Construye la sentencia SQL del alta de un paciente.
     *
     * @return string Sentencia parametrizada de inserción.
     */
    private function construirSqlInsercion(): string
    {
        return 'INSERT INTO PACIENTE (nombre, apellido, numero_identificacion, fecha_nacimiento, '
            . 'genero, direccion, telefono, email, tipo_sangre, alergias) '
            . 'VALUES (:nombre, :apellido, :numero_identificacion, :fecha_nacimiento, '
            . ':genero, :direccion, :telefono, :email, :tipo_sangre, :alergias)';
    }

    /**
     * Construye la sentencia SQL de actualización de un paciente.
     *
     * @return string Sentencia parametrizada de actualización.
     */
    private function construirSqlActualizacion(): string
    {
        return 'UPDATE PACIENTE SET nombre = :nombre, apellido = :apellido, '
            . 'numero_identificacion = :numero_identificacion, '
            . 'fecha_nacimiento = :fecha_nacimiento, genero = :genero, direccion = :direccion, '
            . 'telefono = :telefono, email = :email, tipo_sangre = :tipo_sangre, '
            . 'alergias = :alergias WHERE id_paciente = :id';
    }

    /**
     * Construye la sentencia SQL de eliminación de un paciente.
     *
     * @return string Sentencia parametrizada de eliminación.
     */
    private function construirSqlEliminacion(): string
    {
        return 'DELETE FROM PACIENTE WHERE id_paciente = :id';
    }

    /**
     * Construye la sentencia SQL de consulta de un paciente por identificador.
     *
     * @return string Sentencia parametrizada de consulta.
     */
    private static function construirSqlConsulta(): string
    {
        return 'SELECT id_paciente, nombre, apellido, numero_identificacion, fecha_nacimiento, '
            . 'genero, direccion, telefono, email, tipo_sangre, alergias '
            . 'FROM PACIENTE WHERE id_paciente = :id';
    }

    /**
     * Construye la sentencia SQL del listado de pacientes.
     *
     * @return string Sentencia parametrizada de listado.
     */
    private static function construirSqlListado(): string
    {
        return 'SELECT id_paciente, nombre, apellido, numero_identificacion, fecha_nacimiento, '
            . 'genero, direccion, telefono, email, tipo_sangre, alergias '
            . 'FROM PACIENTE ORDER BY apellido ASC, nombre ASC';
    }

    /**
     * Devuelve los valores del paciente en el orden de los marcadores SQL.
     *
     * @return array<string, mixed> Valores vinculados a las sentencias.
     */
    private function construirParametros(): array
    {
        return [
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'numero_identificacion' => $this->numeroIdentificacion,
            'fecha_nacimiento' => $this->fechaNacimiento,
            'genero' => $this->genero,
            'direccion' => $this->direccion,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'tipo_sangre' => $this->tipoSangre,
            'alergias' => $this->alergias,
        ];
    }

    /**
     * Construye un paciente a partir de una fila de la base de datos.
     *
     * @param array<string, mixed> $fila Fila devuelta por la consulta.
     * @return Paciente Paciente construido a través de los modificadores.
     * @throws \InvalidArgumentException Si un dato almacenado no cumple las reglas del modelo.
     */
    private static function construirDesdeFila(array $fila): Paciente
    {
        $paciente = new Paciente(
            (string) $fila['nombre'],
            (string) $fila['apellido'],
            (string) $fila['numero_identificacion'],
            self::textoONulo($fila['fecha_nacimiento']),
            self::textoONulo($fila['genero']),
            self::textoONulo($fila['direccion']),
            self::textoONulo($fila['telefono']),
            self::textoONulo($fila['email']),
            self::textoONulo($fila['tipo_sangre']),
            self::textoONulo($fila['alergias'])
        );

        $paciente->setIdPaciente((int) $fila['id_paciente']);

        return $paciente;
    }

    /**
     * Convierte un valor de la base de datos en texto o `null`.
     *
     * @param mixed $valor Valor almacenado.
     * @return string|null Texto limpio o `null` si el valor está vacío.
     */
    private static function textoONulo(mixed $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        $texto = trim((string) $valor);

        return $texto === '' ? null : $texto;
    }
}
