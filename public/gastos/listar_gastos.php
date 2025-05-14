<?php
$page_title = "Listado de Gastos"; // Título específico para esta página
require_once 'layout_header.php'; // Incluir el nuevo header

// Habilitar reporte de errores para depuración (puedes comentar estas líneas en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir el archivo de conexión a la BD
require_once 'config/db.php'; 

// La lógica PHP para obtener los gastos de la BD
$gastos = [];
$error_message = '';
$success_message = ''; // Para mensajes de éxito después de una acción (ej. eliminar, editar)

// Verificar si hay un mensaje de alguna acción previa
if (isset($_GET['mensaje_exito'])) {
    $success_message = htmlspecialchars($_GET['mensaje_exito']);
}
if (isset($_GET['mensaje_error'])) {
    $error_message = htmlspecialchars($_GET['mensaje_error']);
}

try {
    $pdo = getPDOConnection(); 

    $sql = "SELECT 
                g.id_gasto, 
                g.fecha_compra, 
                tg.nombre_tipo AS tipo_gasto, 
                au.nombre_area AS area_uso, 
                g.descripcion, 
                g.total_gasto, 
                g.folio_factura, 
                g.tiene_factura, 
                g.tiene_xml,
                g.nombre_archivo_doc, 
                g.ruta_archivo_doc,   
                g.nombre_archivo_xml, 
                g.ruta_archivo_xml,   
                IFNULL(t.nombre_tarjeta, g.tarjeta_otro) AS tarjeta,
                IFNULL(pc.nombre_plataforma, g.plataforma_otro) AS plataforma
            FROM gastos g
            LEFT JOIN tipos_gasto tg ON g.id_tipo_gasto = tg.id_tipo_gasto
            LEFT JOIN areas_uso au ON g.id_area_uso = au.id_area_uso
            LEFT JOIN tarjetas t ON g.id_tarjeta = t.id_tarjeta
            LEFT JOIN plataformas_compra pc ON g.id_plataforma_compra = pc.id_plataforma_compra
            ORDER BY g.fecha_compra DESC, g.id_gasto DESC";

    $stmt = $pdo->query($sql);
    $gastos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_message = "Error de base de datos al listar gastos: " . $e->getMessage();
} catch (Exception $e) {
    $error_message = "Error general al listar gastos: " . $e->getMessage();
} finally {
    if (isset($pdo)) {
        $pdo = null;
    }
}

?>

<div class="container mx-auto px-0 md:px-4 py-0">

    <?php if (!empty($success_message)): ?>
        <div class="mensaje mensaje-success my-4"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (!empty($error_message)): ?>
        <div class="mensaje mensaje-error my-4"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <div class="my-4"> 
        <a href="captura_gasto.php" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-150 ease-in-out inline-flex items-center">
            <i class="fas fa-plus mr-2"></i>Registrar Nuevo Gasto
        </a>
    </div>

    <?php if (empty($gastos) && empty($error_message) && empty($success_message)): ?>
        <div class="mensaje mensaje-info">No hay gastos registrados todavía.</div>
    <?php elseif (!empty($gastos)): ?>
        <div class="bg-white shadow-md rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Área</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plataforma</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarjeta</th>
                        <th scope="col" class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Folio</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Factura</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiene XML</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doc. PDF/JPG</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Archivo XML</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($gastos as $gasto): ?>
                        <tr>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($gasto['id_gasto']); ?></td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars(date("d/m/Y", strtotime($gasto['fecha_compra']))); ?></td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($gasto['tipo_gasto']); ?></td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($gasto['area_uso']); ?></td>
                            <td class="px-3 py-3 text-sm text-gray-700 max-w-xs truncate" title="<?php echo htmlspecialchars($gasto['descripcion']); ?>"><?php echo nl2br(htmlspecialchars(mb_strimwidth($gasto['descripcion'], 0, 40, "..."))); ?></td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($gasto['plataforma'] ?: 'N/A'); ?></td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($gasto['tarjeta'] ?: 'N/A'); ?></td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-900 text-right">$<?php echo htmlspecialchars(number_format($gasto['total_gasto'], 2)); ?></td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($gasto['folio_factura'] ?: 'N/A'); ?></td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-700"><?php echo $gasto['tiene_factura'] ? 'Sí' : 'No'; ?></td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-700"><?php echo $gasto['tiene_xml'] ? 'Sí' : 'No'; ?></td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm">
                                <?php if (!empty($gasto['nombre_archivo_doc'])): ?>
                                    <a href="<?php echo htmlspecialchars('uploads/' . $gasto['nombre_archivo_doc']); ?>" target="_blank" class="text-indigo-600 hover:text-indigo-800 hover:underline" title="Ver <?php echo htmlspecialchars($gasto['nombre_archivo_doc']); ?>">
                                        <i class="fas fa-file-pdf mr-1"></i> Ver
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-400 italic">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm">
                                <?php if (!empty($gasto['nombre_archivo_xml'])): ?>
                                    <a href="<?php echo htmlspecialchars('visualizar_documento.php?archivo=' . urlencode($gasto['nombre_archivo_xml']) . '&tipo=xml'); ?>" target="_blank" class="text-sky-600 hover:text-sky-800 hover:underline mr-1" title="Ver <?php echo htmlspecialchars($gasto['nombre_archivo_xml']); ?>">
                                        <i class="fas fa-file-code mr-1"></i> Ver
                                    </a>
                                    <a href="<?php echo htmlspecialchars('visualizar_documento.php?archivo=' . urlencode($gasto['nombre_archivo_xml']) . '&descargar=1&tipo=xml'); ?>" class="text-teal-600 hover:text-teal-800 hover:underline" title="Descargar <?php echo htmlspecialchars($gasto['nombre_archivo_xml']); ?>">
                                        <i class="fas fa-download"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-400 italic">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm font-medium">
                                <a href="editar_gasto.php?id=<?php echo $gasto['id_gasto']; ?>" class="text-yellow-500 hover:text-yellow-700 mr-2" title="Editar Gasto">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if (!empty($gasto['nombre_archivo_doc'])): ?>
                                    <a href="<?php echo htmlspecialchars('visualizar_documento.php?archivo=' . urlencode($gasto['nombre_archivo_doc'])); ?>" target="_blank" class="text-blue-600 hover:text-blue-800 mr-2" title="Visualizar Documento PDF/JPG">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo htmlspecialchars('visualizar_documento.php?archivo=' . urlencode($gasto['nombre_archivo_doc']) . '&descargar=1'); ?>" class="text-green-600 hover:text-green-800 mr-2" title="Descargar Documento PDF/JPG">
                                        <i class="fas fa-download"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="eliminar_gasto.php?id=<?php echo $gasto['id_gasto']; ?>" 
                                   class="text-red-600 hover:text-red-800" 
                                   title="Eliminar Gasto"
                                   onclick="return confirm('¿Estás seguro de que quieres eliminar este gasto (ID: <?php echo $gasto['id_gasto']; ?>)? Esta acción no se puede deshacer.');">
                                    <i class="fas fa-trash"></i>
                                </a>
                                </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php
require_once 'layout_footer.php'; // Incluir el nuevo footer
?>
