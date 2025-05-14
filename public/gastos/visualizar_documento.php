<?php
// Archivo: visualizar_documento.php
// Propósito: Mostrar o forzar la descarga de un archivo adjunto.

// Habilitar reporte de errores para depuración (opcional, quitar en producción)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// --- Directorio de Subida de Archivos ---
// Debe coincidir con el definido en registrar_gasto.php
define('UPLOAD_DIR', __DIR__ . '/uploads/');

// --- Obtener parámetros de la URL ---
$nombre_archivo = $_GET['archivo'] ?? null;
$forzar_descarga = isset($_GET['descargar']) && $_GET['descargar'] == '1';

if (empty($nombre_archivo)) {
    http_response_code(400); // Bad Request
    die("Error: Nombre de archivo no especificado.");
}

// --- Validar el nombre del archivo para evitar Path Traversal ---
// Asegurarse de que el nombre del archivo no contenga '..' o barras '/' o '\'
// que podrían permitir acceder a directorios superiores.
if (strpos($nombre_archivo, '..') !== false || strpos($nombre_archivo, '/') !== false || strpos($nombre_archivo, '\\') !== false) {
    http_response_code(400); // Bad Request
    die("Error: Nombre de archivo inválido.");
}

$ruta_completa_archivo = UPLOAD_DIR . basename($nombre_archivo); // basename() es una capa adicional de seguridad

// --- Verificar si el archivo existe y es legible ---
if (!file_exists($ruta_completa_archivo) || !is_readable($ruta_completa_archivo)) {
    http_response_code(404); // Not Found
    die("Error: El archivo '" . htmlspecialchars($nombre_archivo) . "' no se encuentra o no es accesible.");
}

// --- Determinar el tipo MIME del archivo ---
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$tipo_mime = finfo_file($finfo, $ruta_completa_archivo);
finfo_close($finfo);

if ($tipo_mime === false) {
    // Si no se puede determinar el tipo MIME, usar un genérico para descarga
    $tipo_mime = 'application/octet-stream';
}

// --- Configurar Cabeceras HTTP ---

// Limpiar cualquier salida previa que pueda interferir con las cabeceras o el archivo
if (ob_get_level()) {
    ob_end_clean();
}

if ($forzar_descarga) {
    // Forzar descarga
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream'); // Tipo genérico para forzar descarga
    header('Content-Disposition: attachment; filename="' . basename($ruta_completa_archivo) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($ruta_completa_archivo));
} else {
    // Intentar mostrar en el navegador
    header('Content-Type: ' . $tipo_mime);
    header('Content-Disposition: inline; filename="' . basename($ruta_completa_archivo) . '"');
    // Cache control para visualización (opcional)
    // header('Cache-Control: public, max-age=3600'); // Cachear por 1 hora
    header('Content-Length: ' . filesize($ruta_completa_archivo));
}

// --- Enviar el contenido del archivo ---
// Usar readfile() es eficiente para enviar archivos.
if (!readfile($ruta_completa_archivo)) {
    // Si readfile falla, puede que ya se hayan enviado cabeceras o haya otro problema.
    http_response_code(500); // Internal Server Error
    // No se puede enviar 'die()' aquí si readfile ya empezó a enviar el archivo.
    // Se podría loguear un error.
}
exit; // Asegurar que no se ejecute más código después de enviar el archivo.
?>
