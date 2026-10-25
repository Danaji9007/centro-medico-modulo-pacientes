<?php

declare(strict_types=1);

/**
 * Vista de edicion de usuarios.
 *
 * Presenta el formulario de edicion con los datos del usuario ya cargados.
 * El identificador viaja en un campo oculto para que el controlador sepa que
 * registro debe actualizar.
 */

$urlEstilos = RUTA_ESTILOS;
$urlListado = RUTA_BASE_URL . 'index.php?modulo=usuarios&accion=listado';
$urlActualizar = RUTA_BASE_URL . 'index.php?modulo=usuarios&accion=actualizar';
$urlPacientes = RUTA_BASE_URL . 'index.php?modulo=pacientes&accion=listado';
$urlMedicos = RUTA_BASE_URL . 'index.php?modulo=medicos&accion=listado';

$estados = ['Activo', 'Inactivo'];
$roles = ['Administrador', 'Medico', 'Recepcionista', 'Paciente'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar usuario | Centro medico</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($urlEstilos, ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
    <header class="cabecera">
        <h1>Centro medico</h1>
        <p class="subtitulo">Edicion de usuario</p>
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

        <form method="post" action="<?= htmlspecialchars($urlActualizar, ENT_QUOTES, 'UTF-8') ?>"
              class="formulario">
            <input type="hidden" id="id" name="id"
                   value="<?= htmlspecialchars((string) $idUsuario, ENT_QUOTES, 'UTF-8') ?>">

            <fieldset>
                <legend>Datos del usuario</legend>

                <div class="campo">
                    <label for="usuario">Nombre de usuario *</label>
                    <input type="text" id="usuario" name="usuario" maxlength="50" required
                           value="<?= htmlspecialchars($valores['usuario'],
                                                       ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="campo">
                    <label for="password">Contrasena *</label>
                    <input type="password" id="password" name="password" minlength="6" required>
                    <small>En la edicion se debe ingresar y re-cifrar la contrasena.</small>
                </div>

                <div class="campo">
                    <label for="estado">Estado *</label>
                    <select id="estado" name="estado" required>
                        <?php foreach ($estados as $estado) : ?>
                            <?php $seleccionado = $valores['estado'] === $estado ? ' selected' : ''; ?>
                            <option value="<?= htmlspecialchars($estado, ENT_QUOTES, 'UTF-8') ?>"
                                <?= $seleccionado ?>><?= htmlspecialchars($estado,
                                    ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo">
                    <label for="rol">Rol *</label>
                    <select id="rol" name="rol" required>
                        <?php foreach ($roles as $rol) : ?>
                            <?php $seleccionado = $valores['rol'] === $rol ? ' selected' : ''; ?>
                            <option value="<?= htmlspecialchars($rol, ENT_QUOTES, 'UTF-8') ?>"
                                <?= $seleccionado ?>><?= htmlspecialchars($rol,
                                    ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo">
                    <label for="roles_id_rol">Id del rol (opcional)</label>
                    <input type="number" id="roles_id_rol" name="roles_id_rol" min="1"
                           value="<?= $valores['roles_id_rol'] !== null
                               ? htmlspecialchars((string) $valores['roles_id_rol'],
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
