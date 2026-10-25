<?php

declare(strict_types=1);

/**
 * Servidor de recursos estáticos del proyecto.
 *
 * `public/` es la única carpeta expuesta por el servidor web (regla A-02) y la
 * hoja de estilos vive en `assets/css/`, fuera de esa carpeta. Este archivo
 * entrega el recurso sin duplicar su contenido ni exponer el resto del
 * proyecto por la URL.
 */

error_reporting(E_ALL);

$rutaEstilos = __DIR__ . '/../assets/css/estilos.css';

if (!is_file($rutaEstilos)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'No se encontró la hoja de estilos del proyecto.';

    return;
}

header('Content-Type: text/css; charset=utf-8');
header('Content-Length: ' . (string) filesize($rutaEstilos));

readfile($rutaEstilos);
