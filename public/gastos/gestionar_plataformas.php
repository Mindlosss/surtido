<?php
$page_title = "Gestionar Plataformas de Compra";
require_once 'layout_header.php';
require_once 'config/db.php'; 

$plataformas = [];
$error_message = '';
$success_message = '';
$nombre_plataforma_actual = ''; 
$id_plataforma_actual = null;   
$modo_edicion = false;

// --- Lógica para PROCESAR ACCIONES (Añadir, Editar por POST; Eliminar por GET) ---

// Procesar acciones POST (Agregar, Editar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo_accion = getPDOConnection();
    
    if (isset($_POST['accion']) && $_POST['accion'] === 'agregar_plataforma') {
        $nuevo_nombre_plataforma = trim($_POST['nombre_plataforma'] ?? '');
        if (!empty($nuevo_nombre_plataforma)) {
            try {
                $sql_insert = "INSERT INTO plataformas_compra (nombre_plataforma) VALUES (:nombre_plataforma)";
                $stmt_insert = $pdo_accion->prepare($sql_insert);
                $stmt_insert->bindParam(':nombre_plataforma', $nuevo_nombre_plataforma);
                if ($stmt_insert->execute()) {
                    $success_message = "Plataforma de Compra '" . htmlspecialchars($nuevo_nombre_plataforma) . "' agregada exitosamente.";
                } else {
                    $error_message = "Error al agregar la plataforma de compra.";
                }
            } catch (PDOException $e) {
                if ($e->errorInfo[1] == 1062) { 
                    $error_message = "Error: La plataforma de compra '" . htmlspecialchars($nuevo_nombre_plataforma) . "' ya existe.";
                } else {
                    $error_message = "Error de base de datos al agregar: " . $e->getMessage();
                }
            }
        } else {
            $error_message = "El nombre de la plataforma de compra no puede estar vacío.";
        }
    }
    elseif (isset($_POST['accion']) && $_POST['accion'] === 'editar_plataforma') {
        $id_plataforma_a_editar = filter_input(INPUT_POST, 'id_plataforma_editar', FILTER_VALIDATE_INT);
        $nuevo_nombre_plataforma_editada = trim($_POST['nombre_plataforma_editar'] ?? '');

        if ($id_plataforma_a_editar && !empty($nuevo_nombre_plataforma_editada)) {
            try {
                $sql_update = "UPDATE plataformas_compra SET nombre_plataforma = :nombre_plataforma WHERE id_plataforma_compra = :id_plataforma_compra";
                $stmt_update = $pdo_accion->prepare($sql_update);
                $stmt_update->bindParam(':nombre_plataforma', $nuevo_nombre_plataforma_editada);
                $stmt_update->bindParam(':id_plataforma_compra', $id_plataforma_a_editar, PDO::PARAM_INT);
                if ($stmt_update->execute()) {
                    $success_message = "Plataforma de compra actualizada exitosamente.";
                } else {
                    $error_message = "Error al actualizar la plataforma de compra.";
                }
            } catch (PDOException $e) {
                 if ($e->errorInfo[1] == 1062) { 
                    $error_message = "Error: Ya existe una plataforma de compra con el nombre '" . htmlspecialchars($nuevo_nombre_plataforma_editada) . "'.";
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
                $sql_select_uno = "SELECT id_plataforma_compra, nombre_plataforma FROM plataformas_compra WHERE id_plataforma_compra = :id_plataforma_compra";
                $stmt_select_uno = $pdo_get_accion->prepare($sql_select_uno);
                $stmt_select_uno->bindParam(':id_plataforma_compra', $id_get, PDO::PARAM_INT);
                $stmt_select_uno->execute();
                $plataforma_a_editar = $stmt_select_uno->fetch(PDO::FETCH_ASSOC);
                if ($plataforma_a_editar) {
                    $nombre_plataforma_actual = $plataforma_a_editar['nombre_plataforma'];
                    $id_plataforma_actual = $plataforma_a_editar['id_plataforma_compra'];
                    $modo_edicion = true;
                } else {
                    $error_message = "No se encontró la plataforma de compra para editar con ID: " . htmlspecialchars($id_get);
                }
            } 
            elseif ($accion_get === 'eliminar') {
                try {
                    $sql_delete = "DELETE FROM plataformas_compra WHERE id_plataforma_compra = :id_plataforma_compra";
                    $stmt_delete = $pdo_get_accion->prepare($sql_delete);
                    $stmt_delete->bindParam(':id_plataforma_compra', $id_get, PDO::PARAM_INT);
                    if ($stmt_delete->execute()) {
                        if ($stmt_delete->rowCount() > 0) {
                            $success_message = "Plataforma de compra (ID: " . htmlspecialchars($id_get) . ") eliminada exitosamente.";
                        } else {
                            $error_message = "No se encontró la plataforma de compra con ID: " . htmlspecialchars($id_get) . " para eliminar o ya fue eliminada.";
                        }
                    } else {
                        $error_message = "Error al eliminar la plataforma de compra.";
                    }
                } catch (PDOException $e) {
                    // La FK en `gastos` para `id_plataforma_compra` es ON DELETE SET NULL, 
                    // por lo que el error 1451 no debería ocurrir si la BD está configurada como se planeó.
                    // Si ocurriera, significa que la FK es RESTRICT.
                    if ($e->errorInfo[1] == 1451) { 
                        $error_message = "Error: No se puede eliminar la plataforma (ID: " . htmlspecialchars($id_get) . ") porque está siendo utilizada en uno o más gastos.";
                    } else {
                        $error_message = "Error de base de datos al eliminar la plataforma: " . $e->getMessage();
                    }
                }
            }
            if(isset($pdo_get_accion)) {$pdo_get_accion = null;}
        } elseif ($accion_get === 'eliminar' || $accion_get === 'editar') {
            $error_message = "ID no proporcionado para la acción '" . htmlspecialchars($accion_get) . "'.";
        }
    }
}

