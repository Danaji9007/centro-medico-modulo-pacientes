<?php

declare(strict_types=1);

/**
 * Vista del listado de pacientes.
 *
 * Presenta una fila por cada paciente registrado. Todos los valores se
 * escapan antes de imprimirse (regla SE-03) y las operaciones de edicion y
 * eliminacion apuntan al controlador (regla VU-02).
 */

$urlEstilos = RUTA_ESTILOS;
$urlListado = RUTA_BASE_URL . 'index.php?modulo=pacientes&accion=listado';
$urlRegistro = RUTA_BASE_URL . 'index.php?modulo=pacientes&accion=registro';
$urlUsuarios = RUTA_BASE_URL . 'index.php?modulo=usuarios&accion=listado';
$urlMedicos = RUTA_BASE_URL . 'index.php?modulo=medicos&accion=listado';

$etiquetasSexo = [
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
    <title>Listado de pacientes | Centro medico</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($urlEstilos, ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
    <header class="cabecera">
        <h1>Centro medico</h1>
        <p class="subtitulo">Listado de pacientes</p>
    </header>

    <nav class="navegacion">
        <a href="<?= htmlspecialchars($urlListado, ENT_QUOTES, 'UTF-8') ?>">Pacientes</a>
        <a href="<?= htmlspecialchars($urlUsuarios, ENT_QUOTES, 'UTF-8') ?>">Usuarios</a>
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
                Registrar paciente
            </a>
            <span class="contador">
                <?= count($filas) ?> paciente(s) registrado(s)
            </span>
        </div>

        <?php if (count($filas) === 0) : ?>
            <p class="vacio">Todavia no hay pacientes registrados en la base de datos.</p>
        <?php else : ?>
            <table class="tabla">
                <caption class="oculto">Pacientes registrados en el centro medico</caption>
                <thead>
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Identificacion</th>
                        <th scope="col">Nombres</th>
                        <th scope="col">Apellidos</th>
                        <th scope="col">Fecha de nacimiento</th>
                        <th scope="col">Sexo</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filas as $fila) : ?>
                        <?php
                        $idPaciente = (int) $fila['idPaciente'];
                        $urlEdicion = RUTA_BASE_URL
                            . 'index.php?modulo=pacientes&accion=edicion&id=' . $idPaciente;
                        $urlEliminar = RUTA_BASE_URL
                            . 'index.php?modulo=pacientes&accion=eliminar&id=' . $idPaciente;
                        ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars((string) $idPaciente,
                                                      ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars((string) $fila['pacIdentificacion'],
                                                      ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars((string) $fila['pacNombres'],
                                                      ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars((string) $fila['pacApellidos'],
                                                      ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars((string) ($fila['pacFechaNacimiento'] ?? ''),
                                                      ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars(
                                    $etiquetasSexo[(string) ($fila['pacSexo'] ?? '')]
                                        ?? 'Sin especificar',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
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
