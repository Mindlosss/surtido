<?php
$page_title = "Registrar Nuevo Gasto"; // Título específico para esta página
require_once 'layout_header.php'; // Incluir el nuevo header
?>

<div class="container mx-auto px-0 md:px-4 py-0">

    <div id="messageContainer" class="mb-4">
        </div>

    <form id="formRegistroGasto" enctype="multipart/form-data" class="form-container-gastos bg-white p-6 md:p-8 rounded-lg shadow-md">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
            
            <div class="form-grupo-gastos">
                <label for="fecha_compra" class="block text-sm font-medium text-gray-700">Fecha de Compra <span class="text-red-500">*</span>:</label>
                <input type="date" id="fecha_compra" name="fecha_compra" required class="mt-1">
            </div>

            <div class="form-grupo-gastos">
                <label for="id_tipo_gasto" class="block text-sm font-medium text-gray-700">Tipo de Gasto <span class="text-red-500">*</span>:</label>
                <select id="id_tipo_gasto" name="id_tipo_gasto" required class="mt-1">
                    <option value="">Cargando tipos...</option>
                </select>
            </div>

            <div class="form-grupo-gastos">
                <label for="id_area_uso" class="block text-sm font-medium text-gray-700">Área de Uso <span class="text-red-500">*</span>:</label>
                <select id="id_area_uso" name="id_area_uso" required class="mt-1">
                    <option value="">Cargando áreas...</option>
                </select>
            </div>
            
            <div class="form-grupo-gastos">
                <label for="id_tarjeta" class="block text-sm font-medium text-gray-700">Tarjeta (Opcional):</label>
                <select id="id_tarjeta" name="id_tarjeta" class="mt-1">
                    <option value="">Cargando tarjetas...</option>
                </select>
            </div>

            <div class="form-grupo-gastos">
                <label for="tarjeta_otro" class="block text-sm font-medium text-gray-700">Otra Tarjeta (Si no está en lista):</label>
                <input type="text" id="tarjeta_otro" name="tarjeta_otro" placeholder="Ej: PayPal, Efectivo" class="mt-1">
            </div>

            <div class="form-grupo-gastos">
                <label for="precio_unitario" class="block text-sm font-medium text-gray-700">Precio Unitario <span class="text-red-500">*</span>:</label>
                <input type="number" id="precio_unitario" name="precio_unitario" step="0.01" min="0" required class="mt-1">
            </div>

            <div class="form-grupo-gastos">
                <label for="cantidad" class="block text-sm font-medium text-gray-700">Cantidad <span class="text-red-500">*</span>:</label>
                <input type="number" id="cantidad" name="cantidad" step="1" min="1" value="1" required class="mt-1">
            </div>

            <div class="form-grupo-gastos">
                <label for="total_gasto" class="block text-sm font-medium text-gray-700">Total Gasto:</label>
                <input type="number" id="total_gasto" name="total_gasto" step="0.01" readonly placeholder="Se calcula automáticamente" class="mt-1 bg-gray-100">
            </div>
            
            <div class="form-grupo-gastos">
                <label for="id_plataforma_compra" class="block text-sm font-medium text-gray-700">Plataforma de Compra (Opcional):</label>
                <select id="id_plataforma_compra" name="id_plataforma_compra" class="mt-1">
                    <option value="">Cargando plataformas...</option>
                </select>
            </div>

            <div class="form-grupo-gastos">
                <label for="plataforma_otro" class="block text-sm font-medium text-gray-700">Otra Plataforma (Si no está en lista):</label>
                <input type="text" id="plataforma_otro" name="plataforma_otro" placeholder="Ej: Tienda local" class="mt-1">
            </div>
            
            <div class="form-grupo-gastos md:col-span-2"> 
                <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción del Producto/Servicio <span class="text-red-500">*</span>:</label>
                <textarea id="descripcion" name="descripcion" rows="3" required class="mt-1"></textarea>
            </div>

            <div class="form-grupo-gastos">
                <label for="folio_factura" class="block text-sm font-medium text-gray-700">Folio Factura/Ticket (Opcional):</label>
                <input type="text" id="folio_factura" name="folio_factura" class="mt-1">
            </div>

            <div class="form-grupo-gastos md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-x-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">¿Tiene Factura (PDF/JPG)? <span class="text-red-500">*</span>:</label>
                    <div class="mt-2 space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" id="factura_si" name="tiene_factura" value="1" required class="form-radio text-indigo-600 h-4 w-4">
                            <span class="ml-2 text-sm text-gray-700">Sí</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" id="factura_no" name="tiene_factura" value="0" checked required class="form-radio text-indigo-600 h-4 w-4">
                            <span class="ml-2 text-sm text-gray-700">No</span>
                        </label>
                    </div>
                </div>
                
                <div> 
                    <label class="block text-sm font-medium text-gray-700">¿Tiene Archivo XML? <span class="text-red-500">*</span>:</label>
                    <div class="mt-2 space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" id="xml_si" name="tiene_xml" value="1" required class="form-radio text-indigo-600 h-4 w-4">
                            <span class="ml-2 text-sm text-gray-700">Sí</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" id="xml_no" name="tiene_xml" value="0" checked required class="form-radio text-indigo-600 h-4 w-4">
                            <span class="ml-2 text-sm text-gray-700">No</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="form-grupo-gastos md:col-span-2">
                <label for="documento_pdf_jpg" class="block text-sm font-medium text-gray-700">Adjuntar Factura (PDF/JPG/PNG, Opcional):</label>
                <input type="file" id="documento_pdf_jpg" name="documento_pdf_jpg" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-md file:border-0
                    file:text-sm file:font-semibold
                    file:bg-indigo-50 file:text-indigo-700
                    hover:file:bg-indigo-100">
            </div>

            <div class="form-grupo-gastos md:col-span-2">
                <label for="documento_xml" class="block text-sm font-medium text-gray-700">Adjuntar Archivo XML (Opcional):</label>
                <input type="file" id="documento_xml" name="documento_xml" accept=".xml" class="mt-1 block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-md file:border-0
                    file:text-sm file:font-semibold
                    file:bg-sky-50 file:text-sky-700  hover:file:bg-sky-100">
            </div>
            <div class="form-grupo-gastos md:col-span-2 mt-4">
                 <button type="submit" id="btnGuardarGasto" class="w-full">Guardar Gasto</button>
            </div>
        </div>
    </form>
