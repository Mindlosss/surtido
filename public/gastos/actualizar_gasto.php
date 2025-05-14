<?php
// Archivo: actualizar_gasto.php
// Propósito: Recibir datos del formulario de edición, validar, manejar subida/reemplazo de archivos y actualizar en la BD.

require_once 'config/db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// NO enviaremos respuesta JSON aquí, redirigiremos con mensajes GET.
// header('Content-Type: application/json'); 
// $response = [ ... ]; // No necesario si redirigimos

// --- Directorio de Subida de Archivos ---
define('UPLOAD_DIR', __DIR__ . '/uploads/');
if (!is_dir(UPLOAD_DIR)) {
    // Intentar crear el directorio (aunque debería existir por registrar_gasto.php)
    if (!mkdir(UPLOAD_DIR, 0775, true)) {
        header('Location: listar_gastos.php?mensaje_error=' . urlencode('Error crítico: No se pudo crear el directorio de subidas.'));
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listar_gastos.php?mensaje_error=' . urlencode('Error: Método de solicitud no permitido.'));
    exit;
}

// --- Recolección de Datos del Formulario ---
$id_gasto_a_actualizar = filter_input(INPUT_POST, 'id_gasto', FILTER_VALIDATE_INT);

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
$tiene_xml_checkbox = filter_input(INPUT_POST, 'tiene_xml', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

// Nombres y rutas de archivos actuales (enviados como hidden inputs)
$nombre_archivo_doc_actual = $_POST['documento_pdf_jpg_actual'] ?? null;
$ruta_archivo_doc_actual = $_POST['ruta_documento_pdf_jpg_actual'] ?? null;
$nombre_archivo_xml_actual = $_POST['documento_xml_actual'] ?? null;
$ruta_archivo_xml_actual = $_POST['ruta_documento_xml_actual'] ?? null;


// --- Validaciones ---
if (!$id_gasto_a_actualizar) {
    header('Location: listar_gastos.php?mensaje_error=' . urlencode('Error: ID de gasto para actualizar no proporcionado.'));
    exit;
}
// Añadir aquí las mismas validaciones que en registrar_gasto.php para los campos obligatorios
if (empty($fecha_compra) || $id_tipo_gasto === false || $id_tipo_gasto === null /* ... etc ... */ || $tiene_xml_checkbox === null) {
    header('Location: editar_gasto.php?id=' . $id_gasto_a_actualizar . '&mensaje_error=' . urlencode('Faltan campos obligatorios o tienen valores inválidos.'));
    exit;
}

$total_gasto_calculado = round($precio_unitario * $cantidad, 2);

// Variables para los nuevos archivos (si se suben)
$nuevo_nombre_archivo_doc = $nombre_archivo_doc_actual;
$nueva_ruta_archivo_doc = $ruta_archivo_doc_actual;
$nuevo_nombre_archivo_xml = $nombre_archivo_xml_actual;
$nueva_ruta_archivo_xml = $ruta_archivo_xml_actual;

$mensaje_operacion = ''; // Para acumular mensajes de subida/borrado de archivos

// --- Manejo de Subida de NUEVO Archivo PDF/JPG (documento_pdf_jpg) ---
if (isset($_FILES['documento_pdf_jpg']) && $_FILES['documento_pdf_jpg']['error'] == UPLOAD_ERR_OK) {
    // ... (lógica de validación de extensión y tamaño igual que en registrar_gasto.php)
    $file_tmp_path = $_FILES['documento_pdf_jpg']['tmp_name'];
    $file_name = $_FILES['documento_pdf_jpg']['name'];
    $file_size = $_FILES['documento_pdf_jpg']['size'];
    $file_name_parts = explode(".", $file_name);
    $file_extension = strtolower(end($file_name_parts));
    $allowed_extensions_doc = ['pdf', 'jpg', 'jpeg', 'png'];

    if (in_array($file_extension, $allowed_extensions_doc)) {
        if ($file_size < 5000000) { // 5MB
            // Eliminar archivo PDF/JPG anterior si existe
            if (!empty($ruta_archivo_doc_actual) && file_exists($ruta_archivo_doc_actual)) {
                unlink($ruta_archivo_doc_actual);
                $mensaje_operacion .= " Documento PDF/JPG anterior eliminado.";
            }
            $nuevo_nombre_archivo_doc = "factura_" . time() . "_" . uniqid() . "." . $file_extension;
            $nueva_ruta_archivo_doc = UPLOAD_DIR . $nuevo_nombre_archivo_doc;
            if (!move_uploaded_file($file_tmp_path, $nueva_ruta_archivo_doc)) {
                header('Location: editar_gasto.php?id=' . $id_gasto_a_actualizar . '&mensaje_error=' . urlencode('Error al mover el nuevo archivo PDF/JPG.'));
                exit;
            }
        } else { /* Error tamaño */ 
            header('Location: editar_gasto.php?id=' . $id_gasto_a_actualizar . '&mensaje_error=' . urlencode('Error: El nuevo archivo PDF/JPG es demasiado grande.'));
            exit;
        }
    } else { /* Error extensión */ 
        header('Location: editar_gasto.php?id=' . $id_gasto_a_actualizar . '&mensaje_error=' . urlencode('Error: Extensión no permitida para el nuevo archivo PDF/JPG.'));
        exit;
    }
} elseif (isset($_FILES['documento_pdf_jpg']) && $_FILES['documento_pdf_jpg']['error'] != UPLOAD_ERR_NO_FILE) {
    header('Location: editar_gasto.php?id=' . $id_gasto_a_actualizar . '&mensaje_error=' . urlencode('Error al subir el nuevo archivo PDF/JPG. Código: '.$_FILES['documento_pdf_jpg']['error']));
    exit;
}


// --- Manejo de Subida de NUEVO Archivo XML (documento_xml) ---
if (isset($_FILES['documento_xml']) && $_FILES['documento_xml']['error'] == UPLOAD_ERR_OK) {
    // ... (lógica de validación de extensión y tamaño igual que en registrar_gasto.php)
    $xml_file_tmp_path = $_FILES['documento_xml']['tmp_name'];
    $xml_file_name = $_FILES['documento_xml']['name'];
    $xml_file_size = $_FILES['documento_xml']['size'];
    $xml_file_name_parts = explode(".", $xml_file_name);
    $xml_file_extension = strtolower(end($xml_file_name_parts));
    $allowed_extensions_xml = ['xml'];

    if (in_array($xml_file_extension, $allowed_extensions_xml)) {
        if ($xml_file_size < 2000000) { // 2MB
            // Eliminar archivo XML anterior si existe
            if (!empty($ruta_archivo_xml_actual) && file_exists($ruta_archivo_xml_actual)) {
                unlink($ruta_archivo_xml_actual);
                $mensaje_operacion .= " Archivo XML anterior eliminado.";
            }
            $nuevo_nombre_archivo_xml = "xml_" . time() . "_" . uniqid() . "." . $xml_file_extension;
            $nueva_ruta_archivo_xml = UPLOAD_DIR . $nuevo_nombre_archivo_xml;
            if (!move_uploaded_file($xml_file_tmp_path, $nueva_ruta_archivo_xml)) {
                 header('Location: editar_gasto.php?id=' . $id_gasto_a_actualizar . '&mensaje_error=' . urlencode('Error al mover el nuevo archivo XML.'));
                exit;
            }
        } else { /* Error tamaño */ 
            header('Location: editar_gasto.php?id=' . $id_gasto_a_actualizar . '&mensaje_error=' . urlencode('Error: El nuevo archivo XML es demasiado grande.'));
            exit;
        }
    } else { /* Error extensión */ 
        header('Location: editar_gasto.php?id=' . $id_gasto_a_actualizar . '&mensaje_error=' . urlencode('Error: Extensión no permitida para el nuevo archivo XML.'));
        exit;
    }
} elseif (isset($_FILES['documento_xml']) && $_FILES['documento_xml']['error'] != UPLOAD_ERR_NO_FILE) {
     header('Location: editar_gasto.php?id=' . $id_gasto_a_actualizar . '&mensaje_error=' . urlencode('Error al subir el nuevo archivo XML. Código: '.$_FILES['documento_xml']['error']));
    exit;
}


// --- Actualización en la Base de Datos ---
$pdo = null;
try {
    $pdo = getPDOConnection();
    $sql = "UPDATE gastos SET 
                fecha_compra = :fecha_compra, 
                id_tipo_gasto = :id_tipo_gasto, 
                id_area_uso = :id_area_uso, 
                id_tarjeta = :id_tarjeta, 
                tarjeta_otro = :tarjeta_otro,
                precio_unitario = :precio_unitario, 
                cantidad = :cantidad, 
                total_gasto = :total_gasto, 
                id_plataforma_compra = :id_plataforma_compra, 
                plataforma_otro = :plataforma_otro,
                descripcion = :descripcion, 
                folio_factura = :folio_factura, 
                tiene_factura = :tiene_factura, 
                tiene_xml = :tiene_xml_checkbox, 
                nombre_archivo_doc = :nombre_archivo_doc, 
                ruta_archivo_doc = :ruta_archivo_doc,
                nombre_archivo_xml = :nombre_archivo_xml, 
                ruta_archivo_xml = :ruta_archivo_xml
                -- id_usuario_registro puede actualizarse si es necesario
            WHERE id_gasto = :id_gasto_a_actualizar";

    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':fecha_compra', $fecha_compra);
    $stmt->bindParam(':id_tipo_gasto', $id_tipo_gasto, PDO::PARAM_INT);
    // ... (otros binds) ...
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
    $stmt->bindParam(':tiene_xml_checkbox', $tiene_xml_checkbox, PDO::PARAM_BOOL);
    
    $stmt->bindParam(':nombre_archivo_doc', $nuevo_nombre_archivo_doc, $nuevo_nombre_archivo_doc === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindParam(':ruta_archivo_doc', $nueva_ruta_archivo_doc, $nueva_ruta_archivo_doc === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindParam(':nombre_archivo_xml', $nuevo_nombre_archivo_xml, $nuevo_nombre_archivo_xml === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindParam(':ruta_archivo_xml', $nueva_ruta_archivo_xml, $nueva_ruta_archivo_xml === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    
    $stmt->bindParam(':id_gasto_a_actualizar', $id_gasto_a_actualizar, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header('Location: listar_gastos.php?mensaje_exito=' . urlencode('Gasto (ID: ' . $id_gasto_a_actualizar . ') actualizado exitosamente.' . $mensaje_operacion));
    } else {
        // Si la actualización de BD falla, pero se subieron archivos nuevos y se borraron viejos,
        // idealmente se debería intentar revertir la subida o loguear la inconsistencia.
        // Por ahora, solo mostramos error.
        header('Location: editar_gasto.php?id=' . $id_gasto_a_actualizar . '&mensaje_error=' . urlencode('Error al actualizar el gasto en la base de datos.'));
    }
    exit;

} catch (PDOException $e) {
    header('Location: editar_gasto.php?id=' . $id_gasto_a_actualizar . '&mensaje_error=' . urlencode('Error de BD al actualizar: ' . $e->getMessage()));
    exit;
} catch (Exception $e) {
    header('Location: editar_gasto.php?id=' . $id_gasto_a_actualizar . '&mensaje_error=' . urlencode('Error general al actualizar: ' . $e->getMessage()));
    exit;
} finally {
    if (isset($pdo)) {
        $pdo = null;
    }
}
?>
