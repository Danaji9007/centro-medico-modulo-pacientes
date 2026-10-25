<?php

declare(strict_types=1);

namespace App\Modelos;

use App\Infraestructura\BaseDeDatos;

/**
 * Modelo del usuario del centro medico.
 *
 * Concentra los datos del usuario y las operaciones de persistencia sobre la
 * tabla `usuarios` de la base de datos `centromedico`. La contrasena se
 * almacena siempre con `password_hash()` y se verifica con `password_verify()`
 * (reglas SE-01 y SE-02 del estandar de codificacion).
 */
final class Usuario
{
    /* ----------------------------------------------------------------- */
    /* Constantes de clase (regla V-09).                                 */
    /* ----------------------------------------------------------------- */

    public const ESTADO_ACTIVO = 'Activo';
    public const ESTADO_INACTIVO = 'Inactivo';

    public const ROL_ADMINISTRADOR = 'Administrador';
    public const ROL_MEDICO = 'Medico';
    public const ROL_RECEPCIONISTA = 'Recepcionista';
    public const ROL_PACIENTE = 'Paciente';

    public const LONGITUD_MAXIMA_LOGIN = 50;
    public const LONGITUD_MINIMA_PASSWORD = 6;
    public const LONGITUD_MAXIMA_ROL = 50;
    public const LONGITUD_MAXIMA_ESTADO = 20;

    /* ----------------------------------------------------------------- */
    /* Propiedades (reglas P-01, P-02 y P-03).                           */
    /* ----------------------------------------------------------------- */

    /**
     * Identificador del usuario; cero mientras el registro no esta persistido.
     */
    private int $idUsuario = 0;

    private string $usuario = '';
    private string $password = '';
    private string $estado = self::ESTADO_ACTIVO;
    private string $rol = self::ROL_RECEPCIONISTA;
    private ?int $rolesIdRol = null;

    /* ----------------------------------------------------------------- */
    /* Constructor (reglas M-05 y D-03).                                 */
    /* ----------------------------------------------------------------- */

    /**
     * Construye un usuario con sus datos.
     *
     * La asignacion se delega en los modificadores para no duplicar las
     * reglas de validacion (regla D-03).
     *
     * @param string $usuario Nombre de login del usuario.
     * @param string $password Contrasena en texto plano.
     * @param string $estado Estado del usuario.
     * @param string $rol Rol del usuario.
     * @param int|null $rolesIdRol Identificador del rol en la tabla roles.
     * @throws \InvalidArgumentException Si algun dato no cumple las reglas del modelo.
     */
    public function __construct(
        string $usuario,
        string $password,
        string $estado = self::ESTADO_ACTIVO,
        string $rol = self::ROL_RECEPCIONISTA,
        ?int $rolesIdRol = null
    ) {
        $this->setUsuario($usuario);
        $this->setPassword($password);
        $this->setEstado($estado);
        $this->setRol($rol);
        $this->setRolesIdRol($rolesIdRol);
    }

    /* ----------------------------------------------------------------- */
    /* Consultores (regla M-02).                                          */
    /* ----------------------------------------------------------------- */

    /**
     * Obtiene el identificador del usuario.
     *
     * @return int Identificador del usuario.
     */
    public function getIdUsuario(): int
    {
        return $this->idUsuario;
    }

    /**
     * Obtiene el nombre de login del usuario.
     *
     * @return string Nombre de login.
     */
    public function getUsuario(): string
    {
        return $this->usuario;
    }

    /**
     * Obtiene el hash de la contrasena.
     *
     * @return string Hash de la contrasena.
     */
    public function getPasswordHash(): string
    {
        return $this->password;
    }

    /**
     * Obtiene el estado del usuario.
     *
     * @return string Estado del usuario.
     */
    public function getEstado(): string
    {
        return $this->estado;
    }

    /**
     * Obtiene el rol del usuario.
     *
     * @return string Rol del usuario.
     */
    public function getRol(): string
    {
        return $this->rol;
    }

    /**
     * Obtiene el identificador del rol asociado.
     *
     * @return int|null Identificador del rol asociado.
     */
    public function getRolesIdRol(): ?int
    {
        return $this->rolesIdRol;
    }

    /* ----------------------------------------------------------------- */
    /* Modificadores con validacion (reglas M-02 y EN-04).                 */
    /* ----------------------------------------------------------------- */

