<?php

declare(strict_types=1);

/**
 * Vista de alta de pacientes.
 *
 * Presenta el formulario de registro. Los datos que llegan a esta vista ya
 * fueron validados por el controlador y por el modelo: la vista únicamente
 * los muestra (reglas VU-01 y VU-02 del estándar de codificación).
 */

$urlEstilos = RUTA_ESTILOS;
$urlListado = RUTA_BASE_URL . 'index.php?accion=listado';
$urlRegistrar = RUTA_BASE_URL . 'index.php?accion=registrar';

$opcionSinGenero = $valores['genero'] === '' ? ' selected' : '';
$opcionMasculino = $valores['genero'] === 'M' ? ' selected' : '';
$opcionFemenino = $valores['genero'] === 'F' ? ' selected' : '';
$opcionOtro = $valores['genero'] === 'O' ? ' selected' : '';
$opcionSinTipoSangre = $valores['tipo_sangre'] === '' ? ' selected' : '';
$tiposSangre = ['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrar paciente | Centro médico</title>
    <!-- Hoja de estilos del proyecto: assets/css/estilos.css -->
    <link rel="stylesheet" href="<?= htmlspecialchars($urlEstilos, ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
    <header class="cabecera">
        <h1>Módulo de pacientes</h1>
        <p class="subtitulo">Registro de paciente</p>
    </header>

    <main class="contenido">
        <?php if ($mensajeError !== null) : ?>
            <div class="alerta alerta-error" role="alert">
                <?= htmlspecialchars($mensajeError, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= htmlspecialchars($urlRegistrar, ENT_QUOTES, 'UTF-8') ?>"
              class="formulario">
            <fieldset>
                <legend>Datos de identificación</legend>

                <div class="campo">
                    <label for="nombre">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" maxlength="100" required
                           value="<?= htmlspecialchars($valores['nombre'], ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="campo">
                    <label for="apellido">Apellido *</label>
                    <input type="text" id="apellido" name="apellido" maxlength="100" required
                           value="<?= htmlspecialchars($valores['apellido'],
                                                      ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="campo">
                    <label for="numero_identificacion">Número de identificación *</label>
                    <input type="text" id="numero_identificacion" name="numero_identificacion"
                           maxlength="20" required
                           value="<?= htmlspecialchars($valores['numero_identificacion'],
                                                      ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </fieldset>

            <fieldset>
                <legend>Datos personales</legend>

                <div class="campo">
                    <label for="fecha_nacimiento">Fecha de nacimiento</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"
                           value="<?= htmlspecialchars($valores['fecha_nacimiento'],
                                                      ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="campo">
                    <label for="genero">Género</label>
                    <select id="genero" name="genero">
                        <option value=""<?= $opcionSinGenero ?>>Sin especificar</option>
                        <option value="M"<?= $opcionMasculino ?>>Masculino</option>
                        <option value="F"<?= $opcionFemenino ?>>Femenino</option>
                        <option value="O"<?= $opcionOtro ?>>Otro</option>
                    </select>
                </div>

                <div class="campo">
                    <label for="tipo_sangre">Tipo de sangre</label>
                    <select id="tipo_sangre" name="tipo_sangre">
                        <option value=""<?= $opcionSinTipoSangre ?>>Sin especificar</option>
                        <?php foreach ($tiposSangre as $tipoSangre) : ?>
                            <?php
                            $opcionTipoSangre = $valores['tipo_sangre'] === $tipoSangre
                                ? ' selected'
                                : '';
                            ?>
                            <option
                                value="<?= htmlspecialchars($tipoSangre,
                                                       ENT_QUOTES, 'UTF-8') ?>"
                                <?= $opcionTipoSangre ?>><?= htmlspecialchars($tipoSangre,
                                    ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo">
                    <label for="direccion">Dirección</label>
                    <input type="text" id="direccion" name="direccion" maxlength="200"
                           value="<?= htmlspecialchars($valores['direccion'],
                                                      ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="campo">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" maxlength="20"
                           value="<?= htmlspecialchars($valores['telefono'],
                                                      ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="campo">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" maxlength="100"
                           value="<?= htmlspecialchars($valores['email'], ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="campo">
                    <label for="alergias">Alergias</label>
                    <textarea id="alergias" name="alergias" rows="3"
                              placeholder="Alergias registradas del paciente"
                              ><?= htmlspecialchars($valores['alergias'],
                                        ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
            </fieldset>

            <div class="acciones">
                <button type="submit" class="boton boton-primario">Registrar paciente</button>
                <a class="boton boton-secundario"
                   href="<?= htmlspecialchars($urlListado, ENT_QUOTES, 'UTF-8') ?>">
                    Volver al listado
                </a>
            </div>
        </form>
    </main>

    <footer class="pie">
        <p>Centro médico &middot; Módulo de pacientes &middot; Evidencia GA7-220501096-AA2-EV01</p>
    </footer>
</body>
</html>
