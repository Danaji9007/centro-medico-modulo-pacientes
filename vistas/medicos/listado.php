<?php

declare(strict_types=1);

/**
 * Vista del listado de medicos.
 *
 * Presenta una fila por cada medico registrado. Todos los valores se
 * escapan antes de imprimirse (regla SE-03).
 */

$urlEstilos = RUTA_ESTILOS;
$urlListado = RUTA_BASE_URL . 'index.php?modulo=medicos&accion=listado';
$urlRegistro = RUTA_BASE_URL . 'index.php?modulo=medicos&accion=registro';
$urlPacientes = RUTA_BASE_URL . 'index.php?modulo=pacientes&accion=listado';
$urlUsuarios = RUTA_BASE_URL . 'index.php?modulo=usuarios&accion=listado';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Listado de medicos | Centro medico</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($urlEstilos, ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
    <header class="cabecera">
        <h1>Centro medico</h1>
        <p class="subtitulo">Listado de medicos</p>
    </header>

    <nav class="navegacion">
        <a href="<?= htmlspecialchars($urlPacientes, ENT_QUOTES, 'UTF-8') ?>">Pacientes</a>
        <a href="<?= htmlspecialchars($urlUsuarios, ENT_QUOTES, 'UTF-8') ?>">Usuarios</a>
        <a href="<?= htmlspecialchars($urlListado, ENT_QUOTES, 'UTF-8') ?>">Medicos</a>
    </nav>

    <main class="contenido">
        <?php if ($mensajeError !== null) : ?>
            <div class="alerta alerta-error" role="alert">
                <?= htmlspecialchars($mensajeError, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <div class="barra-acciones">
            <a class="boton boton-primario"
               href="<?= htmlspecialchars($urlRegistro, ENT_QUOTES, 'UTF-8') ?>">
                Registrar medico
            </a>
            <span class="contador">
                <?= count($filas) ?> medico(s) registrado(s)
            </span>
        </div>

        <?php if (count($filas) === 0) : ?>
            <p class="vacio">Todavia no hay medicos registrados en la base de datos.</p>
        <?php else : ?>
            <table class="tabla">
                <caption class="oculto">Medicos registrados en el centro medico</caption>
                <thead>
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Identificacion</th>
                        <th scope="col">Nombres</th>
                        <th scope="col">Apellidos</th>
                        <th scope="col">Especialidad</th>
                        <th scope="col">Telefono</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filas as $fila) : ?>
                        <?php
                        $idMedico = (int) $fila['idMedico'];
                        $urlEdicion = RUTA_BASE_URL
                            . 'index.php?modulo=medicos&accion=edicion&id=' . $idMedico;
                        $urlEliminar = RUTA_BASE_URL
                            . 'index.php?modulo=medicos&accion=eliminar&id=' . $idMedico;
                        ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars((string) $idMedico,
                                                      ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars((string) $fila['medIdentificacion'],
                                                      ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars((string) $fila['medNombres'],
                                                      ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars((string) $fila['medApellidos'],
                                                      ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars((string) $fila['medEspecialidad'],
                                                      ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars((string) ($fila['medTelefono'] ?? ''),
                                                      ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars((string) ($fila['medCorreo'] ?? ''),
                                                      ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="acciones-fila">
                                <a href="<?= htmlspecialchars($urlEdicion, ENT_QUOTES, 'UTF-8') ?>">
                                    Editar
                                </a>
                                <a class="enlace-peligro"
                                   href="<?= htmlspecialchars($urlEliminar,
                                                       ENT_QUOTES, 'UTF-8') ?>">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>

    <footer class="pie">
        <p>Centro medico &middot; Evidencia GA7-220501096-AA2-EV01</p>
    </footer>
</body>
</html>
