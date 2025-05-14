<?php
// Archivo: eliminar_gasto.php
// Propósito: Eliminar un registro de gasto y su archivo asociado.

require_once 'config/db.php'; // Conexión a la BD

// Habilitar reporte de errores para depuración (comentar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_gasto_a_eliminar = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id_gasto_a_eliminar === false || $id_gasto_a_eliminar === null) {
    // Redirigir con mensaje de error si el ID no es válido o no se proporciona
    header('Location: listar_gastos.php?mensaje_error=' . urlencode('ID de gasto no válido para eliminar.'));
    exit;
}

$pdo = null;
$mensaje_exito = '';
$mensaje_error = '';

try {
    $pdo = getPDOConnection();

    // Iniciar una transacción para asegurar que ambas operaciones (borrar de BD y borrar archivo)
    // se completen o ninguna lo haga.
    $pdo->beginTransaction();

    // 1. Obtener la información del archivo antes de borrar el registro de la BD
    $sql_select_archivo = "SELECT nombre_archivo_doc, ruta_archivo_doc FROM gastos WHERE id_gasto = :id_gasto";
    $stmt_select = $pdo->prepare($sql_select_archivo);
    $stmt_select->bindParam(':id_gasto', $id_gasto_a_eliminar, PDO::PARAM_INT);
    $stmt_select->execute();
    $gasto_info = $stmt_select->fetch(PDO::FETCH_ASSOC);

    // 2. Eliminar el registro de la base de datos
    $sql_delete = "DELETE FROM gastos WHERE id_gasto = :id_gasto";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->bindParam(':id_gasto', $id_gasto_a_eliminar, PDO::PARAM_INT);
    
    if ($stmt_delete->execute()) {
        if ($stmt_delete->rowCount() > 0) {
            // 3. Si el borrado en BD fue exitoso y se afectó una fila, intentar borrar el archivo físico
            if ($gasto_info && !empty($gasto_info['ruta_archivo_doc'])) {
                // La ruta_archivo_doc ya debería ser la ruta completa desde registrar_gasto.php
                // Ejemplo: /home/user/public_html/gastos/uploads/nombre_archivo.pdf
                // Si solo guardaste el nombre y UPLOAD_DIR, reconstrúyela aquí.
                // Asumiendo que ruta_archivo_doc es la ruta completa y correcta:
                $ruta_fisica_archivo = $gasto_info['ruta_archivo_doc']; 

                if (file_exists($ruta_fisica_archivo) && is_writable($ruta_fisica_archivo)) { // is_writable no es estrictamente necesario para unlink, pero es buena práctica verificar
                    if (unlink($ruta_fisica_archivo)) {
                        $mensaje_exito = "Gasto (ID: " . $id_gasto_a_eliminar . ") y su archivo asociado eliminados exitosamente.";
                    } else {
                        // El registro de BD se borró, pero el archivo no. Esto es un problema potencial.
                        // Podrías loguearlo o manejarlo de forma más robusta.
                        $mensaje_exito = "Gasto (ID: " . $id_gasto_a_eliminar . ") eliminado de la BD, pero hubo un error al eliminar el archivo físico: " . htmlspecialchars($gasto_info['nombre_archivo_doc']);
                        // Considerar no hacer commit si el borrado del archivo es crítico.
                    }
                } elseif ($gasto_info && !empty($gasto_info['nombre_archivo_doc'])) {
                    // El archivo estaba registrado pero no se encontró o no es accesible en la ruta especificada.
                    $mensaje_exito = "Gasto (ID: " . $id_gasto_a_eliminar . ") eliminado de la BD. El archivo asociado (" . htmlspecialchars($gasto_info['nombre_archivo_doc']) . ") no se encontró en la ruta esperada o no se pudo acceder.";
                } else {
                    // No había archivo asociado o la ruta no estaba registrada.
                    $mensaje_exito = "Gasto (ID: " . $id_gasto_a_eliminar . ") eliminado exitosamente (no tenía archivo asociado).";
                }
            } else {
                 $mensaje_exito = "Gasto (ID: " . $id_gasto_a_eliminar . ") eliminado exitosamente (no tenía archivo asociado).";
            }
            $pdo->commit(); // Confirmar la transacción
        } else {
            $mensaje_error = "No se encontró ningún gasto con el ID: " . $id_gasto_a_eliminar . ". No se eliminó nada.";
            $pdo->rollBack(); // Revertir si no se afectaron filas (aunque DELETE no da error si no encuentra)
        }
    } else {
        $mensaje_error = "Error al intentar eliminar el gasto de la base de datos.";
        $pdo->rollBack(); // Revertir la transacción
    }

} catch (PDOException $e) {
    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $mensaje_error = "Error de base de datos al eliminar: " . $e->getMessage();
} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $mensaje_error = "Error general al eliminar: " . $e->getMessage();
} finally {
    if (isset($pdo)) {
        $pdo = null;
    }
}

// Redirigir de vuelta a listar_gastos.php con el mensaje apropiado
if (!empty($mensaje_exito)) {
    header('Location: listar_gastos.php?mensaje_exito=' . urlencode($mensaje_exito));
} else {
    header('Location: listar_gastos.php?mensaje_error=' . urlencode($mensaje_error ?: 'Ocurrió un error desconocido durante la eliminación.'));
}
exit;
?>
