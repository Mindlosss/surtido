<?php
$page_title = "Gestionar Áreas de Uso";
require_once 'layout_header.php';
require_once 'config/db.php'; 

$areas_uso = [];
$error_message = '';
$success_message = '';
$nombre_area_actual = ''; 
$id_area_actual = null; 
$modo_edicion = false;

// --- Lógica para PROCESAR ACCIONES (Añadir, Editar por POST; Eliminar por GET) ---

// Procesar acciones POST (Agregar, Editar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo_accion = getPDOConnection();
    
    if (isset($_POST['accion']) && $_POST['accion'] === 'agregar_area') {
        $nuevo_nombre_area = trim($_POST['nombre_area'] ?? '');
        if (!empty($nuevo_nombre_area)) {
            try {
                $sql_insert = "INSERT INTO areas_uso (nombre_area) VALUES (:nombre_area)";
                $stmt_insert = $pdo_accion->prepare($sql_insert);
                $stmt_insert->bindParam(':nombre_area', $nuevo_nombre_area);
                if ($stmt_insert->execute()) {
                    $success_message = "Área de uso '" . htmlspecialchars($nuevo_nombre_area) . "' agregada exitosamente.";
                } else {
                    $error_message = "Error al agregar el área de uso.";
                }
            } catch (PDOException $e) {
                if ($e->errorInfo[1] == 1062) { 
                    $error_message = "Error: El área de uso '" . htmlspecialchars($nuevo_nombre_area) . "' ya existe.";
                } else {
                    $error_message = "Error de base de datos al agregar: " . $e->getMessage();
                }
            }
        } else {
            $error_message = "El nombre del área no puede estar vacío.";
        }
    }
    elseif (isset($_POST['accion']) && $_POST['accion'] === 'editar_area') {
        $id_area_a_editar = filter_input(INPUT_POST, 'id_area_editar', FILTER_VALIDATE_INT);
        $nuevo_nombre_area_editada = trim($_POST['nombre_area_editar'] ?? '');

        if ($id_area_a_editar && !empty($nuevo_nombre_area_editada)) {
            try {
                $sql_update = "UPDATE areas_uso SET nombre_area = :nombre_area WHERE id_area_uso = :id_area_uso";
                $stmt_update = $pdo_accion->prepare($sql_update);
                $stmt_update->bindParam(':nombre_area', $nuevo_nombre_area_editada);
                $stmt_update->bindParam(':id_area_uso', $id_area_a_editar, PDO::PARAM_INT);
                if ($stmt_update->execute()) {
                    $success_message = "Área de uso actualizada exitosamente.";
                } else {
                    $error_message = "Error al actualizar el área de uso.";
                }
            } catch (PDOException $e) {
                 if ($e->errorInfo[1] == 1062) { 
                    $error_message = "Error: Ya existe un área de uso con el nombre '" . htmlspecialchars($nuevo_nombre_area_editada) . "'.";
                } else {
                    $error_message = "Error de base de datos al actualizar: " . $e->getMessage();
                }
            }
        } else {
            $error_message = "Faltan datos para la actualización o el nombre está vacío.";
        }
    }
    $pdo_accion = null;
}

// Procesar acción GET (Eliminar, Cargar para Editar)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['accion'])) {
        $accion_get = $_GET['accion'];
        $id_get = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($id_get) {
            $pdo_get_accion = getPDOConnection();
            if ($accion_get === 'editar') {
                $sql_select_uno = "SELECT id_area_uso, nombre_area FROM areas_uso WHERE id_area_uso = :id_area_uso";
                $stmt_select_uno = $pdo_get_accion->prepare($sql_select_uno);
                $stmt_select_uno->bindParam(':id_area_uso', $id_get, PDO::PARAM_INT);
                $stmt_select_uno->execute();
                $area_a_editar = $stmt_select_uno->fetch(PDO::FETCH_ASSOC);
                if ($area_a_editar) {
                    $nombre_area_actual = $area_a_editar['nombre_area'];
                    $id_area_actual = $area_a_editar['id_area_uso'];
                    $modo_edicion = true;
                } else {
                    $error_message = "No se encontró el área de uso para editar con ID: " . htmlspecialchars($id_get);
                }
            } elseif ($accion_get === 'eliminar') {
                try {
                    $sql_delete = "DELETE FROM areas_uso WHERE id_area_uso = :id_area_uso";
                    $stmt_delete = $pdo_get_accion->prepare($sql_delete);
                    $stmt_delete->bindParam(':id_area_uso', $id_get, PDO::PARAM_INT);
                    if ($stmt_delete->execute()) {
                        if ($stmt_delete->rowCount() > 0) {
                            $success_message = "Área de uso (ID: " . htmlspecialchars($id_get) . ") eliminada exitosamente.";
                        } else {
                            $error_message = "No se encontró el área de uso con ID: " . htmlspecialchars($id_get) . " para eliminar o ya fue eliminada.";
                        }
                    } else {
                        $error_message = "Error al eliminar el área de uso.";
                    }
                } catch (PDOException $e) {
                    // Error 1451: Cannot delete or update a parent row: a foreign key constraint fails
                    if ($e->errorInfo[1] == 1451) { 
                        $error_message = "Error: No se puede eliminar el área de uso (ID: " . htmlspecialchars($id_get) . ") porque está siendo utilizada en uno o más gastos. Por favor, reasigne o elimine esos gastos primero.";
                    } else {
                        $error_message = "Error de base de datos al eliminar: " . $e->getMessage();
                    }
                }
            }
            $pdo_get_accion = null;
        } elseif ($accion_get === 'eliminar' || $accion_get === 'editar') {
            $error_message = "ID no proporcionado para la acción '" . htmlspecialchars($accion_get) . "'.";
        }
    }
}


