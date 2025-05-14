<?php
// Archivo: obtener_dashboard_data_api.php
// Propósito: API para obtener datos consolidados para el dashboard.

require_once 'config/db.php'; // Asegúrate de que la ruta a tu archivo de conexión sea correcta.

// Habilitar reporte de errores para depuración (puedes comentar o quitar esto en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json'); // La respuesta siempre será en formato JSON.

$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

// --- Obtener parámetros de filtro (desde la URL, método GET) ---
// Rango de fechas
$fecha_desde = $_GET['fecha_desde'] ?? null;
$fecha_hasta = $_GET['fecha_hasta'] ?? null;

// Filtros específicos por IDs de las tablas catálogo
$filtro_id_tipo_gasto = filter_input(INPUT_GET, 'id_tipo_gasto', FILTER_VALIDATE_INT);
$filtro_id_area_uso = filter_input(INPUT_GET, 'id_area_uso', FILTER_VALIDATE_INT);
$filtro_id_plataforma = filter_input(INPUT_GET, 'id_plataforma_compra', FILTER_VALIDATE_INT);

$pdo = null;
$where_clauses = []; // Array para almacenar las condiciones del WHERE
$params = []; // Array para almacenar los parámetros para la sentencia preparada

// --- Construir cláusula WHERE dinámicamente basada en los filtros ---
if (!empty($fecha_desde)) {
    // Validar formato de fecha YYYY-MM-DD
    if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fecha_desde)) {
        $response['message'] = 'Formato de fecha_desde inválido (se esperaba YYYY-MM-DD).';
        echo json_encode($response);
        exit;
    }
    $where_clauses[] = "g.fecha_compra >= :fecha_desde";
    $params[':fecha_desde'] = $fecha_desde;
}

if (!empty($fecha_hasta)) {
    // Validar formato de fecha YYYY-MM-DD
     if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fecha_hasta)) {
        $response['message'] = 'Formato de fecha_hasta inválido (se esperaba YYYY-MM-DD).';
        echo json_encode($response);
        exit;
    }
    $where_clauses[] = "g.fecha_compra <= :fecha_hasta";
    $params[':fecha_hasta'] = $fecha_hasta;
}

if ($filtro_id_tipo_gasto) {
    $where_clauses[] = "g.id_tipo_gasto = :id_tipo_gasto";
    $params[':id_tipo_gasto'] = $filtro_id_tipo_gasto;
}
if ($filtro_id_area_uso) {
    $where_clauses[] = "g.id_area_uso = :id_area_uso";
    $params[':id_area_uso'] = $filtro_id_area_uso;
}
if ($filtro_id_plataforma) {
    $where_clauses[] = "g.id_plataforma_compra = :id_plataforma_compra";
    $params[':id_plataforma_compra'] = $filtro_id_plataforma;
}

$sql_where_condition = ""; // String para la condición WHERE en las consultas SQL
if (!empty($where_clauses)) {
    $sql_where_condition = "WHERE " . implode(" AND ", $where_clauses);
}

