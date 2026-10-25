<?php

declare(strict_types=1);

namespace App\Modelos;

use App\Infraestructura\BaseDeDatos;

/**
 * Modelo del medico del centro medico.
 *
 * Concentra los datos del medico y las operaciones de persistencia sobre la
 * tabla `medicos` de la base de datos `centromedico`.
 */
final class Medico
{
    /* ----------------------------------------------------------------- */
    /* Constantes de clase (regla V-09).                                 */
    /* ----------------------------------------------------------------- */

    public const LONGITUD_MAXIMA_NOMBRES = 100;
    public const LONGITUD_MAXIMA_APELLIDOS = 100;
    public const LONGITUD_MAXIMA_IDENTIFICACION = 20;
    public const LONGITUD_MAXIMA_ESPECIALIDAD = 100;
    public const LONGITUD_MAXIMA_TELEFONO = 20;
    public const LONGITUD_MAXIMA_CORREO = 100;

    public const PATRON_TELEFONO = '/^[0-9+\-\s()]{7,20}$/';

    /* ----------------------------------------------------------------- */
    /* Propiedades (reglas P-01, P-02 y P-03).                           */
    /* ----------------------------------------------------------------- */

    /**
     * Identificador del medico; cero mientras el registro no esta persistido.
     */
    private int $idMedico = 0;

    private string $identificacion = '';
    private string $nombres = '';
    private string $apellidos = '';
    private string $especialidad = '';
    private ?string $telefono = null;
    private ?string $correo = null;
    private ?int $usuariosIdUsuario = null;

    /* ----------------------------------------------------------------- */
    /* Constructor (reglas M-05 y D-03).                                 */
    /* ----------------------------------------------------------------- */

    /**
     * Construye un medico con sus datos.
     *
     * La asignacion se delega en los modificadores para no duplicar las
     * reglas de validacion (regla D-03).
     *
     * @param string $identificacion Numero de identificacion del medico.
     * @param string $nombres Nombres del medico.
     * @param string $apellidos Apellidos del medico.
     * @param string $especialidad Especialidad del medico.
     * @param string|null $telefono Telefono de contacto.
     * @param string|null $correo Correo electronico de contacto.
     * @param int|null $usuariosIdUsuario Identificador del usuario asociado.
     * @throws \InvalidArgumentException Si algun dato no cumple las reglas del modelo.
     */
    public function __construct(
        string $identificacion,
        string $nombres,
        string $apellidos,
        string $especialidad,
        ?string $telefono = null,
        ?string $correo = null,
        ?int $usuariosIdUsuario = null
    ) {
        $this->setIdentificacion($identificacion);
        $this->setNombres($nombres);
        $this->setApellidos($apellidos);
        $this->setEspecialidad($especialidad);
        $this->setTelefono($telefono);
        $this->setCorreo($correo);
        $this->setUsuariosIdUsuario($usuariosIdUsuario);
    }

    /* ----------------------------------------------------------------- */
    /* Consultores (regla M-02).                                          */
    /* ----------------------------------------------------------------- */

    /**
     * Obtiene el identificador del medico.
     *
     * @return int Identificador del medico.
     */
    public function getIdMedico(): int
    {
        return $this->idMedico;
    }

    /**
     * Obtiene el numero de identificacion del medico.
     *
     * @return string Numero de identificacion.
     */
    public function getIdentificacion(): string
    {
        return $this->identificacion;
    }

    /**
     * Obtiene los nombres del medico.
     *
     * @return string Nombres del medico.
     */
    public function getNombres(): string
    {
        return $this->nombres;
    }

    /**
     * Obtiene los apellidos del medico.
     *
     * @return string Apellidos del medico.
     */
    public function getApellidos(): string
    {
        return $this->apellidos;
    }

    /**
     * Obtiene la especialidad del medico.
     *
     * @return string Especialidad del medico.
     */
    public function getEspecialidad(): string
    {
        return $this->especialidad;
    }