    /**
     * Asigna el identificador del usuario.
     *
     * @param int $idUsuario Identificador del usuario.
     * @return void
     * @throws \InvalidArgumentException Si el identificador no es un entero positivo.
     */
    public function setIdUsuario(int $idUsuario): void
    {
        if ($idUsuario <= 0) {
            throw new \InvalidArgumentException(
                'El identificador del usuario debe ser un numero positivo.'
            );
        }

        $this->idUsuario = $idUsuario;
    }

    /**
     * Asigna el nombre de login del usuario.
     *
     * @param string $usuario Nombre de login.
     * @return void
     * @throws \InvalidArgumentException Si el login esta vacio o es demasiado largo.
     */
    public function setUsuario(string $usuario): void
    {
        $valor = trim($usuario);

        if ($valor === '') {
            throw new \InvalidArgumentException('El nombre de usuario es obligatorio.');
        }

        if (mb_strlen($valor) > self::LONGITUD_MAXIMA_LOGIN) {
            throw new \InvalidArgumentException(
                'El nombre de usuario no puede superar los '
                . self::LONGITUD_MAXIMA_LOGIN . ' caracteres.'
            );
        }

        $this->usuario = $valor;
    }

    /**
     * Asigna y cifra la contrasena del usuario.
     *
     * @param string $password Contrasena en texto plano.
     * @return void
     * @throws \InvalidArgumentException Si la contrasena no cumple la longitud minima.
     */
    public function setPassword(string $password): void
    {
        if (mb_strlen($password) < self::LONGITUD_MINIMA_PASSWORD) {
            throw new \InvalidArgumentException(
                'La contrasena debe tener al menos '
                . self::LONGITUD_MINIMA_PASSWORD . ' caracteres.'
            );
        }

        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Asigna el hash de la contrasena directamente.
     *
     * Se usa al reconstruir un usuario desde la base de datos.
     *
     * @param string $passwordHash Hash de la contrasena.
     * @return void
     */
    public function setPasswordHash(string $passwordHash): void
    {
        if ($passwordHash === '') {
            throw new \InvalidArgumentException('El hash de la contrasena no puede estar vacio.');
        }

        $this->password = $passwordHash;
    }

    /**
     * Asigna el estado del usuario.
     *
     * @param string $estado Estado del usuario.
     * @return void
     * @throws \InvalidArgumentException Si el estado esta vacio.
     */
    public function setEstado(string $estado): void
    {
        $valor = trim($estado);

        if ($valor === '') {
            throw new \InvalidArgumentException('El estado del usuario es obligatorio.');
        }

        if (mb_strlen($valor) > self::LONGITUD_MAXIMA_ESTADO) {
            throw new \InvalidArgumentException(
                'El estado no puede superar los '
                . self::LONGITUD_MAXIMA_ESTADO . ' caracteres.'
            );
        }

        $this->estado = $valor;
    }

    /**
     * Asigna el rol del usuario.
     *
     * @param string $rol Rol del usuario.
     * @return void
     * @throws \InvalidArgumentException Si el rol esta vacio.
     */
    public function setRol(string $rol): void
    {
        $valor = trim($rol);

        if ($valor === '') {
            throw new \InvalidArgumentException('El rol del usuario es obligatorio.');
        }

        if (mb_strlen($valor) > self::LONGITUD_MAXIMA_ROL) {
            throw new \InvalidArgumentException(
                'El rol no puede superar los '
                . self::LONGITUD_MAXIMA_ROL . ' caracteres.'
            );
        }

        $this->rol = $valor;
    }

    /**
     * Asigna el identificador del rol asociado.
     *
     * @param int|null $rolesIdRol Identificador del rol.
     * @return void
     */
    public function setRolesIdRol(?int $rolesIdRol): void
    {
        $this->rolesIdRol = $rolesIdRol;
    }

    /* ----------------------------------------------------------------- */
    /* Operaciones de persistencia (reglas M-07, B-06 y B-08).            */
    /* ----------------------------------------------------------------- */

    /**
     * Registra el usuario en la base de datos.
     *
     * @return bool `true` si el registro fue insertado.
     * @throws \PDOException Si la insercion falla; la transaccion se revierte.
     */
    public function agregarUsuario(): bool
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
            $this->idUsuario = $baseDeDatos->ultimoId();
        }

