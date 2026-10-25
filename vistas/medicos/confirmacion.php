<?php

declare(strict_types=1);

/**
 * Vista de confirmacion de eliminacion de un medico.
 *
 * Un enlace GET nunca modifica datos: la vista pide una confirmacion
 * explicita y el borrado se ejecuta unicamente con una peticion POST.
 */

$urlEstilos = RUTA_ESTILOS;
$urlListado = RUTA_BASE_URL . 'index.php?modulo=medicos&accion=listado';
$urlEliminar = RUTA_BASE_URL
    . 'index.php?modulo=medicos&accion=eliminar&id=' . (int) $idMedico;
$urlPacientes = RUTA_BASE_URL . 'index.php?modulo=pacientes&accion=listado';
$urlUsuarios = RUTA_BASE_URL . 'index.php?modulo=usuarios&accion=listado';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eliminar medico | Centro medico</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($urlEstilos, ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
    <header class="cabecera">
        <h1>Centro medico</h1>
        <p class="subtitulo">Eliminacion de medico</p>
    </header>

    <nav class="navegacion">
        <a href="<?= htmlspecialchars($urlPacientes, ENT_QUOTES, 'UTF-8') ?>">Pacientes</a>
        <a href="<?= htmlspecialchars($urlUsuarios, ENT_QUOTES, 'UTF-8') ?>">Usuarios</a>
        <a href="<?= htmlspecialchars($urlListado, ENT_QUOTES, 'UTF-8') ?>">Medicos</a>
    </nav>

    <main class="contenido">
        <div class="alerta alerta-advertencia" role="alert">
            <?= htmlspecialchars($mensajeConfirmacion, ENT_QUOTES, 'UTF-8') ?>
        </div>

        <table class="tabla tabla-detalle">
            <caption class="oculto">Datos del medico que se desea eliminar</caption>
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
                <tr>
                    <th scope="row">Especialidad</th>
                    <td>
                        <?= htmlspecialchars($valores['especialidad'], ENT_QUOTES, 'UTF-8') ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <form method="post" action="<?= htmlspecialchars($urlEliminar, ENT_QUOTES, 'UTF-8') ?>"
              class="formulario">
            <input type="hidden" id="id" name="id"
                   value="<?= htmlspecialchars((string) $idMedico, ENT_QUOTES, 'UTF-8') ?>">

            <div class="acciones">
                <button type="submit" class="boton boton-peligro">Eliminar medico</button>
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
