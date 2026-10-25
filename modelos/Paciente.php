<?php

declare(strict_types=1);

namespace App\Modelos;

use App\Infraestructura\BaseDeDatos;

/**
 * Modelo del paciente del centro medico.
 *
 * Concentra los datos del paciente y las operaciones de persistencia sobre la
 * tabla `pacientes` de la base de datos `centromedico`. Todas las reglas de
 * validacion viven en los modificadores (`setX()`), de modo que ningun objeto
 * puede quedar en un estado invalido (regla EN-04 del estandar de codificacion).
 */
final class Paciente
{
    /* ----------------------------------------------------------------- */
    /* Constantes de clase (regla V-09).                                 */
    /* ----------------------------------------------------------------- */

    public const SEXO_MASCULINO = 'M';
    public const SEXO_FEMENINO = 'F';
    public const SEXO_OTRO = 'O';

    public const LONGITUD_MAXIMA_NOMBRES = 100;
    public const LONGITUD_MAXIMA_APELLIDOS = 100;
    public const LONGITUD_MAXIMA_IDENTIFICACION = 20;

    public const FORMATO_FECHA = 'Y-m-d';

    /* ----------------------------------------------------------------- */
    /* Propiedades (reglas P-01, P-02 y P-03).                           */
    /* ----------------------------------------------------------------- */

    /**
     * Identificador del paciente; cero mientras el registro no esta persistido.
     */
    private int $idPaciente = 0;

    private string $identificacion = '';
    private string $nombres = '';
    private string $apellidos = '';
    private ?\DateTimeImmutable $fechaNacimiento = null;
    private ?string $sexo = null;
    private ?int $usuariosIdUsuario = null;

    /* ----------------------------------------------------------------- */
    /* Constructor (reglas M-05 y D-03).                                 */
    /* ----------------------------------------------------------------- */

