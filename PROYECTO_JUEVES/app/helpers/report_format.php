<?php
function estadosReporte() {
    return [
        'pendiente' => 'Pendiente',
        'en_revision' => 'En revision',
        'en_proceso' => 'En proceso',
        'resuelto' => 'Resuelto',
        'rechazado' => 'Rechazado',
    ];
}

function prioridadesReporte() {
    return [
        'baja' => 'Baja',
        'media' => 'Media',
        'alta' => 'Alta',
    ];
}

function estadoReporteLabel($estado) {
    $estados = estadosReporte();
    return $estados[$estado] ?? ucfirst(str_replace('_', ' ', $estado));
}

function prioridadReporteLabel($prioridad) {
    $prioridades = prioridadesReporte();
    return $prioridades[$prioridad] ?? ucfirst($prioridad);
}

function estadoReporteClass($estado) {
    $clases = [
        'pendiente' => 'status-pendiente',
        'en_revision' => 'status-revision',
        'en_proceso' => 'status-proceso',
        'resuelto' => 'status-resuelto',
        'rechazado' => 'status-rechazado',
    ];

    return $clases[$estado] ?? 'status-pendiente';
}

function prioridadReporteClass($prioridad) {
    $clases = [
        'baja' => 'priority-baja',
        'media' => 'priority-media',
        'alta' => 'priority-alta',
    ];

    return $clases[$prioridad] ?? 'priority-media';
}

function esImagenReporte($url) {
    $url = trim((string) $url);
    if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }

    return (bool) preg_match('/\.(jpg|jpeg|png|gif|webp)(\?.*)?$/i', $url);
}

function imagenReporteHtml($url, $alt = 'Evidencia del reporte') {
    if (!esImagenReporte($url)) {
        return '';
    }

    $urlSeguro = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    $altSeguro = htmlspecialchars($alt, ENT_QUOTES, 'UTF-8');
    return '<a class="report-image-link" href="' . $urlSeguro . '" target="_blank" rel="noopener"><img class="report-image" src="' . $urlSeguro . '" alt="' . $altSeguro . '" loading="lazy"></a>';
}
