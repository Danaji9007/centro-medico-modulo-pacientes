<?php

declare(strict_types=1);

/**
 * Configuración general de la aplicación web del centro médico.
 *
 * Concentra las credenciales de la base de datos, las rutas del proyecto, la
 * codificacion de caracteres y la autocarga de clases. Las credenciales se
 * leen desde el archivo `.env` de la raiz del proyecto (reglas B-02 y SE-07
 * del estandar de codificacion del proyecto). El archivo `.env` no se
 * versiona; para crearlo localmente se usa `.env.example` como plantilla.
 */

/* --------------------------------------------------------------------- */
/* Carga de variables de entorno desde .env (reglas B-02 y SE-07).        */
/* --------------------------------------------------------------------- */

/**
 * Lee las variables de entorno definidas en el archivo `.env` de la raiz.
 *
 * @return void
 */
function cargarVariablesEntorno(): void
{
    $rutaEnv = __DIR__ . '/../.env';

    if (!is_file($rutaEnv)) {
        return;
    }

    $lineas = file($rutaEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if ($lineas === false) {
        return;
    }

    foreach ($lineas as $linea) {
        $linea = trim($linea);

        if ($linea === '' || str_starts_with($linea, '#')) {
            continue;
        }

        $partes = explode('=', $linea, 2);

        if (count($partes) !== 2) {
            continue;
        }

        [$nombre, $valor] = $partes;
        $nombre = trim($nombre);
        $valor = trim($valor);

        if ($nombre === '' || getenv($nombre) !== false) {
            continue;
        }

        putenv("{$nombre}={$valor}");
        $_ENV[$nombre] = $valor;
    }
}

cargarVariablesEntorno();

/**
 * Obtiene una variable de entorno o devuelve el valor por defecto.
 *
 * @param string $nombre Nombre de la variable.
 * @param string $valorPorDefecto Valor por defecto si no esta definida.
 * @return string Valor de la variable o el valor por defecto.
 */
function obtenerVariableEntorno(string $nombre, string $valorPorDefecto = ''): string
{
    $valor = getenv($nombre);

    return $valor === false ? $valorPorDefecto : $valor;
}

/* --------------------------------------------------------------------- */
/* Credenciales de la base de datos (regla B-02).                         */
/* --------------------------------------------------------------------- */

define('BD_SERVIDOR', obtenerVariableEntorno('BD_SERVIDOR', '127.0.0.1'));
define('BD_PUERTO', (int) obtenerVariableEntorno('BD_PUERTO', '3306'));
define('BD_USUARIO', obtenerVariableEntorno('BD_USUARIO', 'root'));
define('BD_CLAVE', obtenerVariableEntorno('BD_CLAVE', ''));
define('BD_ESQUEMA', obtenerVariableEntorno('BD_ESQUEMA', 'centromedico'));
define('BD_CHARSET', obtenerVariableEntorno('BD_CHARSET', 'utf8mb4'));
define('BD_MODO_SQL', obtenerVariableEntorno(
    'BD_MODO_SQL',
    'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION'
));

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
