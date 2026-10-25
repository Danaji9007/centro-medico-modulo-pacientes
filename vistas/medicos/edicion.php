<?php

declare(strict_types=1);

/**
 * Vista de edicion de medicos.
 *
 * Presenta el formulario de edicion con los datos del medico ya cargados.
 * El identificador viaja en un campo oculto para que el controlador sepa que
 * registro debe actualizar.
 */

$urlEstilos = RUTA_ESTILOS;
$urlListado = RUTA_BASE_URL . 'index.php?modulo=medicos&accion=listado';
$urlActualizar = RUTA_BASE_URL . 'index.php?modulo=medicos&accion=actualizar';
$urlPacientes = RUTA_BASE_URL . 'index.php?modulo=pacientes&accion=listado';
$urlUsuarios = RUTA_BASE_URL . 'index.php?modulo=usuarios&accion=listado';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar medico | Centro medico</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($urlEstilos, ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
    <header class="cabecera">
        <h1>Centro medico</h1>
        <p class="subtitulo">Edicion de medico</p>
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

        <form method="post" action="<?= htmlspecialchars($urlActualizar, ENT_QUOTES, 'UTF-8') ?>"
              class="formulario">
            <input type="hidden" id="id" name="id"
                   value="<?= htmlspecialchars((string) $idMedico, ENT_QUOTES, 'UTF-8') ?>">

            <fieldset>
                <legend>Datos del medico</legend>

                <div class="campo">
                    <label for="identificacion">Numero de identificacion *</label>
                    <input type="text" id="identificacion" name="identificacion"
                           maxlength="20" required
                           value="<?= htmlspecialchars($valores['identificacion'],
                                                       ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="campo">
                    <label for="nombres">Nombres *</label>
                    <input type="text" id="nombres" name="nombres" maxlength="100" required
                           value="<?= htmlspecialchars($valores['nombres'],
                                                       ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="campo">
                    <label for="apellidos">Apellidos *</label>
                    <input type="text" id="apellidos" name="apellidos" maxlength="100" required
                           value="<?= htmlspecialchars($valores['apellidos'],
                                                       ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="campo">
                    <label for="especialidad">Especialidad *</label>
                    <input type="text" id="especialidad" name="especialidad"
                           maxlength="100" required
                           value="<?= htmlspecialchars($valores['especialidad'],
                                                       ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="campo">
                    <label for="telefono">Telefono</label>
                    <input type="tel" id="telefono" name="telefono" maxlength="20"
                           value="<?= htmlspecialchars($valores['telefono'],
                                                       ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="campo">
                    <label for="correo">Correo electronico</label>
                    <input type="email" id="correo" name="correo" maxlength="100"
                           value="<?= htmlspecialchars($valores['correo'],
                                                       ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="campo">
                    <label for="usuarios_id_usuario">Id del usuario asociado (opcional)</label>
                    <input type="number" id="usuarios_id_usuario" name="usuarios_id_usuario"
                           min="1"
                           value="<?= $valores['usuarios_id_usuario'] !== null
                               ? htmlspecialchars((string) $valores['usuarios_id_usuario'],
                                                  ENT_QUOTES, 'UTF-8')
                               : '' ?>">
                </div>
            </fieldset>

            <div class="acciones">
                <button type="submit" class="boton boton-primario">Guardar cambios</button>
                <a class="boton boton-secundario"
                   href="<?= htmlspecialchars($urlListado, ENT_QUOTES, 'UTF-8') ?>">
                    Volver al listado
                </a>
            </div>
        </form>
    </main>

    <footer class="pie">
        <p>Centro medico &middot; Evidencia GA7-220501096-AA2-EV01</p>
    </footer>
</body>
</html>
