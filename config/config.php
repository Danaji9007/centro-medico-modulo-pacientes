<?php

declare(strict_types=1);

/**
 * Configuración general de la aplicación web del centro médico.
 *
 * Concentra las credenciales de la base de datos, las rutas del proyecto, la
 * codificación de caracteres y la autocarga de clases. El resto del código
 * fuente no contiene secretos (reglas B-02 y SE-07 del estándar de
 * codificación del proyecto).
 */

/* --------------------------------------------------------------------- */
/* Credenciales de la base de datos (regla B-02).                         */
/* --------------------------------------------------------------------- */

const BD_SERVIDOR = '127.0.0.1';
const BD_PUERTO = 3306;
const BD_USUARIO = 'root';
const BD_CLAVE = '';
const BD_ESQUEMA = 'clinica_salud';
const BD_CHARSET = 'utf8mb4';
const BD_MODO_SQL = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION';

/* --------------------------------------------------------------------- */
/* Rutas del sistema de archivos.                                         */
/* --------------------------------------------------------------------- */

const RUTA_RAIZ = __DIR__ . '/..';
const RUTA_VISTAS = __DIR__ . '/../vistas';
const RUTA_ASSETS = __DIR__ . '/../assets';
const RUTA_PUBLICA = __DIR__ . '/../public';

/* --------------------------------------------------------------------- */
/* Codificación de caracteres (regla G-01).                               */
/* --------------------------------------------------------------------- */

const CODIFICACION = 'UTF-8';

/**
 * Paquetes de la aplicación (namespaces) y carpeta que los respalda.
 *
 * Cada namespace es un paquete: `App\Modelos` vive en `modelos/`,
 * `App\Controladores` en `controladores/`, `App\Infraestructura` en
 * `infraestructura/` y `App\Vistas` en `vistas/` (regla G-09).
 */
const MAPA_PAQUETES = [
    'Modelos' => 'modelos',
    'Controladores' => 'controladores',
    'Infraestructura' => 'infraestructura',
    'Vistas' => 'vistas',
];

/* --------------------------------------------------------------------- */
/* Rutas web: dependen de la URL pública de la instalación.               */
/* --------------------------------------------------------------------- */

$rutaGuion = (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php');
$rutaBase = rtrim(str_replace('\\', '/', dirname($rutaGuion)), '/');

define('RUTA_BASE_URL', $rutaBase === '' ? '/' : $rutaBase . '/');
define('RUTA_ESTILOS', RUTA_BASE_URL . 'estilos.php');

/**
 * Autocarga de clases con convención PSR-4 sobre el namespace `App\`.
 *
 * El nombre de la clase determina el archivo que la contiene, de modo que
 * un archivo y una clase comparten nombre (reglas C-04 y G-09).
 */
spl_autoload_register(static function (string $nombreClase): void {
    $prefijo = 'App\\';

    if (strncmp($nombreClase, $prefijo, strlen($prefijo)) !== 0) {
        return;
    }

    $segmentos = explode('\\', substr($nombreClase, strlen($prefijo)));
    $paquete = array_shift($segmentos);

    if (!isset(MAPA_PAQUETES[$paquete])) {
        return;
    }

    $carpeta = RUTA_RAIZ . '/' . MAPA_PAQUETES[$paquete];
    $archivo = $carpeta . '/' . implode('_', $segmentos) . '.php';

    if (is_file($archivo)) {
        require_once $archivo;
    }
});
