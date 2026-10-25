<?php

declare(strict_types=1);

/**
 * Vista del listado de pacientes.
 *
 * Presenta una fila por cada paciente registrado. Todos los valores se
 * escapan antes de imprimirse (regla SE-03) y las operaciones de edición y
 * eliminación apuntan al controlador (regla VU-02).
 */

$urlEstilos = RUTA_ESTILOS;
$urlListado = RUTA_BASE_URL . 'index.php?accion=listado';
$urlRegistro = RUTA_BASE_URL . 'index.php?accion=registro';

$etiquetasGenero = [
    'M' => 'Masculino',
    'F' => 'Femenino',
    'O' => 'Otro',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Listado de pacientes | Centro médico</title>
    <!-- Hoja de estilos del proyecto: assets/css/estilos.css -->
    <link rel="stylesheet" href="<?= htmlspecialchars($urlEstilos, ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
    <header class="cabecera">
        <h1>Módulo de pacientes</h1>
        <p class="subtitulo">Listado de pacientes</p>
    </header>

    <main class="contenido">
        <?php if ($mensajeError !== null) : ?>
            <div class="alerta alerta-error" role="alert">
                <?= htmlspecialchars($mensajeError, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <div class="barra-acciones">
            <a class="boton boton-primario"
               href="<?= htmlspecialchars($urlRegistro, ENT_QUOTES, 'UTF-8') ?>">
                Registrar paciente
            </a>
            <span class="contador">
                <?= count($filas) ?> paciente(s) registrado(s)
            </span>
        </div>

        <?php if (count($filas) === 0) : ?>
            <p class="vacio">Todavía no hay pacientes registrados en la base de datos.</p>
        <?php else : ?>
            <table class="tabla">
                <caption class="oculto">Pacientes registrados en el centro médico</caption>
                <thead>
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Apellido</th>
                        <th scope="col">Identificación</th>
                        <th scope="col">Fecha de nacimiento</th>
                        <th scope="col">Género</th>
                        <th scope="col">Teléfono</th>
                        <th scope="col">Correo electrónico</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filas as $fila) : ?>
                        <?php
                        $idPaciente = (int) $fila['id_paciente'];
                        $urlEdicion = RUTA_BASE_URL . 'index.php?accion=edicion&id=' . $idPaciente;
                        $urlEliminar = RUTA_BASE_URL
                            . 'index.php?accion=eliminar&id=' . $idPaciente;
                        ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars((string) $idPaciente,
                                                      ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars((string) $fila['nombre'],
                                                      ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars((string) $fila['apellido'],
                                                      ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars((string) $fila['numero_identificacion'],
                                                      ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars((string) ($fila['fecha_nacimiento'] ?? ''),
                                                      ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars(
                                    $etiquetasGenero[(string) ($fila['genero'] ?? '')]
                                        ?? 'Sin especificar',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars((string) ($fila['telefono'] ?? ''),
                                                      ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars((string) ($fila['email'] ?? ''),
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
        <p>Centro médico &middot; Módulo de pacientes &middot; Evidencia GA7-220501096-AA2-EV01</p>
    </footer>
</body>
</html>