    /**
     * Obtiene el telefono de contacto del medico.
     *
     * @return string|null Telefono de contacto.
     */
    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    /**
     * Obtiene el correo electronico de contacto del medico.
     *
     * @return string|null Correo electronico.
     */
    public function getCorreo(): ?string
    {
        return $this->correo;
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
     * Asigna el identificador del medico.
     *
     * @param int $idMedico Identificador del medico.
     * @return void
     * @throws \InvalidArgumentException Si el identificador no es un entero positivo.
     */
    public function setIdMedico(int $idMedico): void
    {
        if ($idMedico <= 0) {
            throw new \InvalidArgumentException(
                'El identificador del medico debe ser un numero positivo.'
            );
        }

        $this->idMedico = $idMedico;
    }

    /**
     * Asigna el numero de identificacion del medico.
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
                'El numero de identificacion del medico es obligatorio.'
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
     * Asigna los nombres del medico.
     *
     * @param string $nombres Nombres del medico.
     * @return void
     * @throws \InvalidArgumentException Si los nombres estan vacios o exceden la longitud maxima.
     */
    public function setNombres(string $nombres): void
    {
        $valor = trim($nombres);

        if ($valor === '') {
            throw new \InvalidArgumentException('Los nombres del medico son obligatorios.');
        }

        if (mb_strlen($valor) > self::LONGITUD_MAXIMA_NOMBRES) {
            throw new \InvalidArgumentException(
                'Los nombres del medico no pueden superar los '
                . self::LONGITUD_MAXIMA_NOMBRES . ' caracteres.'
            );
        }

        $this->nombres = $valor;
    }

    /**
     * Asigna los apellidos del medico.
     *
     * @param string $apellidos Apellidos del medico.
     * @return void
     * @throws \InvalidArgumentException Si los apellidos estan vacios o exceden la longitud maxima.
     */
    public function setApellidos(string $apellidos): void
    {
        $valor = trim($apellidos);

        if ($valor === '') {
            throw new \InvalidArgumentException('Los apellidos del medico son obligatorios.');
        }

        if (mb_strlen($valor) > self::LONGITUD_MAXIMA_APELLIDOS) {
            throw new \InvalidArgumentException(
                'Los apellidos del medico no pueden superar los '
                . self::LONGITUD_MAXIMA_APELLIDOS . ' caracteres.'
            );
        }

        $this->apellidos = $valor;
    }

    /**
     * Asigna la especialidad del medico.
     *
     * @param string $especialidad Especialidad del medico.
     * @return void
     * @throws \InvalidArgumentException Si la especialidad esta vacia.
     */
    public function setEspecialidad(string $especialidad): void
    {
        $valor = trim($especialidad);

        if ($valor === '') {
            throw new \InvalidArgumentException('La especialidad del medico es obligatoria.');
        }

        if (mb_strlen($valor) > self::LONGITUD_MAXIMA_ESPECIALIDAD) {
            throw new \InvalidArgumentException(
                'La especialidad no puede superar los '
                . self::LONGITUD_MAXIMA_ESPECIALIDAD . ' caracteres.'
            );
        }

        $this->especialidad = $valor;
    }

    /**
     * Asigna el telefono de contacto del medico.
     *
     * @param string|null $telefono Telefono de contacto.
     * @return void
     * @throws \InvalidArgumentException Si el telefono no cumple el formato admitido.
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

        if (mb_strlen($valor) > self::LONGITUD_MAXIMA_TELEFONO) {
            throw new \InvalidArgumentException(
                'El telefono no puede superar los '
                . self::LONGITUD_MAXIMA_TELEFONO . ' caracteres.'
            );
        }

        if (preg_match(self::PATRON_TELEFONO, $valor) !== 1) {
            throw new \InvalidArgumentException(
                'El telefono solo admite numeros y los signos + - ( ) con una longitud '
                . 'entre 7 y 20 caracteres.'
            );
        }

        $this->telefono = $valor;
    }

    /**
     * Asigna el correo electronico de contacto del medico.
     *
     * @param string|null $correo Correo electronico.
     * @return void
     * @throws \InvalidArgumentException Si el correo no tiene un formato valido.
     */
    public function setCorreo(?string $correo): void
    {
        if ($correo === null) {
            $this->correo = null;

            return;
        }

        $valor = trim($correo);

        if ($valor === '') {
            $this->correo = null;

            return;
        }

        if (mb_strlen($valor) > self::LONGITUD_MAXIMA_CORREO) {
            throw new \InvalidArgumentException(
                'El correo electronico no puede superar los '
                . self::LONGITUD_MAXIMA_CORREO . ' caracteres.'
            );
        }

        if (filter_var($valor, FILTER_VALIDATE_EMAIL) === false) {
            throw new \InvalidArgumentException(
                'El correo electronico no tiene un formato valido.'
            );
        }

        $this->correo = $valor;
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
     * Registra el medico en la base de datos.
     *
     * @return bool `true` si el registro fue insertado.
     * @throws \PDOException Si la insercion falla; la transaccion se revierte.
     */
    public function agregarMedico(): bool
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
            $this->idMedico = $baseDeDatos->ultimoId();
        }

        return $filas > 0;
    }