// Obtener todas las plataformas para listarlas
try {
    $pdo_lista = getPDOConnection();
    $sql_select_all = "SELECT id_plataforma_compra, nombre_plataforma, fecha_creacion FROM plataformas_compra ORDER BY nombre_plataforma ASC";
    $stmt_select_all = $pdo_lista->query($sql_select_all);
    $plataformas = $stmt_select_all->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    if(empty($success_message) && empty($error_message)) {
        $error_message = "Error al cargar la lista de plataformas de compra: " . $e->getMessage();
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
            <?php echo $modo_edicion ? 'Editar Plataforma de Compra' : 'Añadir Nueva Plataforma de Compra'; ?>
        </h2>
        <form action="gestionar_plataformas.php<?php echo $modo_edicion ? '?accion=editar&id=' . $id_plataforma_actual : ''; ?>" method="POST" class="flex flex-wrap gap-4 items-end">
            <?php if ($modo_edicion): ?>
                <input type="hidden" name="accion" value="editar_plataforma">
                <input type="hidden" name="id_plataforma_editar" value="<?php echo htmlspecialchars($id_plataforma_actual); ?>">
                <div class="control-grupo flex-grow">
                    <label for="nombre_plataforma_editar" class="block text-sm font-medium text-gray-700">Nombre de la Plataforma:</label>
                    <input type="text" id="nombre_plataforma_editar" name="nombre_plataforma_editar" value="<?php echo htmlspecialchars($nombre_plataforma_actual); ?>" required class="mt-1">
                </div>
                <div class="control-grupo">
                    <button type="submit" class="bg-amber-500 hover:bg-amber-600">Actualizar Plataforma</button>
                </div>
                <div class="control-grupo">
                    <a href="gestionar_plataformas.php" class="w-full text-center bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-md shadow-sm transition duration-150 ease-in-out h-11 leading-7">Cancelar Edición</a>
                </div>
            <?php else: ?>
                <input type="hidden" name="accion" value="agregar_plataforma">
                <div class="control-grupo flex-grow">
                    <label for="nombre_plataforma" class="block text-sm font-medium text-gray-700">Nombre de la Plataforma Nueva:</label>
                    <input type="text" id="nombre_plataforma" name="nombre_plataforma" required class="mt-1" placeholder="Ej: Amazon, Mercado Libre">
                </div>
                <div class="control-grupo">
                    <button type="submit">Añadir Plataforma</button>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <div class="control-section bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Plataformas de Compra Existentes</h2>
        <?php if (empty($plataformas) && empty($error_message) && !($modo_edicion && !$success_message)): ?>
            <p class="text-gray-500">No hay plataformas de compra registradas todavía.</p>
        <?php elseif (!empty($plataformas)): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre de la Plataforma</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Creación</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($plataformas as $plataforma): ?>
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($plataforma['id_plataforma_compra']); ?></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($plataforma['nombre_plataforma']); ?></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars(date("d/m/Y H:i", strtotime($plataforma['fecha_creacion']))); ?></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <a href="gestionar_plataformas.php?accion=editar&id=<?php echo $plataforma['id_plataforma_compra']; ?>" class="text-yellow-500 hover:text-yellow-700 action-btn" title="Editar">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a href="gestionar_plataformas.php?accion=eliminar&id=<?php echo $plataforma['id_plataforma_compra']; ?>" 
                                       class="text-red-600 hover:text-red-800 action-btn ml-2" 
                                       title="Eliminar"
                                       onclick="return confirm('¿Estás seguro de que quieres eliminar la plataforma \'<?php echo htmlspecialchars(addslashes($plataforma['nombre_plataforma'])); ?>\'?');">
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
