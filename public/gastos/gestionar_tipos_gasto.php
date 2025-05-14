<?php
$page_title = "Gestionar Tipos de Gasto";
require_once 'layout_header.php';
require_once 'config/db.php'; 

$tipos_gasto = [];
$error_message = '';
$success_message = '';
$nombre_tipo_actual = ''; 
$id_tipo_actual = null;   
$modo_edicion = false;

// --- Lógica para PROCESAR ACCIONES (Añadir, Editar por POST; Eliminar por GET) ---

// Procesar acciones POST (Agregar, Editar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo_accion = getPDOConnection();
    
    if (isset($_POST['accion']) && $_POST['accion'] === 'agregar_tipo_gasto') {
        $nuevo_nombre_tipo = trim($_POST['nombre_tipo'] ?? '');
        if (!empty($nuevo_nombre_tipo)) {
            try {
                $sql_insert = "INSERT INTO tipos_gasto (nombre_tipo) VALUES (:nombre_tipo)";
                $stmt_insert = $pdo_accion->prepare($sql_insert);
                $stmt_insert->bindParam(':nombre_tipo', $nuevo_nombre_tipo);
                if ($stmt_insert->execute()) {
                    $success_message = "Tipo de Gasto '" . htmlspecialchars($nuevo_nombre_tipo) . "' agregado exitosamente.";
                } else {
                    $error_message = "Error al agregar el tipo de gasto.";
                }
            } catch (PDOException $e) {
                if ($e->errorInfo[1] == 1062) { 
                    $error_message = "Error: El tipo de gasto '" . htmlspecialchars($nuevo_nombre_tipo) . "' ya existe.";
                } else {
                    $error_message = "Error de base de datos al agregar: " . $e->getMessage();
                }
            }
        } else {
            $error_message = "El nombre del tipo de gasto no puede estar vacío.";
        }
    }
    elseif (isset($_POST['accion']) && $_POST['accion'] === 'editar_tipo_gasto') {
        $id_tipo_a_editar = filter_input(INPUT_POST, 'id_tipo_gasto_editar', FILTER_VALIDATE_INT);
        $nuevo_nombre_tipo_editado = trim($_POST['nombre_tipo_editar'] ?? '');

        if ($id_tipo_a_editar && !empty($nuevo_nombre_tipo_editado)) {
            try {
                $sql_update = "UPDATE tipos_gasto SET nombre_tipo = :nombre_tipo WHERE id_tipo_gasto = :id_tipo_gasto";
                $stmt_update = $pdo_accion->prepare($sql_update);
                $stmt_update->bindParam(':nombre_tipo', $nuevo_nombre_tipo_editado);
                $stmt_update->bindParam(':id_tipo_gasto', $id_tipo_a_editar, PDO::PARAM_INT);
                if ($stmt_update->execute()) {
                    $success_message = "Tipo de gasto actualizado exitosamente.";
                } else {
                    $error_message = "Error al actualizar el tipo de gasto.";
                }
            } catch (PDOException $e) {
                 if ($e->errorInfo[1] == 1062) { 
                    $error_message = "Error: Ya existe un tipo de gasto con el nombre '" . htmlspecialchars($nuevo_nombre_tipo_editado) . "'.";
                } else {
                    $error_message = "Error de base de datos al actualizar: " . $e->getMessage();
                }
            }
        } else {
            $error_message = "Faltan datos para la actualización o el nombre está vacío.";
        }
    }
    if(isset($pdo_accion)) {$pdo_accion = null;}
}

// Procesar acción GET (Eliminar, Cargar para Editar)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['accion'])) {
        $accion_get = $_GET['accion'];
        $id_get = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($id_get) {
            $pdo_get_accion = getPDOConnection();
            if ($accion_get === 'editar') {
                $sql_select_uno = "SELECT id_tipo_gasto, nombre_tipo FROM tipos_gasto WHERE id_tipo_gasto = :id_tipo_gasto";
                $stmt_select_uno = $pdo_get_accion->prepare($sql_select_uno);
                $stmt_select_uno->bindParam(':id_tipo_gasto', $id_get, PDO::PARAM_INT);
                $stmt_select_uno->execute();
                $tipo_a_editar = $stmt_select_uno->fetch(PDO::FETCH_ASSOC);
                if ($tipo_a_editar) {
                    $nombre_tipo_actual = $tipo_a_editar['nombre_tipo'];
                    $id_tipo_actual = $tipo_a_editar['id_tipo_gasto'];
                    $modo_edicion = true;
                } else {
                    $error_message = "No se encontró el tipo de gasto para editar con ID: " . htmlspecialchars($id_get);
                }
            } 
            elseif ($accion_get === 'eliminar') {
                try {
                    $sql_delete = "DELETE FROM tipos_gasto WHERE id_tipo_gasto = :id_tipo_gasto";
                    $stmt_delete = $pdo_get_accion->prepare($sql_delete);
                    $stmt_delete->bindParam(':id_tipo_gasto', $id_get, PDO::PARAM_INT);
                    if ($stmt_delete->execute()) {
                        if ($stmt_delete->rowCount() > 0) {
                            $success_message = "Tipo de gasto (ID: " . htmlspecialchars($id_get) . ") eliminado exitosamente.";
                        } else {
                            $error_message = "No se encontró el tipo de gasto con ID: " . htmlspecialchars($id_get) . " para eliminar o ya fue eliminado.";
                        }
                    } else {
                        $error_message = "Error al eliminar el tipo de gasto.";
                    }
                } catch (PDOException $e) {
                    // La FK en `gastos` para `id_tipo_gasto` es ON DELETE RESTRICT
                    if ($e->errorInfo[1] == 1451) { 
                        $error_message = "Error: No se puede eliminar el tipo de gasto (ID: " . htmlspecialchars($id_get) . ") porque está siendo utilizado en uno o más gastos. Por favor, reasigne o elimine esos gastos primero.";
                    } else {
                        $error_message = "Error de base de datos al eliminar el tipo de gasto: " . $e->getMessage();
                    }
                }
            }
            if(isset($pdo_get_accion)) {$pdo_get_accion = null;}
        } elseif ($accion_get === 'eliminar' || $accion_get === 'editar') {
            $error_message = "ID no proporcionado para la acción '" . htmlspecialchars($accion_get) . "'.";
        }
    }
}