    /**
     * Busca un medico por su numero de identificacion.
     *
     * @param string $identificacion Numero de identificacion del medico.
     * @return array<string, mixed>|null Medico encontrado o `null` si no existe.
     * @throws \PDOException Si la consulta falla.
     */
    public static function consultarMedico(string $identificacion): ?array
    {
        $numero = trim($identificacion);

        if ($numero === '') {
            return null;
        }

        return BaseDeDatos::obtenerInstancia()->obtenerFila(
            self::construirSqlConsultaPorIdentificacion(),
            ['identificacion' => $numero]
        );
    }

    /**
     * Busca un medico por su identificador.
     *
     * @param int $idMedico Identificador del medico.
     * @return Medico|null Medico encontrado o `null` si no existe.
     * @throws \PDOException Si la consulta falla.
     * @throws \InvalidArgumentException Si un dato almacenado no cumple las reglas del modelo.
     */
    public static function buscarPorId(int $idMedico): ?Medico
    {
        if ($idMedico <= 0) {
            return null;
        }

        $fila = BaseDeDatos::obtenerInstancia()->obtenerFila(
            self::construirSqlConsultaPorId(),
            ['id' => $idMedico]
        );

        if ($fila === null) {
            return null;
        }

        return self::construirDesdeFila($fila);
    }

    /**
     * Actualiza los datos del medico en la base de datos.
     *
     * @param int $idMedico Identificador del medico a actualizar.
     * @return bool `true` si el registro fue actualizado.
     * @throws \InvalidArgumentException Si el medico aun no ha sido registrado.
     * @throws \PDOException Si la actualizacion falla; la transaccion se revierte.
     */
    public function actualizarMedico(int $idMedico): bool
    {
        if ($idMedico <= 0) {
            throw new \InvalidArgumentException(
                'El medico no esta registrado: no se puede actualizar.'
            );
        }

        $baseDeDatos = BaseDeDatos::obtenerInstancia();
        $baseDeDatos->iniciarTransaccion();

        try {
            $filas = $baseDeDatos->ejecutar(
                $this->construirSqlActualizacion(),
                array_merge($this->construirParametros(), ['id' => $idMedico])
            );
            $baseDeDatos->confirmarTransaccion();
        } catch (\Throwable $error) {
            $baseDeDatos->revertirTransaccion();

            throw $error;
        }

        if ($filas > 0) {
            $this->idMedico = $idMedico;
        }

        return $filas > 0;
    }

    /**
     * Elimina el medico de la base de datos.
     *
     * @param int $idMedico Identificador del medico a eliminar.
     * @return bool `true` si el registro fue eliminado.
     * @throws \InvalidArgumentException Si el identificador no es valido.
     * @throws \PDOException Si la eliminacion falla; la transaccion se revierte.
     */
    public static function eliminarMedico(int $idMedico): bool
    {
        if ($idMedico <= 0) {
            throw new \InvalidArgumentException(
                'El medico no esta registrado: no se puede eliminar.'
            );
        }

        $baseDeDatos = BaseDeDatos::obtenerInstancia();
        $baseDeDatos->iniciarTransaccion();

        try {
            $filas = $baseDeDatos->ejecutar(
                self::construirSqlEliminacion(),
                ['id' => $idMedico]
            );
            $baseDeDatos->confirmarTransaccion();
        } catch (\Throwable $error) {
            $baseDeDatos->revertirTransaccion();

            throw $error;
        }

        return $filas > 0;
    }

    /**
     * Lista todos los medicos registrados.
     *
     * @return array<int, array<string, mixed>> Filas de la tabla `medicos`.
     * @throws \PDOException Si la consulta falla.
     */
    public static function listarMedicos(): array
    {
        return BaseDeDatos::obtenerInstancia()->consultar(self::construirSqlListado());
    }

    /**
     * Verifica si un numero de identificacion ya esta registrado.
     *
     * @param string $identificacion Numero de identificacion buscado.
     * @param int|null $excluirId Identificador que se excluye de la busqueda.
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

        $sql = 'SELECT COUNT(*) AS total FROM medicos WHERE medIdentificacion = :identificacion';
        $parametros = ['identificacion' => $numero];

        if ($excluirId !== null) {
            $sql .= ' AND idMedico <> :excluir';
            $parametros['excluir'] = $excluirId;
        }

        $fila = BaseDeDatos::obtenerInstancia()->obtenerFila($sql, $parametros);

        return $fila !== null && (int) $fila['total'] > 0;
    }

    /* ----------------------------------------------------------------- */
    /* Metodos privados (reglas D-06 y B-09).                             */
    /* ----------------------------------------------------------------- */

