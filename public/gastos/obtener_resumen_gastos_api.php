<?php
// Archivo: obtener_resumen_gastos_api.php
// Propósito: API para obtener un reporte anual de gastos agrupados por plataforma, con totales mensuales.

require_once 'config/db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => '',
    'reporte_anual' => null,
    'anio_consultado' => null,
    'meses_nombres' => [] // Para los encabezados de la tabla
];

// Obtener parámetro del año
$anio = filter_input(INPUT_GET, 'anio', FILTER_VALIDATE_INT);

if ($anio === false || $anio === null || $anio < 2000 || $anio > date("Y") + 5) {
    $response['message'] = 'Año no válido o no proporcionado. Por favor, ingrese un año válido.';
    echo json_encode($response);
    exit;
}
$response['anio_consultado'] = $anio;

// Nombres de los meses para los encabezados (puedes localizar esto si es necesario)
$response['meses_nombres'] = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

$pdo = null;

try {
    $pdo = getPDOConnection();

    // Consulta principal para obtener los gastos mensuales por plataforma para el año dado
    $sql = "SELECT 
                IFNULL(pc.nombre_plataforma, g.plataforma_otro) AS plataforma,
                MONTH(g.fecha_compra) AS mes_numero,
                SUM(g.total_gasto) AS total_mensual_plataforma
            FROM gastos g
            LEFT JOIN plataformas_compra pc ON g.id_plataforma_compra = pc.id_plataforma_compra
            WHERE YEAR(g.fecha_compra) = :anio 
                  AND (pc.nombre_plataforma IS NOT NULL OR g.plataforma_otro IS NOT NULL AND g.plataforma_otro != '')
            GROUP BY plataforma, mes_numero
            ORDER BY plataforma, mes_numero";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
    $stmt->execute();
    $resultados_crudos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Procesar los resultados para pivotar los datos:
    // Queremos una estructura donde cada plataforma sea una fila,
    // y las columnas sean los meses con sus totales, más un total anual.
    $reporte_procesado = [];
    foreach ($resultados_crudos as $fila) {
        $plataforma = $fila['plataforma'];
        $mes_numero = (int)$fila['mes_numero']; // 1-12
        $total_mensual = (float)$fila['total_mensual_plataforma'];

        // Inicializar la plataforma si no existe en el array de reporte
        if (!isset($reporte_procesado[$plataforma])) {
            $reporte_procesado[$plataforma] = [
                'plataforma' => $plataforma,
                'gastos_mensuales' => array_fill(1, 12, 0.00), // Array de 12 meses inicializado a 0.00
                'total_anual_plataforma' => 0.00
            ];
        }

        // Asignar el total al mes correspondiente
        if ($mes_numero >= 1 && $mes_numero <= 12) {
            $reporte_procesado[$plataforma]['gastos_mensuales'][$mes_numero] = $total_mensual;
        }
        // Sumar al total anual de la plataforma
        $reporte_procesado[$plataforma]['total_anual_plataforma'] += $total_mensual;
    }

    // Convertir el array asociativo a un array indexado para el JSON final
    // y ordenar por el nombre de la plataforma (opcional)
    $response['reporte_anual'] = array_values($reporte_procesado);
    // Podrías añadir un sort aquí si quieres que las plataformas salgan ordenadas alfabéticamente
    // usort($response['reporte_anual'], function($a, $b) {
    //     return strcmp($a['plataforma'], $b['plataforma']);
    // });


    $response['success'] = true;

} catch (PDOException $e) {
    $response['message'] = "Error de base de datos: " . $e->getMessage();
    http_response_code(500);
} catch (Exception $e) {
    $response['message'] = "Error general: " . $e->getMessage();
    http_response_code(500);
} finally {
    if ($pdo) {
        $pdo = null;
    }
}

echo json_encode($response);
?>
