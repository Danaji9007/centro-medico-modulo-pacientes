<?php

declare(strict_types=1);

/**
 * Vista de confirmación de una operación exitosa.
 *
 * Informa el resultado del alta, la actualización o la eliminación y ofrece
 * el enlace de regreso al listado (regla VU-02 del estándar de codificación).
 */

$urlEstilos = RUTA_ESTILOS;
$urlListado = RUTA_BASE_URL . 'index.php?accion=listado';
$urlRegistro = RUTA_BASE_URL . 'index.php?accion=registro';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Operación realizada | Centro médico</title>
    <!-- Hoja de estilos del proyecto: assets/css/estilos.css -->
    <link rel="stylesheet" href="<?= htmlspecialchars($urlEstilos, ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
    <header class="cabecera">
        <h1>Módulo de pacientes</h1>
        <p class="subtitulo"><?= htmlspecialchars($accionRealizada, ENT_QUOTES, 'UTF-8') ?></p>
    </header>

    <main class="contenido">
        <div class="alerta alerta-exito" role="status">
            <?= htmlspecialchars($mensajeExito, ENT_QUOTES, 'UTF-8') ?>
        </div>

        <div class="acciones">
            <a class="boton boton-primario"
               href="<?= htmlspecialchars($urlListado, ENT_QUOTES, 'UTF-8') ?>">
                Volver al listado
            </a>
            <a class="boton boton-secundario"
               href="<?= htmlspecialchars($urlRegistro, ENT_QUOTES, 'UTF-8') ?>">
                Registrar otro paciente
            </a>
        </div>
    </main>

    <footer class="pie">
        <p>Centro médico &middot; Módulo de pacientes &middot; Evidencia GA7-220501096-AA2-EV01</p>
    </footer>
</body>
</html>
