<?php
$page_title = "Listado de Gastos";
require_once 'layout_header.php';
require_once 'config/db.php'; 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$gastos = [];
$error_message = '';
$success_message = '';

if (isset($_GET['mensaje_exito'])) {
    $success_message = htmlspecialchars($_GET['mensaje_exito']);
}
if (isset($_GET['mensaje_error'])) {
    $error_message = htmlspecialchars($_GET['mensaje_error']);
}

// --- Recoger parámetros de filtro (GET) ---
$filtro_fecha_desde = $_GET['fecha_desde'] ?? '';
$filtro_fecha_hasta = $_GET['fecha_hasta'] ?? '';
$filtro_id_area_uso = filter_input(INPUT_GET, 'id_area_uso', FILTER_VALIDATE_INT);
$filtro_id_plataforma = filter_input(INPUT_GET, 'id_plataforma_compra', FILTER_VALIDATE_INT);

// --- Lógica para construir la cláusula WHERE ---
$where_clauses = [];
$params = [];

if (!empty($filtro_fecha_desde)) {
    $where_clauses[] = "g.fecha_compra >= :fecha_desde";
    $params[':fecha_desde'] = $filtro_fecha_desde;
}
if (!empty($filtro_fecha_hasta)) {
    $where_clauses[] = "g.fecha_compra <= :fecha_hasta";
    $params[':fecha_hasta'] = $filtro_fecha_hasta;
}
if ($filtro_id_area_uso) {
    $where_clauses[] = "g.id_area_uso = :id_area_uso";
    $params[':id_area_uso'] = $filtro_id_area_uso;
}
if ($filtro_id_plataforma) {
    $where_clauses[] = "g.id_plataforma_compra = :id_plataforma_compra";
    $params[':id_plataforma_compra'] = $filtro_id_plataforma;
}

$sql_where_condition = "";
if (!empty($where_clauses)) {
    $sql_where_condition = "WHERE " . implode(" AND ", $where_clauses);
}

try {
    $pdo = getPDOConnection(); 

    $sql = "SELECT 
                g.id_gasto, g.fecha_compra, 
                tg.nombre_tipo AS tipo_gasto, 
                au.nombre_area AS area_uso, 
                g.descripcion, g.total_gasto, g.folio_factura, 
                g.tiene_factura, g.tiene_xml,
                g.nombre_archivo_doc, g.ruta_archivo_doc,   
                g.nombre_archivo_xml, g.ruta_archivo_xml,   
                IFNULL(t.nombre_tarjeta, g.tarjeta_otro) AS tarjeta,
                IFNULL(pc.nombre_plataforma, g.plataforma_otro) AS plataforma
            FROM gastos g
            LEFT JOIN tipos_gasto tg ON g.id_tipo_gasto = tg.id_tipo_gasto
            LEFT JOIN areas_uso au ON g.id_area_uso = au.id_area_uso
            LEFT JOIN tarjetas t ON g.id_tarjeta = t.id_tarjeta
            LEFT JOIN plataformas_compra pc ON g.id_plataforma_compra = pc.id_plataforma_compra
            {$sql_where_condition}  -- Aplicar filtros aquí
            ORDER BY g.fecha_compra DESC, g.id_gasto DESC";

    $stmt = $pdo->prepare($sql); // Preparar la consulta
    $stmt->execute($params);      // Ejecutar con los parámetros de filtro
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

    <form action="listar_gastos.php" method="GET" class="filtros-container mb-6">
        <div class="filtro-grupo">
            <label for="fecha_desde">Desde:</label>
            <input type="date" id="fecha_desde" name="fecha_desde" value="<?php echo htmlspecialchars($filtro_fecha_desde); ?>">
        </div>
        <div class="filtro-grupo">
            <label for="fecha_hasta">Hasta:</label>
            <input type="date" id="fecha_hasta" name="fecha_hasta" value="<?php echo htmlspecialchars($filtro_fecha_hasta); ?>">
        </div>
        <div class="filtro-grupo">
            <label for="id_area_uso">Área de Uso:</label>
            <select id="id_area_uso" name="id_area_uso">
                <option value="">Todas</option>
                </select>
        </div>
        <div class="filtro-grupo">
            <label for="id_plataforma_compra">Plataforma:</label>
            <select id="id_plataforma_compra" name="id_plataforma_compra">
                <option value="">Todas</option>
                </select>
        </div>
        <div class="filtro-grupo">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold">Aplicar Filtros</button>
        </div>
         <div class="filtro-grupo">
            <a href="listar_gastos.php" class="w-full text-center bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-md shadow-sm transition duration-150 ease-in-out h-11 leading-7">Limpiar Filtros</a>
        </div>
    </form>
    <div class="my-4"> 
        <a href="captura_gasto.php" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-150 ease-in-out inline-flex items-center">
            <i class="fas fa-plus mr-2"></i>Registrar Nuevo Gasto
        </a>
    </div>

    <?php if (empty($gastos) && empty($error_message) && empty($success_message)): ?>
        <div class="mensaje mensaje-info">No hay gastos que coincidan con los filtros aplicados o no hay gastos registrados.</div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Poblar los selects de filtro y seleccionar valores actuales si existen
    const filtroAreaUsoSelect = document.getElementById('id_area_uso');
    const filtroPlataformaSelect = document.getElementById('id_plataforma_compra');
    
    // Valores actuales de los filtros (si la página se recargó con ellos)
    const currentArea = <?php echo json_encode($filtro_id_area_uso); ?>;
    const currentPlataforma = <?php echo json_encode($filtro_id_plataforma); ?>;

    fetch('obtener_catalogos.php')
        .then(response => response.ok ? response.json() : Promise.reject('Error al cargar catálogos'))
        .then(data => {
            if (data.success && data.data) {
                if (filtroAreaUsoSelect && data.data.areas_uso) {
                    poblarSelectFiltro(filtroAreaUsoSelect, data.data.areas_uso, 'id_area_uso', 'nombre_area', 'Todas', currentArea);
                }
                if (filtroPlataformaSelect && data.data.plataformas_compra) {
                    poblarSelectFiltro(filtroPlataformaSelect, data.data.plataformas_compra, 'id_plataforma_compra', 'nombre_plataforma', 'Todas', currentPlataforma);
                }
            } else {
                console.error('Error en datos de catálogos para filtros:', data.message);
            }
        })
        .catch(error => console.error('Error en fetch para catálogos de filtro:', error));
});

function poblarSelectFiltro(selectElement, items, valueField, textField, placeholderText, currentValue) {
    if (!selectElement) return;
    
    // Guardar el placeholder si ya existe o crearlo
    let placeholderOpt = selectElement.querySelector('option[value=""]');
    if (!placeholderOpt) {
        placeholderOpt = document.createElement('option');
        placeholderOpt.value = "";
        placeholderOpt.textContent = placeholderText;
    }
    selectElement.innerHTML = ''; // Limpiar
    selectElement.appendChild(placeholderOpt); // Re-añadir placeholder

    if (items && items.length > 0) {
        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item[valueField];
            option.textContent = item[textField];
            if (currentValue && item[valueField] == currentValue) {
                option.selected = true;
            }
            selectElement.appendChild(option);
        });
    }
}
</script>

<?php
require_once 'layout_footer.php';
?>
