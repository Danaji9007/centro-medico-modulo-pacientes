<?php

declare(strict_types=1);

namespace App\Infraestructura;

/**
 * Punto único de acceso a la base de datos.
 *
 * Encapsula la conexión PDO en un patrón singleton y expone operaciones de
 * consulta parametrizada. Ninguna otra clase crea conexiones ni contiene
 * credenciales (reglas B-01, B-02 y B-06 del estándar de codificación).
 */
final class BaseDeDatos
{
    /**
     * Instancia única de la clase (patrón singleton).
     */
    private static ?BaseDeDatos $instancia = null;

    /**
     * Conexión activa a la base de datos.
     */
    private \PDO $conexion;

    /**
     * Crea la conexión PDO con la configuración declarada en `config/config.php`.
     *
     * @throws \PDOException Si la base de datos no está disponible o la
     *                       configuración es inválida.
     */
    private function __construct()
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            BD_SERVIDOR,
            BD_PUERTO,
            BD_ESQUEMA,
            BD_CHARSET
        );

        $this->conexion = new \PDO($dsn, BD_USUARIO, BD_CLAVE, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::MYSQL_ATTR_FOUND_ROWS => true,
        ]);

        // El modo estricto de MySQL no admite marcadores de posición: el valor
        // proviene de la configuración del proyecto, nunca de la entrada del
        // usuario (regla SE-09).
        $this->conexion->exec('SET SESSION sql_mode = \'' . BD_MODO_SQL . '\'');
    }

    /**
     * Devuelve la instancia única de la clase.
     *
     * La conexión se crea la primera vez y se reutiliza en las llamadas
     * posteriores, de modo que todas las operaciones comparten el mismo
     * enlace y la misma transacción implícita.
     *
     * @return BaseDeDatos Instancia compartida de la clase.
     * @throws \PDOException Si no se puede establecer la conexión.
     */
    public static function obtenerInstancia(): BaseDeDatos
    {
        if (!self::$instancia instanceof self) {
            self::$instancia = new self();
        }

        return self::$instancia;
    }

    /**
     * Ejecuta una consulta y devuelve todas sus filas.
     *
     * @param string $sql Sentencia SQL con marcadores de posición.
     * @param array<string, mixed> $parametros Valores vinculados por nombre a la sentencia.
     * @return array<int, array<string, mixed>> Filas devueltas por la consulta.
     * @throws \PDOException Si la sentencia no puede ejecutarse.
     */
    public function consultar(string $sql, array $parametros = []): array
    {
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute($parametros);

        return $sentencia->fetchAll();
    }

    /**
     * Ejecuta una consulta y devuelve su primera fila.
     *
     * @param string $sql Sentencia SQL con marcadores de posición.
     * @param array<string, mixed> $parametros Valores vinculados por nombre a la sentencia.
     * @return array<string, mixed>|null Fila encontrada o `null` si no hay resultados.
     * @throws \PDOException Si la sentencia no puede ejecutarse.
     */
    public function obtenerFila(string $sql, array $parametros = []): ?array
    {
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute($parametros);
        $fila = $sentencia->fetch();

        return $fila === false ? null : $fila;
    }

    /**
     * Ejecuta una sentencia de escritura y devuelve las filas afectadas.
     *
     * @param string $sql Sentencia SQL con marcadores de posición.
     * @param array<string, mixed> $parametros Valores vinculados por nombre a la sentencia.
     * @return int Número de filas insertadas, actualizadas o eliminadas.
     * @throws \PDOException Si la sentencia no puede ejecutarse.
     */
    public function ejecutar(string $sql, array $parametros = []): int
    {
        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute($parametros);

        return $sentencia->rowCount();
    }

    /**
     * Devuelve el identificador generado por la última inserción.
     *
     * @return int Identificador del registro insertado.
     */
    public function ultimoId(): int
    {
        return (int) $this->conexion->lastInsertId();
    }

    /**
     * Inicia una transacción de base de datos.
     *
     * @return void
     */
    public function iniciarTransaccion(): void
    {
        if (!$this->conexion->inTransaction()) {
            $this->conexion->beginTransaction();
        }
    }

    /**
     * Confirma la transacción abierta.
     *
     * @return void
     */
    public function confirmarTransaccion(): void
    {
        if ($this->conexion->inTransaction()) {
            $this->conexion->commit();
        }
    }

    /**
     * Revierte la transacción abierta.
     *
     * @return void
     */
    public function revertirTransaccion(): void
    {
        if ($this->conexion->inTransaction()) {
            $this->conexion->rollBack();
        }
    }
}