try {
    $pdo = getPDOConnection(); // Obtener la conexión PDO desde tu archivo db.php
    $dashboard_data = []; // Array para almacenar todos los datos del dashboard

    // 1. Total de compras (monto y cantidad)
    // Se usa 'g' como alias para la tabla 'gastos'.
    $sql_total_compras = "SELECT 
                            COUNT(g.id_gasto) AS cantidad_total_compras, 
                            SUM(g.total_gasto) AS monto_total_compras 
                          FROM gastos g {$sql_where_condition}";
    $stmt_total_compras = $pdo->prepare($sql_total_compras);
    $stmt_total_compras->execute($params); // Ejecutar con los parámetros de filtro
    $dashboard_data['totales_compras'] = $stmt_total_compras->fetch(PDO::FETCH_ASSOC);
    // Asegurar que los valores no sean null si no hay resultados, inicializar a 0.
    $dashboard_data['totales_compras']['cantidad_total_compras'] = $dashboard_data['totales_compras']['cantidad_total_compras'] ?? 0;
    $dashboard_data['totales_compras']['monto_total_compras'] = $dashboard_data['totales_compras']['monto_total_compras'] ?? 0.00;


    // 2. Total con factura / sin factura (monto)
    $sql_facturas = "SELECT 
                        SUM(CASE WHEN g.tiene_factura = 1 THEN g.total_gasto ELSE 0 END) AS monto_con_factura,
                        SUM(CASE WHEN g.tiene_factura = 0 THEN g.total_gasto ELSE 0 END) AS monto_sin_factura
                     FROM gastos g {$sql_where_condition}";
    $stmt_facturas = $pdo->prepare($sql_facturas);
    $stmt_facturas->execute($params);
    $dashboard_data['totales_facturas'] = $stmt_facturas->fetch(PDO::FETCH_ASSOC);
    $dashboard_data['totales_facturas']['monto_con_factura'] = $dashboard_data['totales_facturas']['monto_con_factura'] ?? 0.00;
    $dashboard_data['totales_facturas']['monto_sin_factura'] = $dashboard_data['totales_facturas']['monto_sin_factura'] ?? 0.00;


    // 3. Total por tipo de gasto
    // Se une 'gastos' (g) con 'tipos_gasto' (tg) para obtener el nombre del tipo.
    $sql_por_tipo = "SELECT 
                        tg.nombre_tipo, 
                        SUM(g.total_gasto) AS total_por_tipo,
                        COUNT(g.id_gasto) AS cantidad_por_tipo
                     FROM gastos g
                     JOIN tipos_gasto tg ON g.id_tipo_gasto = tg.id_tipo_gasto
                     {$sql_where_condition}
                     GROUP BY tg.id_tipo_gasto, tg.nombre_tipo
                     ORDER BY total_por_tipo DESC";
    $stmt_por_tipo = $pdo->prepare($sql_por_tipo);
    $stmt_por_tipo->execute($params);
    $dashboard_data['por_tipo_gasto'] = $stmt_por_tipo->fetchAll(PDO::FETCH_ASSOC);

    // 4. Total por área de uso
    // Se une 'gastos' (g) con 'areas_uso' (au) para obtener el nombre del área.
    $sql_por_area = "SELECT 
                        au.nombre_area, 
                        SUM(g.total_gasto) AS total_por_area,
                        COUNT(g.id_gasto) AS cantidad_por_area
                     FROM gastos g
                     JOIN areas_uso au ON g.id_area_uso = au.id_area_uso
                     {$sql_where_condition}
                     GROUP BY au.id_area_uso, au.nombre_area
                     ORDER BY total_por_area DESC";
    $stmt_por_area = $pdo->prepare($sql_por_area);
    $stmt_por_area->execute($params);
    $dashboard_data['por_area_uso'] = $stmt_por_area->fetchAll(PDO::FETCH_ASSOC);

    // 5. Total por plataforma de compra
    // Se usa LEFT JOIN con 'plataformas_compra' (pc) y IFNULL para considerar también 'g.plataforma_otro'.
    $sql_por_plataforma = "SELECT 
                                IFNULL(pc.nombre_plataforma, g.plataforma_otro) AS plataforma, 
                                SUM(g.total_gasto) AS total_por_plataforma,
                                COUNT(g.id_gasto) AS cantidad_por_plataforma
                           FROM gastos g
                           LEFT JOIN plataformas_compra pc ON g.id_plataforma_compra = pc.id_plataforma_compra
                           {$sql_where_condition}
                           GROUP BY plataforma
                           HAVING plataforma IS NOT NULL AND plataforma != '' /* Evita grupos nulos o vacíos */
                           ORDER BY total_por_plataforma DESC";
    $stmt_por_plataforma = $pdo->prepare($sql_por_plataforma);
    $stmt_por_plataforma->execute($params);
    $dashboard_data['por_plataforma'] = $stmt_por_plataforma->fetchAll(PDO::FETCH_ASSOC);
    
    $response['success'] = true;
    $response['data'] = $dashboard_data; // Asignar todos los datos recopilados a la respuesta.

} catch (PDOException $e) {
    $response['message'] = "Error de base de datos: " . $e->getMessage();
    http_response_code(500); // Error interno del servidor
} catch (Exception $e) {
    $response['message'] = "Error general: " . $e->getMessage();
    http_response_code(500); // Error interno del servidor
} finally {
    // Cerrar la conexión PDO.
    if ($pdo) {
        $pdo = null;
    }
}

// Devolver la respuesta como JSON.
echo json_encode($response);
?>