        return $filas > 0;
    }

    /**
     * Busca un usuario por su login.
     *
     * @param string $usuario Login del usuario.
     * @return array<string, mixed>|null Usuario encontrado o `null` si no existe.
     * @throws \PDOException Si la consulta falla.
     */
    public static function consultarUsuario(string $usuario): ?array
    {
        $login = trim($usuario);

        if ($login === '') {
            return null;
        }

        return BaseDeDatos::obtenerInstancia()->obtenerFila(
            self::construirSqlConsultaPorLogin(),
            ['usuario' => $login]
        );
    }

    /**
     * Busca un usuario por su identificador.
     *
     * @param int $idUsuario Identificador del usuario.
     * @return Usuario|null Usuario encontrado o `null` si no existe.
     * @throws \PDOException Si la consulta falla.
     * @throws \InvalidArgumentException Si un dato almacenado no cumple las reglas del modelo.
     */
    public static function buscarPorId(int $idUsuario): ?Usuario
    {
        if ($idUsuario <= 0) {
            return null;
        }

        $fila = BaseDeDatos::obtenerInstancia()->obtenerFila(
            self::construirSqlConsultaPorId(),
            ['id' => $idUsuario]
        );

        if ($fila === null) {
            return null;
        }

        return self::construirDesdeFila($fila);
    }

    /**
     * Actualiza los datos del usuario en la base de datos.
     *
     * @param int $idUsuario Identificador del usuario a actualizar.
     * @return bool `true` si el registro fue actualizado.
     * @throws \InvalidArgumentException Si el usuario aun no ha sido registrado.
     * @throws \PDOException Si la actualizacion falla; la transaccion se revierte.
     */
    public function actualizarUsuario(int $idUsuario): bool
    {
        if ($idUsuario <= 0) {
            throw new \InvalidArgumentException(
                'El usuario no esta registrado: no se puede actualizar.'
            );
        }

        $baseDeDatos = BaseDeDatos::obtenerInstancia();
        $baseDeDatos->iniciarTransaccion();

        try {
            $filas = $baseDeDatos->ejecutar(
                $this->construirSqlActualizacion(),
                array_merge($this->construirParametros(), ['id' => $idUsuario])
            );
            $baseDeDatos->confirmarTransaccion();
        } catch (\Throwable $error) {
            $baseDeDatos->revertirTransaccion();

            throw $error;
        }

        if ($filas > 0) {
            $this->idUsuario = $idUsuario;
        }

        return $filas > 0;
    }

    /**
     * Elimina el usuario de la base de datos.
     *
     * @param int $idUsuario Identificador del usuario a eliminar.
     * @return bool `true` si el registro fue eliminado.
     * @throws \InvalidArgumentException Si el identificador no es valido.
     * @throws \PDOException Si la eliminacion falla; la transaccion se revierte.
     */
    public static function eliminarUsuario(int $idUsuario): bool
    {
        if ($idUsuario <= 0) {
            throw new \InvalidArgumentException(
                'El usuario no esta registrado: no se puede eliminar.'
            );
        }

        $baseDeDatos = BaseDeDatos::obtenerInstancia();
        $baseDeDatos->iniciarTransaccion();

        try {
            $filas = $baseDeDatos->ejecutar(
                self::construirSqlEliminacion(),
                ['id' => $idUsuario]
            );
            $baseDeDatos->confirmarTransaccion();
        } catch (\Throwable $error) {
            $baseDeDatos->revertirTransaccion();

            throw $error;
        }

        return $filas > 0;
    }

    /**
     * Lista todos los usuarios registrados.
     *
     * @return array<int, array<string, mixed>> Filas de la tabla `usuarios`.
     * @throws \PDOException Si la consulta falla.
     */
    public static function listarUsuarios(): array
    {
        return BaseDeDatos::obtenerInstancia()->consultar(self::construirSqlListado());
    }

    /**
     * Verifica si una contrasena coincide con el hash almacenado.
     *
     * @param string $password Contrasena en texto plano.
     * @return bool `true` si la contrasena es correcta.
     */
    public function verificarPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    /**
     * Verifica si un login ya esta registrado.
     *
     * @param string $usuario Login buscado.
     * @param int|null $excluirId Identificador que se excluye de la busqueda.
     * @return bool `true` si el login ya existe.
     * @throws \PDOException Si la consulta falla.
     */
    public static function existeUsuario(string $usuario, ?int $excluirId = null): bool
    {
        $login = trim($usuario);

        if ($login === '') {
            return false;
        }

        $sql = 'SELECT COUNT(*) AS total FROM usuarios WHERE usuLogin = :usuario';
        $parametros = ['usuario' => $login];

        if ($excluirId !== null) {
            $sql .= ' AND idUsuario <> :excluir';
            $parametros['excluir'] = $excluirId;
        }

        $fila = BaseDeDatos::obtenerInstancia()->obtenerFila($sql, $parametros);

        return $fila !== null && (int) $fila['total'] > 0;
    }

    /* ----------------------------------------------------------------- */
    /* Metodos privados (reglas D-06 y B-09).                             */
    /* ----------------------------------------------------------------- */

    /**
     * Construye la sentencia SQL del alta de un usuario.
     *
     * @return string Sentencia parametrizada de insercion.
     */
    private function construirSqlInsercion(): string
    {
        return 'INSERT INTO usuarios (usuLogin, usuPassword, usuEstado, usuRol, roles_id_rol) '
            . 'VALUES (:usuario, :password, :estado, :rol, :roles_id_rol)';
    }

    /**
     * Construye la sentencia SQL de actualizacion de un usuario.
     *
     * @return string Sentencia parametrizada de actualizacion.
     */
    private function construirSqlActualizacion(): string
    {
        return 'UPDATE usuarios SET usuLogin = :usuario, usuPassword = :password, '
            . 'usuEstado = :estado, usuRol = :rol, roles_id_rol = :roles_id_rol '
            . 'WHERE idUsuario = :id';
    }

    /**
     * Construye la sentencia SQL de eliminacion de un usuario.
     *
     * @return string Sentencia parametrizada de eliminacion.
     */
    private static function construirSqlEliminacion(): string
    {
        return 'DELETE FROM usuarios WHERE idUsuario = :id';
    }

    /**
     * Construye la sentencia SQL de consulta de un usuario por login.
     *
     * @return string Sentencia parametrizada de consulta.
     */
    private static function construirSqlConsultaPorLogin(): string
    {
        return 'SELECT idUsuario, usuLogin, usuPassword, usuEstado, usuRol, roles_id_rol '
            . 'FROM usuarios WHERE usuLogin = :usuario';
    }

    /**
     * Construye la sentencia SQL de consulta de un usuario por identificador.
     *
     * @return string Sentencia parametrizada de consulta.
     */
    private static function construirSqlConsultaPorId(): string
    {
        return 'SELECT idUsuario, usuLogin, usuPassword, usuEstado, usuRol, roles_id_rol '
            . 'FROM usuarios WHERE idUsuario = :id';
    }

    /**
     * Construye la sentencia SQL del listado de usuarios.
     *
     * @return string Sentencia parametrizada de listado.
     */
    private static function construirSqlListado(): string
    {
        return 'SELECT idUsuario, usuLogin, usuPassword, usuEstado, usuRol, roles_id_rol '
            . 'FROM usuarios ORDER BY usuLogin ASC';
    }

    /**
     * Devuelve los valores del usuario en el orden de los marcadores SQL.
     *
     * @return array<string, mixed> Valores vinculados a las sentencias.
     */
    private function construirParametros(): array
    {
        return [
            'usuario' => $this->usuario,
            'password' => $this->password,
            'estado' => $this->estado,
            'rol' => $this->rol,
            'roles_id_rol' => $this->rolesIdRol,
        ];
    }

    /**
     * Construye un usuario a partir de una fila de la base de datos.
     *
     * @param array<string, mixed> $fila Fila devuelta por la consulta.
     * @return Usuario Usuario construido a traves de los modificadores.
     * @throws \InvalidArgumentException Si un dato almacenado no cumple las reglas del modelo.
     */
    private static function construirDesdeFila(array $fila): Usuario
    {
        $usuario = new Usuario(
            (string) $fila['usuLogin'],
            '********', // valor temporal; se reemplaza por el hash real
            (string) $fila['usuEstado'],
            (string) $fila['usuRol'],
            $fila['roles_id_rol'] !== null ? (int) $fila['roles_id_rol'] : null
        );

        $usuario->setPasswordHash((string) $fila['usuPassword']);
        $usuario->setIdUsuario((int) $fila['idUsuario']);

        return $usuario;
    }
}