    /**
     * Construye la sentencia SQL del alta de un medico.
     *
     * @return string Sentencia parametrizada de insercion.
     */
    private function construirSqlInsercion(): string
    {
        return 'INSERT INTO medicos (medIdentificacion, medNombres, medApellidos, '
            . 'medEspecialidad, medTelefono, medCorreo, usuarios_idUsuario) '
            . 'VALUES (:identificacion, :nombres, :apellidos, :especialidad, '
            . ':telefono, :correo, :usuarios_id_usuario)';
    }

    /**
     * Construye la sentencia SQL de actualizacion de un medico.
     *
     * @return string Sentencia parametrizada de actualizacion.
     */
    private function construirSqlActualizacion(): string
    {
        return 'UPDATE medicos SET medIdentificacion = :identificacion, '
            . 'medNombres = :nombres, medApellidos = :apellidos, '
            . 'medEspecialidad = :especialidad, medTelefono = :telefono, '
            . 'medCorreo = :correo, usuarios_idUsuario = :usuarios_id_usuario '
            . 'WHERE idMedico = :id';
    }

    /**
     * Construye la sentencia SQL de eliminacion de un medico.
     *
     * @return string Sentencia parametrizada de eliminacion.
     */
    private static function construirSqlEliminacion(): string
    {
        return 'DELETE FROM medicos WHERE idMedico = :id';
    }

    /**
     * Construye la sentencia SQL de consulta de un medico por identificacion.
     *
     * @return string Sentencia parametrizada de consulta.
     */
    private static function construirSqlConsultaPorIdentificacion(): string
    {
        return 'SELECT idMedico, medIdentificacion, medNombres, medApellidos, '
            . 'medEspecialidad, medTelefono, medCorreo, usuarios_idUsuario '
            . 'FROM medicos WHERE medIdentificacion = :identificacion';
    }

    /**
     * Construye la sentencia SQL de consulta de un medico por identificador.
     *
     * @return string Sentencia parametrizada de consulta.
     */
    private static function construirSqlConsultaPorId(): string
    {
        return 'SELECT idMedico, medIdentificacion, medNombres, medApellidos, '
            . 'medEspecialidad, medTelefono, medCorreo, usuarios_idUsuario '
            . 'FROM medicos WHERE idMedico = :id';
    }

    /**
     * Construye la sentencia SQL del listado de medicos.
     *
     * @return string Sentencia parametrizada de listado.
     */
    private static function construirSqlListado(): string
    {
        return 'SELECT idMedico, medIdentificacion, medNombres, medApellidos, '
            . 'medEspecialidad, medTelefono, medCorreo, usuarios_idUsuario '
            . 'FROM medicos ORDER BY medApellidos ASC, medNombres ASC';
    }

    /**
     * Devuelve los valores del medico en el orden de los marcadores SQL.
     *
     * @return array<string, mixed> Valores vinculados a las sentencias.
     */
    private function construirParametros(): array
    {
        return [
            'identificacion' => $this->identificacion,
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'especialidad' => $this->especialidad,
            'telefono' => $this->telefono,
            'correo' => $this->correo,
            'usuarios_id_usuario' => $this->usuariosIdUsuario,
        ];
    }

    /**
     * Construye un medico a partir de una fila de la base de datos.
     *
     * @param array<string, mixed> $fila Fila devuelta por la consulta.
     * @return Medico Medico construido a traves de los modificadores.
     * @throws \InvalidArgumentException Si un dato almacenado no cumple las reglas del modelo.
     */
    private static function construirDesdeFila(array $fila): Medico
    {
        $medico = new Medico(
            (string) $fila['medIdentificacion'],
            (string) $fila['medNombres'],
            (string) $fila['medApellidos'],
            (string) $fila['medEspecialidad'],
            self::textoONulo($fila['medTelefono']),
            self::textoONulo($fila['medCorreo']),
            $fila['usuarios_idUsuario'] !== null ? (int) $fila['usuarios_idUsuario'] : null
        );

        $medico->setIdMedico((int) $fila['idMedico']);

        return $medico;
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
}
