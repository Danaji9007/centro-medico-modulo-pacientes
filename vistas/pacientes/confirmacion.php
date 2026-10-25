<?php

declare(strict_types=1);

/**
 * Vista de confirmacion de eliminacion de un paciente.
 *
 * Un enlace GET nunca modifica datos: la vista pide una confirmacion
 * explicita y el borrado se ejecuta unicamente con una peticion POST
 * (reglas VU-01 y VU-02 del estandar de codificacion).
 */

$urlEstilos = RUTA_ESTILOS;
$urlListado = RUTA_BASE_URL . 'index.php?modulo=pacientes&accion=listado';
$urlEliminar = RUTA_BASE_URL
    . 'index.php?modulo=pacientes&accion=eliminar&id=' . (int) $idPaciente;
$urlUsuarios = RUTA_BASE_URL . 'index.php?modulo=usuarios&accion=listado';
$urlMedicos = RUTA_BASE_URL . 'index.php?modulo=medicos&accion=listado';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eliminar paciente | Centro medico</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($urlEstilos, ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
    <header class="cabecera">
        <h1>Centro medico</h1>
        <p class="subtitulo">Eliminacion de paciente</p>
    </header>

    <nav class="navegacion">
        <a href="<?= htmlspecialchars($urlListado, ENT_QUOTES, 'UTF-8') ?>">Pacientes</a>
        <a href="<?= htmlspecialchars($urlUsuarios, ENT_QUOTES, 'UTF-8') ?>">Usuarios</a>
        <a href="<?= htmlspecialchars($urlMedicos, ENT_QUOTES, 'UTF-8') ?>">Medicos</a>
    </nav>

    <main class="contenido">
        <div class="alerta alerta-advertencia" role="alert">
            <?= htmlspecialchars($mensajeConfirmacion, ENT_QUOTES, 'UTF-8') ?>
        </div>

        <table class="tabla tabla-detalle">
            <caption class="oculto">Datos del paciente que se desea eliminar</caption>
            <tbody>
                <tr>
                    <th scope="row">Identificacion</th>
                    <td>
                        <?= htmlspecialchars($valores['identificacion'],
                                              ENT_QUOTES, 'UTF-8') ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Nombres</th>
                    <td><?= htmlspecialchars($valores['nombres'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                    <th scope="row">Apellidos</th>
                    <td><?= htmlspecialchars($valores['apellidos'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            </tbody>
        </table>

        <form method="post" action="<?= htmlspecialchars($urlEliminar, ENT_QUOTES, 'UTF-8') ?>"
              class="formulario">
            <input type="hidden" id="id" name="id"
                   value="<?= htmlspecialchars((string) $idPaciente, ENT_QUOTES, 'UTF-8') ?>">

            <div class="acciones">
                <button type="submit" class="boton boton-peligro">Eliminar paciente</button>
                <a class="boton boton-secundario"
                   href="<?= htmlspecialchars($urlListado, ENT_QUOTES, 'UTF-8') ?>">
                    Cancelar
                </a>
            </div>
        </form>
    </main>

    <footer class="pie">
        <p>Centro medico &middot; Evidencia GA7-220501096-AA2-EV01</p>
    </footer>
</body>
</html>