</div>

<script>
    // --- Selectores de Elementos ---
    const formRegistroGasto = document.getElementById('formRegistroGasto');
    const btnGuardarGasto = document.getElementById('btnGuardarGasto');
    const messageContainer = document.getElementById('messageContainer');
    const precioUnitarioInput = document.getElementById('precio_unitario');
    const cantidadInput = document.getElementById('cantidad');
    const totalGastoInput = document.getElementById('total_gasto');

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
    }

    // --- Carga de Catálogos y Manejo del Formulario ---
    document.addEventListener('DOMContentLoaded', function() {
        cargarCatalogos();

        if(formRegistroGasto) {
            formRegistroGasto.addEventListener('submit', function(event) {
                event.preventDefault(); 
                
                if(btnGuardarGasto) btnGuardarGasto.disabled = true;
                mostrarMensaje('Procesando...', 'info'); 

                const formData = new FormData(formRegistroGasto);

                fetch('registrar_gasto.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errData => {
                            throw new Error(errData.message || 'Error en el servidor: ' + response.statusText);
                        }).catch(() => {
                            throw new Error('Error en el servidor: ' + response.statusText + '. Respuesta no es JSON.');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        mostrarMensaje(data.message || 'Gasto registrado exitosamente.', 'success');
                        formRegistroGasto.reset(); 
                        calcularTotal(); 
                        if (messageContainer) messageContainer.scrollIntoView({ behavior: 'smooth' });
                    } else {
                        mostrarMensaje(data.message || 'Ocurrió un error al registrar el gasto.', 'error');
                        if (messageContainer) messageContainer.scrollIntoView({ behavior: 'smooth' });
                    }
                })
                .catch(error => {
                    console.error('Error en el envío del formulario:', error);
                    mostrarMensaje('Error de conexión o en el script del servidor: ' + error.message, 'error');
                    if (messageContainer) messageContainer.scrollIntoView({ behavior: 'smooth' });
                })
                .finally(() => {
                    if(btnGuardarGasto) btnGuardarGasto.disabled = false;
                });
            });
        }
    });

    function cargarCatalogos() {
        fetch('obtener_catalogos.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la red o respuesta no OK: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.data) {
                    poblarSelect('id_tipo_gasto', data.data.tipos_gasto, 'id_tipo_gasto', 'nombre_tipo', 'Seleccione un tipo');
                    poblarSelect('id_area_uso', data.data.areas_uso, 'id_area_uso', 'nombre_area', 'Seleccione un área');
                    poblarSelect('id_tarjeta', data.data.tarjetas, 'id_tarjeta', 'nombre_tarjeta', 'Seleccione una tarjeta o ingrese otra');
                    poblarSelect('id_plataforma_compra', data.data.plataformas_compra, 'id_plataforma_compra', 'nombre_plataforma', 'Seleccione una plataforma o ingrese otra');
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

    function poblarSelect(selectId, items, valueField, textField, placeholderText) {
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
                selectElement.appendChild(option);
            });
        } else {
            console.warn('No hay items para poblar el select:', selectId);
        }
    }
    
    function mostrarMensaje(mensaje, tipo = 'info') { 
        if (messageContainer) {
            messageContainer.innerHTML = ''; 
            const alertDiv = document.createElement('div');
            alertDiv.className = `mensaje mensaje-${tipo}`; 
            alertDiv.textContent = mensaje;
            messageContainer.appendChild(alertDiv);

            if (tipo === 'success' || tipo === 'error') { 
                setTimeout(() => {
                    // No se remueve automáticamente para que el usuario lo vea.
                }, 7000); 
            }
        }
    }
</script>

<?php
require_once 'layout_footer.php'; // Incluir el nuevo footer
?>