    /**
     * Construye un paciente con sus datos personales.
     *
     * La asignacion se delega en los modificadores para no duplicar las
     * reglas de validacion: si un dato es invalido, la excepcion se produce
     * durante la construccion del objeto (regla D-03).
     *
     * @param string $identificacion Numero de identificacion del paciente.
     * @param string $nombres Nombres del paciente.
     * @param string $apellidos Apellidos del paciente.
     * @param \DateTimeImmutable|null $fechaNacimiento Fecha de nacimiento.
     * @param string|null $sexo Sexo: `M`, `F` u `O`.
     * @param int|null $usuariosIdUsuario Identificador del usuario asociado.
     * @throws \InvalidArgumentException Si algun dato no cumple las reglas del modelo.
     */
    public function __construct(
        string $identificacion,
        string $nombres,
        string $apellidos,
        ?\DateTimeImmutable $fechaNacimiento = null,
        ?string $sexo = null,
        ?int $usuariosIdUsuario = null
    ) {
        $this->setIdentificacion($identificacion);
        $this->setNombres($nombres);
        $this->setApellidos($apellidos);
        $this->setFechaNacimiento($fechaNacimiento);
        $this->setSexo($sexo);
        $this->setUsuariosIdUsuario($usuariosIdUsuario);
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
     * Obtiene el numero de identificacion del paciente.
     *
     * @return string Numero de identificacion.
     */
    public function getIdentificacion(): string
    {
        return $this->identificacion;
    }

    /**
     * Obtiene los nombres del paciente.
     *
     * @return string Nombres del paciente.
     */
    public function getNombres(): string
    {
        return $this->nombres;
    }

    /**
     * Obtiene los apellidos del paciente.
     *
     * @return string Apellidos del paciente.
     */
    public function getApellidos(): string
    {
        return $this->apellidos;
    }

    /**
     * Obtiene la fecha de nacimiento del paciente.
     *
     * @return \DateTimeImmutable|null Fecha de nacimiento.
     */
    public function getFechaNacimiento(): ?\DateTimeImmutable
    {
        return $this->fechaNacimiento;
    }

    /**
     * Obtiene el sexo del paciente.
     *
     * @return string|null Sexo: `M`, `F` u `O`.
     */
    public function getSexo(): ?string
    {
        return $this->sexo;
    }

    /**
     * Obtiene el identificador del usuario asociado.
     *
     * @return int|null Identificador del usuario asociado.
     */
    public function getUsuariosIdUsuario(): ?int
    {
        return $this->usuariosIdUsuario;
    }

    /* ----------------------------------------------------------------- */
    /* Modificadores con validacion (reglas M-02 y EN-04).                 */
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
                'El identificador del paciente debe ser un numero positivo.'
            );
        }

        $this->idPaciente = $idPaciente;
    }

    /**
     * Asigna el numero de identificacion del paciente.
     *
     * @param string $identificacion Numero de identificacion.
     * @return void
     * @throws \InvalidArgumentException Si la identificacion esta vacia o es demasiado larga.
     */
    public function setIdentificacion(string $identificacion): void
    {
        $valor = trim($identificacion);

        if ($valor === '') {
            throw new \InvalidArgumentException(
                'El numero de identificacion del paciente es obligatorio.'
            );
        }

        if (mb_strlen($valor) > self::LONGITUD_MAXIMA_IDENTIFICACION) {
            throw new \InvalidArgumentException(
                'El numero de identificacion no puede superar los '
                . self::LONGITUD_MAXIMA_IDENTIFICACION . ' caracteres.'
            );
        }

        $this->identificacion = $valor;
    }

    /**
     * Asigna los nombres del paciente.
     *
     * @param string $nombres Nombres del paciente.
     * @return void
     * @throws \InvalidArgumentException Si los nombres estan vacios o exceden la longitud maxima.
     */
    public function setNombres(string $nombres): void
    {
        $valor = trim($nombres);

        if ($valor === '') {
            throw new \InvalidArgumentException('Los nombres del paciente son obligatorios.');
        }

        if (mb_strlen($valor) > self::LONGITUD_MAXIMA_NOMBRES) {
            throw new \InvalidArgumentException(
                'Los nombres del paciente no pueden superar los '
                . self::LONGITUD_MAXIMA_NOMBRES . ' caracteres.'
            );
        }

        $this->nombres = $valor;
    }

    /**
     * Asigna los apellidos del paciente.
     *
     * @param string $apellidos Apellidos del paciente.
     * @return void
     * @throws \InvalidArgumentException Si los apellidos estan vacios o exceden la longitud maxima.
     */
    public function setApellidos(string $apellidos): void
    {
        $valor = trim($apellidos);

        if ($valor === '') {
            throw new \InvalidArgumentException('Los apellidos del paciente son obligatorios.');
        }

        if (mb_strlen($valor) > self::LONGITUD_MAXIMA_APELLIDOS) {
            throw new \InvalidArgumentException(
                'Los apellidos del paciente no pueden superar los '
                . self::LONGITUD_MAXIMA_APELLIDOS . ' caracteres.'
            );
        }

        $this->apellidos = $valor;
    }

    /**
     * Asigna la fecha de nacimiento del paciente.
     *
     * @param \DateTimeImmutable|null $fechaNacimiento Fecha de nacimiento.
     * @return void
     */
    public function setFechaNacimiento(?\DateTimeImmutable $fechaNacimiento): void
    {
        $this->fechaNacimiento = $fechaNacimiento;
    }

    /**
     * Asigna el sexo del paciente.
     *
     * @param string|null $sexo Sexo: `M`, `F` u `O`.
     * @return void
     * @throws \InvalidArgumentException Si el sexo no corresponde a un valor permitido.
     */
    public function setSexo(?string $sexo): void
    {
        if ($sexo === null) {
            $this->sexo = null;

            return;
        }

        $valor = strtoupper(trim($sexo));

        if ($valor === '') {
            $this->sexo = null;

            return;
        }

        $permitidos = [self::SEXO_MASCULINO, self::SEXO_FEMENINO, self::SEXO_OTRO];

        if (!in_array($valor, $permitidos, true)) {
            throw new \InvalidArgumentException(
                'El sexo debe ser M (masculino), F (femenino) u O (otro).'
            );
        }

        $this->sexo = $valor;
    }

    /**
     * Asigna el identificador del usuario asociado.
     *
     * @param int|null $usuariosIdUsuario Identificador del usuario asociado.
     * @return void
     */
    public function setUsuariosIdUsuario(?int $usuariosIdUsuario): void
    {
        $this->usuariosIdUsuario = $usuariosIdUsuario;
    }

    /* ----------------------------------------------------------------- */
    /* Operaciones de persistencia (reglas M-07, B-06 y B-08).            */
    /* ----------------------------------------------------------------- */

    /**
     * Registra el paciente en la base de datos.
     *
     * @return bool `true` si el registro fue insertado.
     * @throws \PDOException Si la insercion falla; la transaccion se revierte.
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
     * El objeto se construye a traves de los modificadores, de manera que la
     * validacion del modelo es la unica fuente de verdad (regla EN-04).
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
     * @throws \InvalidArgumentException Si el paciente aun no ha sido registrado.
     * @throws \PDOException Si la actualizacion falla; la transaccion se revierte.
     */
    public function actualizar(): bool
    {
        if ($this->idPaciente <= 0) {
            throw new \InvalidArgumentException(
                'El paciente no esta registrado: no se puede actualizar.'
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
     * @throws \InvalidArgumentException Si el paciente aun no ha sido registrado.
     * @throws \PDOException Si la eliminacion falla; la transaccion se revierte.
     */
    public function eliminar(): bool
    {
        if ($this->idPaciente <= 0) {
            throw new \InvalidArgumentException(
                'El paciente no esta registrado: no se puede eliminar.'
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
     * @return array<int, array<string, mixed>> Filas de la tabla `pacientes`.
     * @throws \PDOException Si la consulta falla.
     */
    public static function listar(): array
    {
        return BaseDeDatos::obtenerInstancia()->consultar(self::construirSqlListado());
    }

    /**
     * Verifica si un numero de identificacion ya esta registrado.
     *
     * @param string $identificacion Numero de identificacion buscado.
     * @param int|null $excluirId Identificador que se excluye de la busqueda,
     *                              para no comparar el paciente con si mismo.
     * @return bool `true` si el numero de identificacion ya existe.
     * @throws \PDOException Si la consulta falla.
     */
    public static function existeIdentificacion(
        string $identificacion,
        ?int $excluirId = null
    ): bool {
        $numero = trim($identificacion);

        if ($numero === '') {
            return false;
        }

        $sql = 'SELECT COUNT(*) AS total FROM pacientes WHERE pacIdentificacion = :numero';
        $parametros = ['numero' => $numero];

        if ($excluirId !== null) {
            $sql .= ' AND idPaciente <> :excluir';
            $parametros['excluir'] = $excluirId;
        }

        $fila = BaseDeDatos::obtenerInstancia()->obtenerFila($sql, $parametros);

        return $fila !== null && (int) $fila['total'] > 0;
    }

    /* ----------------------------------------------------------------- */
    /* Metodos privados (reglas D-06 y B-09).                             */
    /* ----------------------------------------------------------------- */

    /**
     * Construye la sentencia SQL del alta de un paciente.
     *
     * @return string Sentencia parametrizada de insercion.
     */
    private function construirSqlInsercion(): string
    {
        return 'INSERT INTO pacientes (pacIdentificacion, pacNombres, pacApellidos, '
            . 'pacFechaNacimiento, pacSexo, usuarios_idUsuario) '
            . 'VALUES (:identificacion, :nombres, :apellidos, :fecha_nacimiento, '
            . ':sexo, :usuarios_id_usuario)';
    }

    /**
     * Construye la sentencia SQL de actualizacion de un paciente.
     *
     * @return string Sentencia parametrizada de actualizacion.
     */
    private function construirSqlActualizacion(): string
    {
        return 'UPDATE pacientes SET pacIdentificacion = :identificacion, '
            . 'pacNombres = :nombres, pacApellidos = :apellidos, '
            . 'pacFechaNacimiento = :fecha_nacimiento, pacSexo = :sexo, '
            . 'usuarios_idUsuario = :usuarios_id_usuario WHERE idPaciente = :id';
    }

    /**
     * Construye la sentencia SQL de eliminacion de un paciente.
     *
     * @return string Sentencia parametrizada de eliminacion.
     */
    private function construirSqlEliminacion(): string
    {
        return 'DELETE FROM pacientes WHERE idPaciente = :id';
    }

    /**
     * Construye la sentencia SQL de consulta de un paciente por identificador.
     *
     * @return string Sentencia parametrizada de consulta.
     */
    private static function construirSqlConsulta(): string
    {
        return 'SELECT idPaciente, pacIdentificacion, pacNombres, pacApellidos, '
            . 'pacFechaNacimiento, pacSexo, usuarios_idUsuario '
            . 'FROM pacientes WHERE idPaciente = :id';
    }

    /**
     * Construye la sentencia SQL del listado de pacientes.
     *
     * @return string Sentencia parametrizada de listado.
     */
    private static function construirSqlListado(): string
    {
        return 'SELECT idPaciente, pacIdentificacion, pacNombres, pacApellidos, '
            . 'pacFechaNacimiento, pacSexo, usuarios_idUsuario '
            . 'FROM pacientes ORDER BY pacApellidos ASC, pacNombres ASC';
    }

    /**
     * Devuelve los valores del paciente en el orden de los marcadores SQL.
     *
     * @return array<string, mixed> Valores vinculados a las sentencias.
     */
    private function construirParametros(): array
    {
        return [
            'identificacion' => $this->identificacion,
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'fecha_nacimiento' => $this->fechaNacimiento?->format(self::FORMATO_FECHA),
            'sexo' => $this->sexo,
            'usuarios_id_usuario' => $this->usuariosIdUsuario,
        ];
    }

    /**
     * Construye un paciente a partir de una fila de la base de datos.
     *
     * @param array<string, mixed> $fila Fila devuelta por la consulta.
     * @return Paciente Paciente construido a traves de los modificadores.
     * @throws \InvalidArgumentException Si un dato almacenado no cumple las reglas del modelo.
     */
    private static function construirDesdeFila(array $fila): Paciente
    {
        $fechaNacimiento = self::fechaONulo($fila['pacFechaNacimiento']);

        $paciente = new Paciente(
            (string) $fila['pacIdentificacion'],
            (string) $fila['pacNombres'],
            (string) $fila['pacApellidos'],
            $fechaNacimiento,
            self::textoONulo($fila['pacSexo']),
            $fila['usuarios_idUsuario'] !== null ? (int) $fila['usuarios_idUsuario'] : null
        );

        $paciente->setIdPaciente((int) $fila['idPaciente']);

        return $paciente;
    }

    /**
     * Convierte un valor de la base de datos en texto o `null`.
     *
     * @param mixed $valor Valor almacenado.
     * @return string|null Texto limpio o `null` si el valor esta vacio.
     */
    private static function textoONulo(mixed $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        $texto = trim((string) $valor);

        return $texto === '' ? null : $texto;
    }

    /**
     * Convierte un valor de fecha de la base de datos en DateTimeImmutable o `null`.
     *
     * @param mixed $valor Valor almacenado.
     * @return \DateTimeImmutable|null Fecha o `null` si el valor esta vacio.
     */
    private static function fechaONulo(mixed $valor): ?\DateTimeImmutable
    {
        $texto = self::textoONulo($valor);

        if ($texto === null) {
            return null;
        }

        $fecha = \DateTimeImmutable::createFromFormat(self::FORMATO_FECHA, $texto);

        return $fecha instanceof \DateTimeImmutable ? $fecha : null;
    }
}
