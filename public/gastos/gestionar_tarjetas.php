<?php
$page_title = "Gestionar Tarjetas de Pago";
require_once 'layout_header.php';
require_once 'config/db.php'; 

$tarjetas = [];
$error_message = '';
$success_message = '';
$nombre_tarjeta_actual = ''; 
$id_tarjeta_actual = null;   
$modo_edicion = false;

// --- Lógica para PROCESAR ACCIONES (Añadir, Editar por POST; Eliminar por GET) ---

// Procesar acciones POST (Agregar, Editar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo_accion = getPDOConnection();
    
    if (isset($_POST['accion']) && $_POST['accion'] === 'agregar_tarjeta') {
        $nuevo_nombre_tarjeta = trim($_POST['nombre_tarjeta'] ?? '');
        if (!empty($nuevo_nombre_tarjeta)) {
            try {
                $sql_insert = "INSERT INTO tarjetas (nombre_tarjeta) VALUES (:nombre_tarjeta)";
                $stmt_insert = $pdo_accion->prepare($sql_insert);
                $stmt_insert->bindParam(':nombre_tarjeta', $nuevo_nombre_tarjeta);
                if ($stmt_insert->execute()) {
                    $success_message = "Tarjeta '" . htmlspecialchars($nuevo_nombre_tarjeta) . "' agregada exitosamente.";
                } else {
                    $error_message = "Error al agregar la tarjeta.";
                }
            } catch (PDOException $e) {
                if ($e->errorInfo[1] == 1062) { 
                    $error_message = "Error: La tarjeta '" . htmlspecialchars($nuevo_nombre_tarjeta) . "' ya existe.";
                } else {
                    $error_message = "Error de base de datos al agregar: " . $e->getMessage();
                }
            }
        } else {
            $error_message = "El nombre de la tarjeta no puede estar vacío.";
        }
    }
    elseif (isset($_POST['accion']) && $_POST['accion'] === 'editar_tarjeta') {
        $id_tarjeta_a_editar = filter_input(INPUT_POST, 'id_tarjeta_editar', FILTER_VALIDATE_INT);
        $nuevo_nombre_tarjeta_editada = trim($_POST['nombre_tarjeta_editar'] ?? '');

        if ($id_tarjeta_a_editar && !empty($nuevo_nombre_tarjeta_editada)) {
            try {
                $sql_update = "UPDATE tarjetas SET nombre_tarjeta = :nombre_tarjeta WHERE id_tarjeta = :id_tarjeta";
                $stmt_update = $pdo_accion->prepare($sql_update);
                $stmt_update->bindParam(':nombre_tarjeta', $nuevo_nombre_tarjeta_editada);
                $stmt_update->bindParam(':id_tarjeta', $id_tarjeta_a_editar, PDO::PARAM_INT);
                if ($stmt_update->execute()) {
                    $success_message = "Tarjeta actualizada exitosamente.";
                } else {
                    $error_message = "Error al actualizar la tarjeta.";
                }
            } catch (PDOException $e) {
                 if ($e->errorInfo[1] == 1062) { 
                    $error_message = "Error: Ya existe una tarjeta con el nombre '" . htmlspecialchars($nuevo_nombre_tarjeta_editada) . "'.";
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
                $sql_select_uno = "SELECT id_tarjeta, nombre_tarjeta FROM tarjetas WHERE id_tarjeta = :id_tarjeta";
                $stmt_select_uno = $pdo_get_accion->prepare($sql_select_uno);
                $stmt_select_uno->bindParam(':id_tarjeta', $id_get, PDO::PARAM_INT);
                $stmt_select_uno->execute();
                $tarjeta_a_editar = $stmt_select_uno->fetch(PDO::FETCH_ASSOC);
                if ($tarjeta_a_editar) {
                    $nombre_tarjeta_actual = $tarjeta_a_editar['nombre_tarjeta'];
                    $id_tarjeta_actual = $tarjeta_a_editar['id_tarjeta'];
                    $modo_edicion = true;
                } else {
                    $error_message = "No se encontró la tarjeta para editar con ID: " . htmlspecialchars($id_get);
                }
            } 
            // ***** NUEVO: Lógica para Eliminar Tarjeta *****
            elseif ($accion_get === 'eliminar') {
                try {
                    // Verificar si la tarjeta está en uso antes de intentar eliminar
                    // Esto es opcional si la restricción FK ya está como SET NULL o si quieres manejar el error de FK directamente
                    // $sql_check_uso = "SELECT COUNT(*) FROM gastos WHERE id_tarjeta = :id_tarjeta";
                    // $stmt_check = $pdo_get_accion->prepare($sql_check_uso);
                    // $stmt_check->bindParam(':id_tarjeta', $id_get, PDO::PARAM_INT);
                    // $stmt_check->execute();
                    // $count_uso = $stmt_check->fetchColumn();

                    // if ($count_uso > 0) {
                    //     $error_message = "Error: No se puede eliminar la tarjeta (ID: " . htmlspecialchars($id_get) . ") porque está siendo utilizada en " . $count_uso . " gasto(s). Considere la opción de 'Desactivar' en el futuro.";
                    // } else {
                        $sql_delete = "DELETE FROM tarjetas WHERE id_tarjeta = :id_tarjeta";
                        $stmt_delete = $pdo_get_accion->prepare($sql_delete);
                        $stmt_delete->bindParam(':id_tarjeta', $id_get, PDO::PARAM_INT);
                        if ($stmt_delete->execute()) {
                            if ($stmt_delete->rowCount() > 0) {
                                $success_message = "Tarjeta (ID: " . htmlspecialchars($id_get) . ") eliminada exitosamente.";
                            } else {
                                $error_message = "No se encontró la tarjeta con ID: " . htmlspecialchars($id_get) . " para eliminar o ya fue eliminada.";
                            }
                        } else {
                            $error_message = "Error al eliminar la tarjeta.";
                        }
                    // }
                } catch (PDOException $e) {
                    // Error 1451: Cannot delete or update a parent row: a foreign key constraint fails
                    // Esto ocurrirá si la FK en `gastos` para `id_tarjeta` es ON DELETE RESTRICT
                    if ($e->errorInfo[1] == 1451) { 
                        $error_message = "Error: No se puede eliminar la tarjeta (ID: " . htmlspecialchars($id_get) . ") porque está siendo utilizada en uno o más gastos. Si la tarjeta ya no se usa, puede editar su nombre o considerar otras opciones de gestión. (La FK en gastos.id_tarjeta es ON DELETE SET NULL, así que esto no debería ocurrir si la BD está configurada como se planeó inicialmente para tarjetas).";
                    } else {
                        $error_message = "Error de base de datos al eliminar la tarjeta: " . $e->getMessage();
                    }
                }
            }
            // ***** FIN DE Lógica para Eliminar Tarjeta *****
            if(isset($pdo_get_accion)) {$pdo_get_accion = null;}
        } elseif ($accion_get === 'eliminar' || $accion_get === 'editar') {
            $error_message = "ID no proporcionado para la acción '" . htmlspecialchars($accion_get) . "'.";
        }
    }
}


