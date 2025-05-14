<?php
// Archivo: obtener_catalogos.php
// Propósito: Obtener los datos de las tablas de catálogo y devolverlos como JSON.

// Incluir el archivo de configuración de la base de datos
require_once 'config/db.php';

// Habilitar reporte de errores para depuración (opcional, quitar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json'); // Indicar que la respuesta será JSON

$pdo = null;
$response = [
    'success' => false,
    'data' => [
        'tipos_gasto' => [],
        'areas_uso' => [],
        'tarjetas' => [],
        'plataformas_compra' => []
    ],
    'message' => ''
];

try {
    $pdo = getPDOConnection();

    // Obtener Tipos de Gasto
    $stmt_tipos = $pdo->query("SELECT id_tipo_gasto, nombre_tipo FROM tipos_gasto ORDER BY nombre_tipo ASC");
    $response['data']['tipos_gasto'] = $stmt_tipos->fetchAll(PDO::FETCH_ASSOC);

    // Obtener Áreas de Uso
    $stmt_areas = $pdo->query("SELECT id_area_uso, nombre_area FROM areas_uso ORDER BY nombre_area ASC");
    $response['data']['areas_uso'] = $stmt_areas->fetchAll(PDO::FETCH_ASSOC);

    // Obtener Tarjetas (opcional)
    $stmt_tarjetas = $pdo->query("SELECT id_tarjeta, nombre_tarjeta FROM tarjetas ORDER BY nombre_tarjeta ASC");
    $response['data']['tarjetas'] = $stmt_tarjetas->fetchAll(PDO::FETCH_ASSOC);

    // Obtener Plataformas de Compra (opcional)
    $stmt_plataformas = $pdo->query("SELECT id_plataforma_compra, nombre_plataforma FROM plataformas_compra ORDER BY nombre_plataforma ASC");
    $response['data']['plataformas_compra'] = $stmt_plataformas->fetchAll(PDO::FETCH_ASSOC);

    $response['success'] = true;

} catch (PDOException $e) {
    $response['message'] = "Error de base de datos: " . $e->getMessage();
    // En un entorno real, no exponer e->getMessage() directamente al cliente por seguridad.
    // Considera loguear el error y dar un mensaje genérico.
    http_response_code(500); // Internal Server Error
} catch (Exception $e) {
    $response['message'] = "Error general: " . $e->getMessage();
    http_response_code(500); // Internal Server Error
} finally {
    if ($pdo) {
        $pdo = null; // Cerrar la conexión
    }
}

echo json_encode($response);
?>
