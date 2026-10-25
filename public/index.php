<?php

declare(strict_types=1);

use App\Controladores\MedicoControlador;
use App\Controladores\PacienteControlador;
use App\Controladores\UsuarioControlador;

/**
 * Punto de entrada unico de la aplicacion web del centro medico.
 *
 * El enrutado se hace con los parametros `modulo` y `accion`: el controlador
 * decide que operacion se ejecuta sobre el modelo y que vista se presenta al
 * usuario (patron Modelo-Vista-Controlador, seccion 4.18 del estandar de
 * codificacion).
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');

require_once __DIR__ . '/../config/config.php';

$modulo = strtolower(trim((string) ($_GET['modulo'] ?? 'pacientes')));

switch ($modulo) {
    case 'usuarios':
        $controlador = new UsuarioControlador();
        break;
    case 'medicos':
        $controlador = new MedicoControlador();
        break;
    case 'pacientes':
    default:
        $controlador = new PacienteControlador();
        break;
}

$controlador->despachar();
