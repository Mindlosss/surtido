<?php
$page_title = "Editar Gasto";
require_once 'layout_header.php';
require_once 'config/db.php';

// Inicializar variables
$gasto_actual = null;
$error_message_carga = '';
$id_gasto_a_editar = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id_gasto_a_editar) {
    $error_message_carga = "ID de gasto no válido o no proporcionado.";
    // Podrías redirigir o simplemente mostrar el error y no el formulario.
} else {
    try {
        $pdo = getPDOConnection();
        $sql = "SELECT * FROM gastos WHERE id_gasto = :id_gasto";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_gasto', $id_gasto_a_editar, PDO::PARAM_INT);
        $stmt->execute();
        $gasto_actual = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$gasto_actual) {
            $error_message_carga = "No se encontró ningún gasto con el ID: " . htmlspecialchars($id_gasto_a_editar);
        }
    } catch (PDOException $e) {
        $error_message_carga = "Error al cargar los datos del gasto: " . $e->getMessage();
    } finally {
        if (isset($pdo)) {
            $pdo = null;
        }
    }
}
?>

<div class="container mx-auto px-0 md:px-4 py-0">

    <?php if (!empty($error_message_carga)): ?>
        <div class="mensaje mensaje-error my-4"><?php echo $error_message_carga; ?></div>
    <?php endif; ?>

    <?php if ($gasto_actual): // Solo mostrar el formulario si se cargaron los datos del gasto ?>
    <div id="messageContainer" class="mb-4">
        </div>

    <form id="formEdicionGasto" action="actualizar_gasto.php" method="POST" enctype="multipart/form-data" class="form-container-gastos bg-white p-6 md:p-8 rounded-lg shadow-md">
        <input type="hidden" name="id_gasto" value="<?php echo htmlspecialchars($gasto_actual['id_gasto']); ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
            
            <div class="form-grupo-gastos">
                <label for="fecha_compra">Fecha de Compra <span class="text-red-500">*</span>:</label>
                <input type="date" id="fecha_compra" name="fecha_compra" required class="mt-1" value="<?php echo htmlspecialchars($gasto_actual['fecha_compra']); ?>">
            </div>

            <div class="form-grupo-gastos">
                <label for="id_tipo_gasto">Tipo de Gasto <span class="text-red-500">*</span>:</label>
                <select id="id_tipo_gasto" name="id_tipo_gasto" required class="mt-1">
                    <option value="">Cargando tipos...</option>
                    </select>
            </div>

            <div class="form-grupo-gastos">
                <label for="id_area_uso">Área de Uso <span class="text-red-500">*</span>:</label>
                <select id="id_area_uso" name="id_area_uso" required class="mt-1">
                    <option value="">Cargando áreas...</option>
                </select>
            </div>
            
            <div class="form-grupo-gastos">
                <label for="id_tarjeta">Tarjeta:</label>
                <select id="id_tarjeta" name="id_tarjeta" class="mt-1">
                    <option value="">Cargando tarjetas...</option>
                </select>
            </div>

            <div class="form-grupo-gastos">
                <label for="tarjeta_otro">Otra Tarjeta:</label>
                <input type="text" id="tarjeta_otro" name="tarjeta_otro" placeholder="Ej: PayPal, Efectivo" class="mt-1" value="<?php echo htmlspecialchars($gasto_actual['tarjeta_otro'] ?? ''); ?>">
            </div>

            <div class="form-grupo-gastos">
                <label for="precio_unitario">Precio Unitario <span class="text-red-500">*</span>:</label>
                <input type="number" id="precio_unitario" name="precio_unitario" step="0.01" min="0" required class="mt-1" value="<?php echo htmlspecialchars($gasto_actual['precio_unitario']); ?>">
            </div>

            <div class="form-grupo-gastos">
                <label for="cantidad">Cantidad <span class="text-red-500">*</span>:</label>
                <input type="number" id="cantidad" name="cantidad" step="1" min="1" required class="mt-1" value="<?php echo htmlspecialchars($gasto_actual['cantidad']); ?>">
            </div>

            <div class="form-grupo-gastos">
                <label for="total_gasto">Total Gasto:</label>
                <input type="number" id="total_gasto" name="total_gasto" step="0.01" readonly placeholder="Se calcula automáticamente" class="mt-1 bg-gray-100" value="<?php echo htmlspecialchars($gasto_actual['total_gasto']); ?>">
            </div>
            
            <div class="form-grupo-gastos">
                <label for="id_plataforma_compra">Plataforma de Compra:</label>
                <select id="id_plataforma_compra" name="id_plataforma_compra" class="mt-1">
                    <option value="">Cargando plataformas...</option>
                </select>
            </div>

            <div class="form-grupo-gastos">
                <label for="plataforma_otro">Otra Plataforma:</label>
                <input type="text" id="plataforma_otro" name="plataforma_otro" placeholder="Ej: Tienda local" class="mt-1" value="<?php echo htmlspecialchars($gasto_actual['plataforma_otro'] ?? ''); ?>">
            </div>
            
            <div class="form-grupo-gastos md:col-span-2"> 
                <label for="descripcion">Descripción <span class="text-red-500">*</span>:</label>
                <textarea id="descripcion" name="descripcion" rows="3" required class="mt-1"><?php echo htmlspecialchars($gasto_actual['descripcion']); ?></textarea>
            </div>

            <div class="form-grupo-gastos">
                <label for="folio_factura">Folio Factura/Ticket:</label>
                <input type="text" id="folio_factura" name="folio_factura" class="mt-1" value="<?php echo htmlspecialchars($gasto_actual['folio_factura'] ?? ''); ?>">
            </div>

            <div class="form-grupo-gastos md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-x-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">¿Tiene Factura (PDF/JPG)? <span class="text-red-500">*</span>:</label>
                    <div class="mt-2 space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="tiene_factura" value="1" required class="form-radio text-indigo-600 h-4 w-4" <?php echo ($gasto_actual['tiene_factura'] == 1) ? 'checked' : ''; ?>>
                            <span class="ml-2 text-sm text-gray-700">Sí</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="tiene_factura" value="0" required class="form-radio text-indigo-600 h-4 w-4" <?php echo ($gasto_actual['tiene_factura'] == 0) ? 'checked' : ''; ?>>
                            <span class="ml-2 text-sm text-gray-700">No</span>
                        </label>
                    </div>
                </div>
                
                <div> 
                    <label class="block text-sm font-medium text-gray-700">¿Tiene Archivo XML? <span class="text-red-500">*</span>:</label>
                    <div class="mt-2 space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="tiene_xml" value="1" required class="form-radio text-indigo-600 h-4 w-4" <?php echo ($gasto_actual['tiene_xml'] == 1) ? 'checked' : ''; ?>>
                            <span class="ml-2 text-sm text-gray-700">Sí</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="tiene_xml" value="0" required class="form-radio text-indigo-600 h-4 w-4" <?php echo ($gasto_actual['tiene_xml'] == 0) ? 'checked' : ''; ?>>
                            <span class="ml-2 text-sm text-gray-700">No</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="form-grupo-gastos md:col-span-2">
                <label for="documento_pdf_jpg" class="block text-sm font-medium text-gray-700">Adjuntar Nueva Factura (PDF/JPG/PNG):</label>
                <?php if (!empty($gasto_actual['nombre_archivo_doc'])): ?>
                    <p class="text-xs text-gray-500 mt-1">Actual: <a href="uploads/<?php echo htmlspecialchars($gasto_actual['nombre_archivo_doc']); ?>" target="_blank" class="text-indigo-600 hover:underline"><?php echo htmlspecialchars($gasto_actual['nombre_archivo_doc']); ?></a> (Dejar vacío para no cambiar)</p>
                <?php endif; ?>
                <input type="file" id="documento_pdf_jpg" name="documento_pdf_jpg" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <input type="hidden" name="documento_pdf_jpg_actual" value="<?php echo htmlspecialchars($gasto_actual['nombre_archivo_doc'] ?? ''); ?>">
                <input type="hidden" name="ruta_documento_pdf_jpg_actual" value="<?php echo htmlspecialchars($gasto_actual['ruta_archivo_doc'] ?? ''); ?>">
            </div>

            <div class="form-grupo-gastos md:col-span-2">
                <label for="documento_xml" class="block text-sm font-medium text-gray-700">Adjuntar Nuevo Archivo XML:</label>
                 <?php if (!empty($gasto_actual['nombre_archivo_xml'])): ?>
                    <p class="text-xs text-gray-500 mt-1">Actual: <a href="uploads/<?php echo htmlspecialchars($gasto_actual['nombre_archivo_xml']); ?>" target="_blank" class="text-sky-600 hover:underline"><?php echo htmlspecialchars($gasto_actual['nombre_archivo_xml']); ?></a> (Dejar vacío para no cambiar)</p>
                <?php endif; ?>
                <input type="file" id="documento_xml" name="documento_xml" accept=".xml" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                <input type="hidden" name="documento_xml_actual" value="<?php echo htmlspecialchars($gasto_actual['nombre_archivo_xml'] ?? ''); ?>">
                <input type="hidden" name="ruta_documento_xml_actual" value="<?php echo htmlspecialchars($gasto_actual['ruta_archivo_xml'] ?? ''); ?>">
            </div>

            <div class="form-grupo-gastos md:col-span-2 mt-4">
                 <button type="submit" id="btnActualizarGasto" class="w-full">Actualizar Gasto</button>
            </div>
        </div>
    </form>
    <?php else: ?>
        <?php if (empty($error_message_carga) && $id_gasto_a_editar): // Si no hubo error de carga pero no se encontró el gasto ?>
            <div class="mensaje mensaje-info my-4">No se encontró el gasto con el ID especificado para editar. <a href="listar_gastos.php" class="font-semibold hover:underline">Volver al listado</a>.</div>
        <?php elseif (!$id_gasto_a_editar && empty($error_message_carga)): // Si no se proporcionó ID ?>
             <div class="mensaje mensaje-info my-4">No se especificó un ID de gasto para editar. <a href="listar_gastos.php" class="font-semibold hover:underline">Volver al listado</a>.</div>
        <?php endif; ?>
    <?php endif; // Fin de if ($gasto_actual) ?>
