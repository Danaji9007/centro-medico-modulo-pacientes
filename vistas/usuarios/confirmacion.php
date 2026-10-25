<?php

declare(strict_types=1);

/**
 * Vista de confirmacion de eliminacion de un usuario.
 *
 * Un enlace GET nunca modifica datos: la vista pide una confirmacion
 * explicita y el borrado se ejecuta unicamente con una peticion POST.
 */

$urlEstilos = RUTA_ESTILOS;
$urlListado = RUTA_BASE_URL . 'index.php?modulo=usuarios&accion=listado';
$urlEliminar = RUTA_BASE_URL
    . 'index.php?modulo=usuarios&accion=eliminar&id=' . (int) $idUsuario;
$urlPacientes = RUTA_BASE_URL . 'index.php?modulo=pacientes&accion=listado';
$urlMedicos = RUTA_BASE_URL . 'index.php?modulo=medicos&accion=listado';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eliminar usuario | Centro medico</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($urlEstilos, ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
    <header class="cabecera">
        <h1>Centro medico</h1>
        <p class="subtitulo">Eliminacion de usuario</p>
    </header>

    <nav class="navegacion">
        <a href="<?= htmlspecialchars($urlPacientes, ENT_QUOTES, 'UTF-8') ?>">Pacientes</a>
        <a href="<?= htmlspecialchars($urlListado, ENT_QUOTES, 'UTF-8') ?>">Usuarios</a>
        <a href="<?= htmlspecialchars($urlMedicos, ENT_QUOTES, 'UTF-8') ?>">Medicos</a>
    </nav>

    <main class="contenido">
        <div class="alerta alerta-advertencia" role="alert">
            <?= htmlspecialchars($mensajeConfirmacion, ENT_QUOTES, 'UTF-8') ?>
        </div>

        <table class="tabla tabla-detalle">
            <caption class="oculto">Datos del usuario que se desea eliminar</caption>
            <tbody>
                <tr>
                    <th scope="row">Usuario</th>
                    <td><?= htmlspecialchars($valores['usuario'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                    <th scope="row">Estado</th>
                    <td><?= htmlspecialchars($valores['estado'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                    <th scope="row">Rol</th>
                    <td><?= htmlspecialchars($valores['rol'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            </tbody>
        </table>

        <form method="post" action="<?= htmlspecialchars($urlEliminar, ENT_QUOTES, 'UTF-8') ?>"
              class="formulario">
            <input type="hidden" id="id" name="id"
                   value="<?= htmlspecialchars((string) $idUsuario, ENT_QUOTES, 'UTF-8') ?>">

            <div class="acciones">
                <button type="submit" class="boton boton-peligro">Eliminar usuario</button>
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
