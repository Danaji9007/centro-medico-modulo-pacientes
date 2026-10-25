<?php

declare(strict_types=1);

/**
 * Vista del listado de usuarios.
 *
 * Presenta una fila por cada usuario registrado. Todos los valores se
 * escapan antes de imprimirse (regla SE-03).
 */

$urlEstilos = RUTA_ESTILOS;
$urlListado = RUTA_BASE_URL . 'index.php?modulo=usuarios&accion=listado';
$urlRegistro = RUTA_BASE_URL . 'index.php?modulo=usuarios&accion=registro';
$urlPacientes = RUTA_BASE_URL . 'index.php?modulo=pacientes&accion=listado';
$urlMedicos = RUTA_BASE_URL . 'index.php?modulo=medicos&accion=listado';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Listado de usuarios | Centro medico</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($urlEstilos, ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
    <header class="cabecera">
        <h1>Centro medico</h1>
        <p class="subtitulo">Listado de usuarios</p>
    </header>

    <nav class="navegacion">
        <a href="<?= htmlspecialchars($urlPacientes, ENT_QUOTES, 'UTF-8') ?>">Pacientes</a>
        <a href="<?= htmlspecialchars($urlListado, ENT_QUOTES, 'UTF-8') ?>">Usuarios</a>
        <a href="<?= htmlspecialchars($urlMedicos, ENT_QUOTES, 'UTF-8') ?>">Medicos</a>
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
                Registrar usuario
            </a>
            <span class="contador">
                <?= count($filas) ?> usuario(s) registrado(s)
            </span>
        </div>

        <?php if (count($filas) === 0) : ?>
            <p class="vacio">Todavia no hay usuarios registrados en la base de datos.</p>
        <?php else : ?>
            <table class="tabla">
                <caption class="oculto">Usuarios registrados en el centro medico</caption>
                <thead>
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Usuario</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Rol</th>
                        <th scope="col">Id rol</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filas as $fila) : ?>
                        <?php
                        $idUsuario = (int) $fila['idUsuario'];
                        $urlEdicion = RUTA_BASE_URL
                            . 'index.php?modulo=usuarios&accion=edicion&id=' . $idUsuario;
                        $urlEliminar = RUTA_BASE_URL
                            . 'index.php?modulo=usuarios&accion=eliminar&id=' . $idUsuario;
                        ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars((string) $idUsuario,
                                                      ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars((string) $fila['usuLogin'],
                                                      ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars((string) $fila['usuEstado'],
                                                      ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars((string) $fila['usuRol'],
                                                      ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars((string) ($fila['roles_id_rol'] ?? ''),
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