// Obtener todos los tipos de gasto para listarlos
try {
    $pdo_lista = getPDOConnection();
    $sql_select_all = "SELECT id_tipo_gasto, nombre_tipo, fecha_creacion FROM tipos_gasto ORDER BY nombre_tipo ASC";
    $stmt_select_all = $pdo_lista->query($sql_select_all);
    $tipos_gasto = $stmt_select_all->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    if(empty($success_message) && empty($error_message)) {
        $error_message = "Error al cargar la lista de tipos de gasto: " . $e->getMessage();
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
            <?php echo $modo_edicion ? 'Editar Tipo de Gasto' : 'Añadir Nuevo Tipo de Gasto'; ?>
        </h2>
        <form action="gestionar_tipos_gasto.php<?php echo $modo_edicion ? '?accion=editar&id=' . $id_tipo_actual : ''; ?>" method="POST" class="flex flex-wrap gap-4 items-end">
            <?php if ($modo_edicion): ?>
                <input type="hidden" name="accion" value="editar_tipo_gasto">
                <input type="hidden" name="id_tipo_gasto_editar" value="<?php echo htmlspecialchars($id_tipo_actual); ?>">
                <div class="control-grupo flex-grow">
                    <label for="nombre_tipo_editar" class="block text-sm font-medium text-gray-700">Nombre del Tipo de Gasto:</label>
                    <input type="text" id="nombre_tipo_editar" name="nombre_tipo_editar" value="<?php echo htmlspecialchars($nombre_tipo_actual); ?>" required class="mt-1">
                </div>
                <div class="control-grupo">
                    <button type="submit" class="bg-amber-500 hover:bg-amber-600">Actualizar Tipo</button>
                </div>
                <div class="control-grupo">
                    <a href="gestionar_tipos_gasto.php" class="w-full text-center bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-md shadow-sm transition duration-150 ease-in-out h-11 leading-7">Cancelar Edición</a>
                </div>
            <?php else: ?>
                <input type="hidden" name="accion" value="agregar_tipo_gasto">
                <div class="control-grupo flex-grow">
                    <label for="nombre_tipo" class="block text-sm font-medium text-gray-700">Nombre del Tipo de Gasto Nuevo:</label>
                    <input type="text" id="nombre_tipo" name="nombre_tipo" required class="mt-1" placeholder="Ej: Software, Hardware, Viáticos">
                </div>
                <div class="control-grupo">
                    <button type="submit">Añadir Tipo</button>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <div class="control-section bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Tipos de Gasto Existentes</h2>
        <?php if (empty($tipos_gasto) && empty($error_message) && !($modo_edicion && !$success_message)): ?>
            <p class="text-gray-500">No hay tipos de gasto registrados todavía.</p>
        <?php elseif (!empty($tipos_gasto)): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre del Tipo de Gasto</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Creación</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($tipos_gasto as $tipo): ?>
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($tipo['id_tipo_gasto']); ?></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($tipo['nombre_tipo']); ?></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars(date("d/m/Y H:i", strtotime($tipo['fecha_creacion']))); ?></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <a href="gestionar_tipos_gasto.php?accion=editar&id=<?php echo $tipo['id_tipo_gasto']; ?>" class="text-yellow-500 hover:text-yellow-700 action-btn" title="Editar">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a href="gestionar_tipos_gasto.php?accion=eliminar&id=<?php echo $tipo['id_tipo_gasto']; ?>" 
                                       class="text-red-600 hover:text-red-800 action-btn ml-2" 
                                       title="Eliminar"
                                       onclick="return confirm('¿Estás seguro de que quieres eliminar el tipo de gasto \'<?php echo htmlspecialchars(addslashes($tipo['nombre_tipo'])); ?>\'? Esta acción podría afectar gastos existentes.');">
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
