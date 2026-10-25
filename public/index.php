<?php

declare(strict_types=1);

use App\Controladores\PacienteControlador;

/**
 * Punto de entrada único de la aplicación web del centro médico.
 *
 * El enrutado se hace con el parámetro `accion`: el controlador decide qué
 * operación se ejecuta sobre el modelo y qué vista se presenta al usuario
 * (patrón Modelo-Vista-Controlador, sección 4.18 del estándar de codificación).
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');

require_once __DIR__ . '/../config/config.php';

$controlador = new PacienteControlador();
$controlador->despachar();
