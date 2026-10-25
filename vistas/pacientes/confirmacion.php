<?php

declare(strict_types=1);

/**
 * Vista de confirmación de eliminación de un paciente.
 *
 * Un enlace GET nunca modifica datos: la vista pide una confirmación
 * explícita y el borrado se ejecuta únicamente con una petición POST
 * (reglas VU-01 y VU-02 del estándar de codificación).
 */

$urlEstilos = RUTA_ESTILOS;
$urlListado = RUTA_BASE_URL . 'index.php?accion=listado';
$urlEliminar = RUTA_BASE_URL . 'index.php?accion=eliminar&id=' . (int) $idPaciente;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eliminar paciente | Centro médico</title>
    <!-- Hoja de estilos del proyecto: assets/css/estilos.css -->
    <link rel="stylesheet" href="<?= htmlspecialchars($urlEstilos, ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
    <header class="cabecera">
        <h1>Módulo de pacientes</h1>
        <p class="subtitulo">Eliminación de paciente</p>
    </header>

    <main class="contenido">
        <div class="alerta alerta-advertencia" role="alert">
            <?= htmlspecialchars($mensajeConfirmacion, ENT_QUOTES, 'UTF-8') ?>
        </div>

        <table class="tabla tabla-detalle">
            <caption class="oculto">Datos del paciente que se desea eliminar</caption>
            <tbody>
                <tr>
                    <th scope="row">Nombre</th>
                    <td><?= htmlspecialchars($valores['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                    <th scope="row">Apellido</th>
                    <td><?= htmlspecialchars($valores['apellido'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                    <th scope="row">Número de identificación</th>
                    <td>
                        <?= htmlspecialchars($valores['numero_identificacion'],
                                              ENT_QUOTES, 'UTF-8') ?>
                    </td>
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
        <p>Centro médico &middot; Módulo de pacientes &middot; Evidencia GA7-220501096-AA2-EV01</p>
    </footer>
</body>
</html>
