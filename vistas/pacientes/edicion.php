<?php

declare(strict_types=1);

/**
 * Vista de edicion de pacientes.
 *
 * Presenta el formulario de edicion con los datos del paciente ya cargados.
 * El identificador viaja en un campo oculto para que el controlador sepa que
 * registro debe actualizar (reglas VU-01 y VU-02 del estandar de codificacion).
 */

$urlEstilos = RUTA_ESTILOS;
$urlListado = RUTA_BASE_URL . 'index.php?modulo=pacientes&accion=listado';
$urlActualizar = RUTA_BASE_URL . 'index.php?modulo=pacientes&accion=actualizar';
$urlUsuarios = RUTA_BASE_URL . 'index.php?modulo=usuarios&accion=listado';
$urlMedicos = RUTA_BASE_URL . 'index.php?modulo=medicos&accion=listado';

$opcionSinSexo = $valores['sexo'] === '' ? ' selected' : '';
$opcionMasculino = $valores['sexo'] === 'M' ? ' selected' : '';
$opcionFemenino = $valores['sexo'] === 'F' ? ' selected' : '';
$opcionOtro = $valores['sexo'] === 'O' ? ' selected' : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar paciente | Centro medico</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($urlEstilos, ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
    <header class="cabecera">
        <h1>Centro medico</h1>
        <p class="subtitulo">Edicion de paciente</p>
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

        <form method="post" action="<?= htmlspecialchars($urlActualizar, ENT_QUOTES, 'UTF-8') ?>"
              class="formulario">
            <input type="hidden" id="id" name="id"
                   value="<?= htmlspecialchars((string) $idPaciente, ENT_QUOTES, 'UTF-8') ?>">

            <fieldset>
                <legend>Datos del paciente</legend>

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
                    <label for="fecha_nacimiento">Fecha de nacimiento</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"
                           value="<?= htmlspecialchars($valores['fecha_nacimiento'],
                                                       ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="campo">
                    <label for="sexo">Sexo</label>
                    <select id="sexo" name="sexo">
                        <option value=""<?= $opcionSinSexo ?>>Sin especificar</option>
                        <option value="M"<?= $opcionMasculino ?>>Masculino</option>
                        <option value="F"<?= $opcionFemenino ?>>Femenino</option>
                        <option value="O"<?= $opcionOtro ?>>Otro</option>
                    </select>
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