// Obtener todas las tarjetas para listarlas (siempre se hace, después de cualquier acción)
try {
    $pdo_lista = getPDOConnection();
    $sql_select_all = "SELECT id_tarjeta, nombre_tarjeta, fecha_creacion FROM tarjetas ORDER BY nombre_tarjeta ASC";
    $stmt_select_all = $pdo_lista->query($sql_select_all);
    $tarjetas = $stmt_select_all->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    if(empty($success_message) && empty($error_message)) {
        $error_message = "Error al cargar la lista de tarjetas: " . $e->getMessage();
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
            <?php echo $modo_edicion ? 'Editar Tarjeta' : 'Añadir Nueva Tarjeta'; ?>
        </h2>
        <form action="gestionar_tarjetas.php<?php echo $modo_edicion ? '?accion=editar&id=' . $id_tarjeta_actual : ''; ?>" method="POST" class="flex flex-wrap gap-4 items-end">
            <?php if ($modo_edicion): ?>
                <input type="hidden" name="accion" value="editar_tarjeta">
                <input type="hidden" name="id_tarjeta_editar" value="<?php echo htmlspecialchars($id_tarjeta_actual); ?>">
                <div class="control-grupo flex-grow">
                    <label for="nombre_tarjeta_editar" class="block text-sm font-medium text-gray-700">Nombre de la Tarjeta:</label>
                    <input type="text" id="nombre_tarjeta_editar" name="nombre_tarjeta_editar" value="<?php echo htmlspecialchars($nombre_tarjeta_actual); ?>" required class="mt-1">
                </div>
                <div class="control-grupo">
                    <button type="submit" class="bg-amber-500 hover:bg-amber-600">Actualizar Tarjeta</button>
                </div>
                <div class="control-grupo">
                    <a href="gestionar_tarjetas.php" class="w-full text-center bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-md shadow-sm transition duration-150 ease-in-out h-11 leading-7">Cancelar Edición</a>
                </div>
            <?php else: ?>
                <input type="hidden" name="accion" value="agregar_tarjeta">
                <div class="control-grupo flex-grow">
                    <label for="nombre_tarjeta" class="block text-sm font-medium text-gray-700">Nombre de la Tarjeta Nueva:</label>
                    <input type="text" id="nombre_tarjeta" name="nombre_tarjeta" required class="mt-1" placeholder="Ej: Visa Bancomer">
                </div>
                <div class="control-grupo">
                    <button type="submit">Añadir Tarjeta</button>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <div class="control-section bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Tarjetas Existentes</h2>
        <?php if (empty($tarjetas) && empty($error_message) && !($modo_edicion && !$success_message)): ?>
            <p class="text-gray-500">No hay tarjetas registradas todavía.</p>
        <?php elseif (!empty($tarjetas)): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre de la Tarjeta</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Creación</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($tarjetas as $tarjeta): ?>
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($tarjeta['id_tarjeta']); ?></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($tarjeta['nombre_tarjeta']); ?></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars(date("d/m/Y H:i", strtotime($tarjeta['fecha_creacion']))); ?></td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <a href="gestionar_tarjetas.php?accion=editar&id=<?php echo $tarjeta['id_tarjeta']; ?>" class="text-yellow-500 hover:text-yellow-700 action-btn" title="Editar">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a href="gestionar_tarjetas.php?accion=eliminar&id=<?php echo $tarjeta['id_tarjeta']; ?>" 
                                       class="text-red-600 hover:text-red-800 action-btn ml-2" 
                                       title="Eliminar"
                                       onclick="return confirm('¿Estás seguro de que quieres eliminar la tarjeta \'<?php echo htmlspecialchars(addslashes($tarjeta['nombre_tarjeta'])); ?>\'?');">
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