// Obtener todas las áreas de uso para listarlas (siempre se hace, después de cualquier acción)
try {
    $pdo_lista = getPDOConnection();
    $sql_select_all = "SELECT id_area_uso, nombre_area, fecha_creacion FROM areas_uso ORDER BY nombre_area ASC";
    $stmt_select_all = $pdo_lista->query($sql_select_all);
    $areas_uso = $stmt_select_all->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Solo mostrar este error si no hay otro mensaje de error/éxito de una acción previa.
    if(empty($success_message) && empty($error_message)) {
        $error_message = "Error al cargar la lista de áreas de uso: " . $e->getMessage();
    }
} finally {
    if (isset($pdo_lista)) {
        $pdo_lista = null;
    }
}
?>

<div class="container mx-auto px-0 md:px-4 py-0">
    <div class="my-4">
        <a href="control_catalogos.php" class="text-indigo-600 hover:text-indigo-800 hover:underline">
            <i class="fas fa-arrow-left mr-2"></i>Volver a Control de Catálogos
        </a>
    </div>

    <?php if (!empty($success_message)): ?>
        <div class="mensaje mensaje-success my-4"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (!empty($error_message)): ?>
        <div class="mensaje mensaje-error my-4"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <div class="control-section bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">
            <?php echo $modo_edicion ? 'Editar Área de Uso' : 'Añadir Nueva Área de Uso'; ?>
        </h2>
        <form action="gestionar_areas.php<?php echo $modo_edicion ? '?accion=editar&id=' . $id_area_actual : ''; ?>" method="POST" class="flex flex-wrap gap-4 items-end">
            <?php if ($modo_edicion): ?>
                <input type="hidden" name="accion" value="editar_area">
                <input type="hidden" name="id_area_editar" value="<?php echo htmlspecialchars($id_area_actual); ?>">
                <div class="control-grupo flex-grow">
                    <label for="nombre_area_editar" class="block text-sm font-medium text-gray-700">Nombre del Área:</label>
                    <input type="text" id="nombre_area_editar" name="nombre_area_editar" value="<?php echo htmlspecialchars($nombre_area_actual); ?>" required class="mt-1">
                </div>
                <div class="control-grupo">
                    <button type="submit" class="bg-amber-500 hover:bg-amber-600">Actualizar Área</button>
                </div>
                <div class="control-grupo">
                    <a href="gestionar_areas.php" class="w-full text-center bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-md shadow-sm transition duration-150 ease-in-out h-11 leading-7">Cancelar Edición</a>
                </div>
            <?php else: ?>
                <input type="hidden" name="accion" value="agregar_area">
                <div class="control-grupo flex-grow">
                    <label for="nombre_area" class="block text-sm font-medium text-gray-700">Nombre del Área Nueva:</label>
                    <input type="text" id="nombre_area" name="nombre_area" required class="mt-1" placeholder="Ej: Contabilidad">
                </div>
                <div class="control-grupo">
                    <button type="submit">Añadir Área</button>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <div class="control-section bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Áreas de Uso Existentes</h2>
        <?php if (empty($areas_uso) && empty($error_message) && !($modo_edicion && !$success_message)): ?>
            <p class="text-gray-500">No hay áreas de uso registradas todavía.</p>
        <?php elseif (!empty($areas_uso)): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre del Área</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Creación</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($areas_uso as $area): ?>
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($area['id_area_uso']); ?></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($area['nombre_area']); ?></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars(date("d/m/Y H:i", strtotime($area['fecha_creacion']))); ?></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <a href="gestionar_areas.php?accion=editar&id=<?php echo $area['id_area_uso']; ?>" class="text-yellow-500 hover:text-yellow-700 action-btn" title="Editar">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a href="gestionar_areas.php?accion=eliminar&id=<?php echo $area['id_area_uso']; ?>" 
                                       class="text-red-600 hover:text-red-800 action-btn ml-2" 
                                       title="Eliminar"
                                       onclick="return confirm('¿Estás seguro de que quieres eliminar el área \'<?php echo htmlspecialchars(addslashes($area['nombre_area'])); ?>\'? Esta acción podría afectar gastos existentes.');">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once 'layout_footer.php';
?>