</div>

<script>
    // --- Selectores de Elementos ---
    const formEdicionGasto = document.getElementById('formEdicionGasto');
    // const btnActualizarGasto = document.getElementById('btnActualizarGasto'); // Aún no se usa para AJAX
    const messageContainer = document.getElementById('messageContainer'); // Ya definido en layout_header
    const precioUnitarioInput = document.getElementById('precio_unitario');
    const cantidadInput = document.getElementById('cantidad');
    const totalGastoInput = document.getElementById('total_gasto');

    // --- Variables con datos PHP para JS (valores actuales para selects) ---
    const idTipoGastoActual = <?php echo json_encode($gasto_actual['id_tipo_gasto'] ?? null); ?>;
    const idAreaUsoActual = <?php echo json_encode($gasto_actual['id_area_uso'] ?? null); ?>;
    const idTarjetaActual = <?php echo json_encode($gasto_actual['id_tarjeta'] ?? null); ?>;
    const idPlataformaActual = <?php echo json_encode($gasto_actual['id_plataforma_compra'] ?? null); ?>;


    // --- Cálculo automático del total ---
    function calcularTotal() {
        if (!precioUnitarioInput || !cantidadInput || !totalGastoInput) return;
        const precio = parseFloat(precioUnitarioInput.value) || 0;
        const cantidad = parseInt(cantidadInput.value) || 0;
        const total = precio * cantidad;
        totalGastoInput.value = total.toFixed(2);
    }

    if(precioUnitarioInput && cantidadInput) {
        precioUnitarioInput.addEventListener('input', calcularTotal);
        cantidadInput.addEventListener('input', calcularTotal);
        // Calcular total inicial al cargar la página (ya que los valores están pre-llenados)
        // calcularTotal(); // Se llama después de cargar catálogos para asegurar que los selects estén listos si afectan el cálculo
    }

    // --- Carga de Catálogos y Manejo del Formulario ---
    document.addEventListener('DOMContentLoaded', function() {
        if (<?php echo $gasto_actual ? 'true' : 'false'; ?>) { // Solo cargar catálogos si hay datos de gasto
            cargarCatalogosYSeleccionarActuales();
            calcularTotal(); // Calcular total después de que los campos numéricos estén poblados por PHP
        }

        // El envío AJAX se implementará después, por ahora es un submit normal a actualizar_gasto.php
        /*
        if(formEdicionGasto) {
            formEdicionGasto.addEventListener('submit', function(event) {
                event.preventDefault(); 
                // Lógica AJAX para actualizar...
            });
        }
        */
    });

    function cargarCatalogosYSeleccionarActuales() {
        fetch('obtener_catalogos.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la red o respuesta no OK: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.data) {
                    poblarSelect('id_tipo_gasto', data.data.tipos_gasto, 'id_tipo_gasto', 'nombre_tipo', 'Seleccione un tipo', idTipoGastoActual);
                    poblarSelect('id_area_uso', data.data.areas_uso, 'id_area_uso', 'nombre_area', 'Seleccione un área', idAreaUsoActual);
                    poblarSelect('id_tarjeta', data.data.tarjetas, 'id_tarjeta', 'nombre_tarjeta', 'Seleccione una tarjeta', idTarjetaActual);
                    poblarSelect('id_plataforma_compra', data.data.plataformas_compra, 'id_plataforma_compra', 'nombre_plataforma', 'Seleccione una plataforma', idPlataformaActual);
                } else {
                    console.error('Error al obtener catálogos:', data.message);
                    mostrarMensaje('Error al cargar datos de los catálogos: ' + (data.message || 'Respuesta no exitosa.'), 'error');
                }
            })
            .catch(error => {
                console.error('Error en fetch para cargar catálogos:', error);
                mostrarMensaje('No se pudieron cargar los catálogos. Verifique la conexión o el script del servidor. Detalles: ' + error.message, 'error');
            });
    }

    function poblarSelect(selectId, items, valueField, textField, placeholderText, valorActual) {
        const selectElement = document.getElementById(selectId);
        if (!selectElement) {
            console.warn('Elemento select no encontrado:', selectId);
            return;
        }
        selectElement.innerHTML = `<option value="">${placeholderText}</option>`; 
        if (items && items.length > 0) {
            items.forEach(item => {
                const option = document.createElement('option');
                option.value = item[valueField];
                option.textContent = item[textField];
                if (valorActual && item[valueField] == valorActual) { // Comparar como string o número según sea el caso
                    option.selected = true;
                }
                selectElement.appendChild(option);
            });
        } else {
            console.warn('No hay items para poblar el select:', selectId);
        }
    }
    
    function mostrarMensaje(mensaje, tipo = 'info') { 
        const localMessageContainer = document.getElementById('messageContainer'); // Usar el local
        if (localMessageContainer) {
            localMessageContainer.innerHTML = ''; 
            const alertDiv = document.createElement('div');
            alertDiv.className = `mensaje mensaje-${tipo}`; 
            alertDiv.textContent = mensaje;
            localMessageContainer.appendChild(alertDiv);
        }
    }
</script>

<?php
require_once 'layout_footer.php'; // Incluir el nuevo footer
?>
