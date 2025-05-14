<?php
// Archivo: registrar_gasto.php
// Propósito: Recibir datos del formulario, validar, manejar subida de PDF/JPG y XML, y guardar en la BD.

require_once 'config/db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => '',
    'id_gasto_insertado' => null
];

define('UPLOAD_DIR', __DIR__ . '/uploads/');
if (!is_dir(UPLOAD_DIR)) {
    if (!mkdir(UPLOAD_DIR, 0775, true)) {
        $response['message'] = 'Error: No se pudo crear el directorio de subidas: ' . UPLOAD_DIR;
        echo json_encode($response);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Error: Método de solicitud no permitido.';
    http_response_code(405); 
    echo json_encode($response);
    exit;
}

// --- Recolección de Datos del Formulario ---
$fecha_compra = $_POST['fecha_compra'] ?? null;
$id_tipo_gasto = filter_input(INPUT_POST, 'id_tipo_gasto', FILTER_VALIDATE_INT);
$id_area_uso = filter_input(INPUT_POST, 'id_area_uso', FILTER_VALIDATE_INT);
$id_tarjeta = filter_input(INPUT_POST, 'id_tarjeta', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$tarjeta_otro = trim($_POST['tarjeta_otro'] ?? '');
$precio_unitario = filter_input(INPUT_POST, 'precio_unitario', FILTER_VALIDATE_FLOAT);
$cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT);
$id_plataforma_compra = filter_input(INPUT_POST, 'id_plataforma_compra', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$plataforma_otro = trim($_POST['plataforma_otro'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$folio_factura = trim($_POST['folio_factura'] ?? '');
$tiene_factura = filter_input(INPUT_POST, 'tiene_factura', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
$tiene_xml_checkbox = filter_input(INPUT_POST, 'tiene_xml', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE); // Renombrado para claridad

// --- Validaciones (incluyendo tiene_xml_checkbox) ---
if (empty($fecha_compra) || $id_tipo_gasto === false || $id_tipo_gasto === null || $id_area_uso === false || $id_area_uso === null || $precio_unitario === false || $precio_unitario === null || $precio_unitario < 0 || $cantidad === false || $cantidad === null || $cantidad <= 0 || empty($descripcion) || $tiene_factura === null || $tiene_xml_checkbox === null) {
    $missing_fields = [];
    if (empty($fecha_compra)) $missing_fields[] = "Fecha de compra";
    if ($id_tipo_gasto === false || $id_tipo_gasto === null) $missing_fields[] = "Tipo de gasto";
    // ... (puedes añadir más campos a esta lógica de detección)
    if ($tiene_xml_checkbox === null) $missing_fields[] = "Indicación de XML";

    $response['message'] = 'Faltan campos obligatorios o tienen valores inválidos: ' . implode(', ', $missing_fields);
    echo json_encode($response);
    exit;
}

$total_gasto_calculado = round($precio_unitario * $cantidad, 2);

// --- Manejo de Subida de Archivo PDF/JPG (documento_pdf_jpg) ---
$nombre_archivo_doc = null;
$ruta_archivo_doc = null;

if (isset($_FILES['documento_pdf_jpg']) && $_FILES['documento_pdf_jpg']['error'] == UPLOAD_ERR_OK) {
    $file_tmp_path = $_FILES['documento_pdf_jpg']['tmp_name'];
    $file_name = $_FILES['documento_pdf_jpg']['name'];
    $file_size = $_FILES['documento_pdf_jpg']['size'];
    $file_name_parts = explode(".", $file_name);
    $file_extension = strtolower(end($file_name_parts));
    $allowed_extensions_doc = ['pdf', 'jpg', 'jpeg', 'png'];

    if (in_array($file_extension, $allowed_extensions_doc)) {
        if ($file_size < 5000000) { // 5MB
            $nombre_archivo_doc = "factura_" . time() . "_" . uniqid() . "." . $file_extension;
            $ruta_archivo_doc = UPLOAD_DIR . $nombre_archivo_doc;
            if (!move_uploaded_file($file_tmp_path, $ruta_archivo_doc)) {
                $response['message'] = 'Error al mover el archivo PDF/JPG subido.';
                echo json_encode($response);
                exit;
            }
        } else { /* ... manejo de error de tamaño ... */ }
    } else { /* ... manejo de error de extensión ... */ }
} elseif (isset($_FILES['documento_pdf_jpg']) && $_FILES['documento_pdf_jpg']['error'] != UPLOAD_ERR_NO_FILE) {
    /* ... manejo de otros errores de subida ... */
}

// ***** NUEVO: Manejo de Subida de Archivo XML (documento_xml) *****
$nombre_archivo_xml = null;
$ruta_archivo_xml = null;

if (isset($_FILES['documento_xml']) && $_FILES['documento_xml']['error'] == UPLOAD_ERR_OK) {
    $xml_file_tmp_path = $_FILES['documento_xml']['tmp_name'];
    $xml_file_name = $_FILES['documento_xml']['name'];
    $xml_file_size = $_FILES['documento_xml']['size'];
    $xml_file_name_parts = explode(".", $xml_file_name);
    $xml_file_extension = strtolower(end($xml_file_name_parts));
    $allowed_extensions_xml = ['xml'];

    if (in_array($xml_file_extension, $allowed_extensions_xml)) {
        if ($xml_file_size < 2000000) { // 2MB para XMLs, por ejemplo
            $nombre_archivo_xml = "xml_" . time() . "_" . uniqid() . "." . $xml_file_extension;
            $ruta_archivo_xml = UPLOAD_DIR . $nombre_archivo_xml;
            if (!move_uploaded_file($xml_file_tmp_path, $ruta_archivo_xml)) {
                $response['message'] = 'Error al mover el archivo XML subido.';
                // Considerar si se debe eliminar el PDF/JPG si este falla
                if ($ruta_archivo_doc && file_exists($ruta_archivo_doc)) {
                    unlink($ruta_archivo_doc);
                }
                echo json_encode($response);
                exit;
            }
        } else {
            $response['message'] = 'Error: El archivo XML es demasiado grande. Máximo 2MB.';
            echo json_encode($response);
            exit;
        }
    } else {
        $response['message'] = 'Error: Extensión de archivo XML no permitida. Solo se permite .xml.';
        echo json_encode($response);
        exit;
    }
} elseif (isset($_FILES['documento_xml']) && $_FILES['documento_xml']['error'] != UPLOAD_ERR_NO_FILE) {
    $response['message'] = 'Error al subir el archivo XML. Código: ' . $_FILES['documento_xml']['error'];
    echo json_encode($response);
    exit;
}


// --- Inserción en la Base de Datos ---
$pdo = null;
try {
    $pdo = getPDOConnection();

    // ***** MODIFICADO: Añadir nombre_archivo_xml y ruta_archivo_xml a la consulta SQL *****
    $sql = "INSERT INTO gastos (
                fecha_compra, id_tipo_gasto, id_area_uso, id_tarjeta, tarjeta_otro,
                precio_unitario, cantidad, total_gasto, id_plataforma_compra, plataforma_otro,
                descripcion, folio_factura, tiene_factura, tiene_xml, 
                nombre_archivo_doc, ruta_archivo_doc,
                nombre_archivo_xml, ruta_archivo_xml, /* <-- NUEVOS CAMPOS */
                id_usuario_registro 
            ) VALUES (
                :fecha_compra, :id_tipo_gasto, :id_area_uso, :id_tarjeta, :tarjeta_otro,
                :precio_unitario, :cantidad, :total_gasto, :id_plataforma_compra, :plataforma_otro,
                :descripcion, :folio_factura, :tiene_factura, :tiene_xml_checkbox, 
                :nombre_archivo_doc, :ruta_archivo_doc,
                :nombre_archivo_xml, :ruta_archivo_xml, /* <-- NUEVOS PARÁMETROS */
                NULL 
            )";

    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':fecha_compra', $fecha_compra);
    $stmt->bindParam(':id_tipo_gasto', $id_tipo_gasto, PDO::PARAM_INT);
    // ... (otros binds existentes) ...
    $stmt->bindParam(':id_area_uso', $id_area_uso, PDO::PARAM_INT);
    $stmt->bindParam(':id_tarjeta', $id_tarjeta, $id_tarjeta === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    $stmt->bindParam(':tarjeta_otro', $tarjeta_otro, empty($tarjeta_otro) ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindParam(':precio_unitario', $precio_unitario); 
    $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
    $stmt->bindParam(':total_gasto', $total_gasto_calculado);
    $stmt->bindParam(':id_plataforma_compra', $id_plataforma_compra, $id_plataforma_compra === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    $stmt->bindParam(':plataforma_otro', $plataforma_otro, empty($plataforma_otro) ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':folio_factura', $folio_factura, empty($folio_factura) ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindParam(':tiene_factura', $tiene_factura, PDO::PARAM_BOOL);
    $stmt->bindParam(':tiene_xml_checkbox', $tiene_xml_checkbox, PDO::PARAM_BOOL); // Usar la variable correcta
    
    $stmt->bindParam(':nombre_archivo_doc', $nombre_archivo_doc, $nombre_archivo_doc === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindParam(':ruta_archivo_doc', $ruta_archivo_doc, $ruta_archivo_doc === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    
    // ***** NUEVO: Bind para los campos del archivo XML *****
    $stmt->bindParam(':nombre_archivo_xml', $nombre_archivo_xml, $nombre_archivo_xml === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindParam(':ruta_archivo_xml', $ruta_archivo_xml, $ruta_archivo_xml === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Gasto registrado exitosamente.';
        $response['id_gasto_insertado'] = $pdo->lastInsertId();
    } else {
        $response['message'] = 'Error al registrar el gasto en la base de datos.';
        // Si falla el guardado en BD, eliminar archivos subidos para no dejar huérfanos
        if ($ruta_archivo_doc && file_exists($ruta_archivo_doc)) unlink($ruta_archivo_doc);
        if ($ruta_archivo_xml && file_exists($ruta_archivo_xml)) unlink($ruta_archivo_xml);
    }

} catch (PDOException $e) {
    $response['message'] = "Error de base de datos al registrar: " . $e->getMessage();
    http_response_code(500);
    // Eliminar archivos si la BD falló
    if ($ruta_archivo_doc && file_exists($ruta_archivo_doc)) unlink($ruta_archivo_doc);
    if ($ruta_archivo_xml && file_exists($ruta_archivo_xml)) unlink($ruta_archivo_xml);
} catch (Exception $e) {
    $response['message'] = "Error general al registrar: " . $e->getMessage();
    http_response_code(500);
    if ($ruta_archivo_doc && file_exists($ruta_archivo_doc)) unlink($ruta_archivo_doc);
    if ($ruta_archivo_xml && file_exists($ruta_archivo_xml)) unlink($ruta_archivo_xml);
} finally {
    if ($pdo) {
        $pdo = null;
    }
}

echo json_encode($response);
?>
