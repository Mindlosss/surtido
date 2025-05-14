<?php
// Archivo: dashboard_vista.php

$page_title = "Dashboard de Gastos"; // Título para el encabezado del layout
require_once 'layout_header.php'; // Incluye la barra lateral, encabezado y estilos base
?>

<style>
    /* Estilos específicos del dashboard */
    .kpi-card h3 {
        font-size: 1.1em;
        color: #4f46e5; /* Indigo-600 de Tailwind */
        margin-top: 0;
        margin-bottom: 0.75rem; /* mb-3 */
        border-bottom: 1px solid #e5e7eb; /* border-gray-200 */
        padding-bottom: 0.5rem; /* pb-2 */
    }
    .kpi-card p {
        margin: 0.25rem 0; /* my-1 */
        text-align: center;
    }
    .kpi-card p.valor-principal { 
        font-size: 1.875rem; /* text-3xl */
        font-weight: 700; /* font-bold */
        color: #10b981; /* Emerald-500 o Green-600 */
    }
    .kpi-card p.subtext {
        font-size: 0.875rem; /* text-sm */
        font-weight: 400; /* font-normal */
        color: #6b7280; /* text-gray-500 */
    }
    .kpi-card p.monto-sin-factura {
        color: #ef4444; /* Red-500 */
        font-weight: 600; /* font-semibold */
    }
    .data-section h3 {
        font-size: 1.125rem; /* text-lg */
        color: #374151; /* text-gray-700 */
        margin-top: 0;
        margin-bottom: 1rem; /* mb-4 */
        font-weight: 600; /* font-semibold */
    }
    .data-section table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem; /* text-sm */
    }
    .data-section th, .data-section td {
        border: 1px solid #e5e7eb; /* border-gray-200 */
        padding: 0.75rem; /* p-3 */
        text-align: left;
    }
    .data-section th {
        background-color: #f9fafb; /* bg-gray-50 */
        font-weight: 600; /* font-semibold */
        color: #4b5563; /* text-gray-600 */
    }
</style>

<div class="container mx-auto px-0 md:px-4 py-0">
    <div class="navegacion-botones mb-6 mt-0">
        <a href="captura_gasto.php" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-150 ease-in-out inline-flex items-center mb-2 md:mb-0">
            <i class="fas fa-plus mr-2"></i>Registrar Gasto
        </a>
        <a href="listar_gastos.php" class="bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-150 ease-in-out inline-flex items-center mb-2 md:mb-0">
            <i class="fas fa-list-ul mr-2"></i>Listado Completo
        </a>
        <a href="resumen_gastos_vista.php" class="bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-150 ease-in-out inline-flex items-center">
            <i class="fas fa-chart-line mr-2"></i>Resúmenes
        </a>
    </div>

    <div class="filtros-dashboard">
        <div class="filtro-grupo">
            <label for="fechaDesde">Desde:</label>
            <input type="date" id="fechaDesde">
        </div>
        <div class="filtro-grupo">
            <label for="fechaHasta">Hasta:</label>
            <input type="date" id="fechaHasta">
        </div>
        <div class="filtro-grupo">
            <label for="filtroTipoGasto">Tipo de Gasto:</label>
            <select id="filtroTipoGasto">
                <option value="">Todos</option>
            </select>
        </div>
        <div class="filtro-grupo">
            <label for="filtroAreaUso">Área de Uso:</label>
            <select id="filtroAreaUso">
                <option value="">Todas</option>
            </select>
        </div>
        <div class="filtro-grupo">
            <label for="filtroPlataforma">Plataforma:</label>
            <select id="filtroPlataforma">
                <option value="">Todas</option>
            </select>
        </div>
        <div class="filtro-grupo">
            <button id="btnAplicarFiltros">Aplicar Filtros</button>
        </div>
    </div>

    <div id="mensajeContainer" style="display:none;"></div>
    
    <div id="dashboardContent" style="display:none;">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="kpi-card bg-white p-5 rounded-xl shadow-lg border border-gray-200">
                <h3>Total de Compras</h3>
                <p class="valor-principal" id="kpiMontoTotal">$0.00</p>
                <p class="subtext" id="kpiCantidadTotal">0 compras</p>
            </div>
            <div class="kpi-card bg-white p-5 rounded-xl shadow-lg border border-gray-200">
                <h3>Facturación</h3>
                <p class="valor-principal" id="kpiMontoConFactura">$0.00</p>
                <p class="subtext mb-1">(Con Factura)</p>
                <p class="monto-sin-factura valor-principal" id="kpiMontoSinFactura">$0.00</p>
                <p class="subtext">(Sin Factura)</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="data-section bg-white p-5 rounded-xl shadow-lg border border-gray-200" id="seccionPorTipoGasto">
                <h3>Gastos por Tipo</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full"><thead><tr><th>Tipo</th><th>Cantidad</th><th>Total</th></tr></thead><tbody class="divide-y divide-gray-200"></tbody></table>
                </div>
            </div>
            <div class="data-section bg-white p-5 rounded-xl shadow-lg border border-gray-200" id="seccionPorAreaUso">
                <h3>Gastos por Área de Uso</h3>
                 <div class="overflow-x-auto">
                    <table class="min-w-full"><thead><tr><th>Área</th><th>Cantidad</th><th>Total</th></tr></thead><tbody class="divide-y divide-gray-200"></tbody></table>
                </div>
            </div>
            <div class="data-section bg-white p-5 rounded-xl shadow-lg border border-gray-200" id="seccionPorPlataforma">
                <h3>Gastos por Plataforma</h3>
                 <div class="overflow-x-auto">
                    <table class="min-w-full"><thead><tr><th>Plataforma</th><th>Cantidad</th><th>Total</th></tr></thead><tbody class="divide-y divide-gray-200"></tbody></table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // El JavaScript se mantiene igual que en la versión anterior del artefacto.
    // ... (todo el bloque <script> que ya tienes) ...

    const fechaDesdeInput = document.getElementById('fechaDesde');
    const fechaHastaInput = document.getElementById('fechaHasta');
    const filtroTipoGastoSelect = document.getElementById('filtroTipoGasto');
    const filtroAreaUsoSelect = document.getElementById('filtroAreaUso');
    const filtroPlataformaSelect = document.getElementById('filtroPlataforma');
    const btnAplicarFiltros = document.getElementById('btnAplicarFiltros');
    const mensajeContainer = document.getElementById('mensajeContainer');
    const dashboardContentDiv = document.getElementById('dashboardContent');

    document.addEventListener('DOMContentLoaded', function() {
        cargarFiltrosCatalogos(); 
        cargarDatosDashboard(); 

        if(btnAplicarFiltros) { 
            btnAplicarFiltros.addEventListener('click', cargarDatosDashboard);
        }
    });

    function cargarFiltrosCatalogos() {
        fetch('obtener_catalogos.php') 
            .then(response => response.ok ? response.json() : Promise.reject({ status: response.status, statusText: response.statusText }))
            .then(data => {
                if (data.success && data.data) {
                    poblarSelectFiltro('filtroTipoGasto', data.data.tipos_gasto, 'id_tipo_gasto', 'nombre_tipo');
                    poblarSelectFiltro('filtroAreaUso', data.data.areas_uso, 'id_area_uso', 'nombre_area');
                    poblarSelectFiltro('filtroPlataforma', data.data.plataformas_compra, 'id_plataforma_compra', 'nombre_plataforma');
                } else {
                    console.error('Error en datos de catálogos:', data.message);
                    mostrarMensaje('No se pudieron cargar las opciones de filtro: ' + (data.message || 'Respuesta inválida de la API de catálogos.'), 'error');
                }
            })
            .catch(error => {
                console.error('Error en fetch para catálogos de filtro:', error);
                mostrarMensaje('Error de red al cargar opciones de filtro. Verifique la consola.', 'error');
            });
    }

    function poblarSelectFiltro(selectId, items, valueField, textField) {
        const selectElement = document.getElementById(selectId);
        if (!selectElement) {
             console.warn('Elemento select de filtro no encontrado:', selectId);
             return;
        }
        const placeholderOption = selectElement.options[0]; 
        selectElement.innerHTML = ''; 
        if(placeholderOption) selectElement.appendChild(placeholderOption); 
        
        if (items && items.length > 0) {
            items.forEach(item => {
                const option = document.createElement('option');
                option.value = item[valueField];
                option.textContent = item[textField];
                selectElement.appendChild(option);
            });
        }
    }
    
    function cargarDatosDashboard() {
        mostrarMensaje('Cargando datos del dashboard...', 'info');
        if(dashboardContentDiv) dashboardContentDiv.style.display = 'none';

        let url = 'obtener_dashboard_data_api.php?';
        const params = new URLSearchParams();

        if (fechaDesdeInput && fechaDesdeInput.value) params.append('fecha_desde', fechaDesdeInput.value);
        if (fechaHastaInput && fechaHastaInput.value) params.append('fecha_hasta', fechaHastaInput.value);
        if (filtroTipoGastoSelect && filtroTipoGastoSelect.value) params.append('id_tipo_gasto', filtroTipoGastoSelect.value);
        if (filtroAreaUsoSelect && filtroAreaUsoSelect.value) params.append('id_area_uso', filtroAreaUsoSelect.value);
        if (filtroPlataformaSelect && filtroPlataformaSelect.value) params.append('id_plataforma_compra', filtroPlataformaSelect.value);
        
        url += params.toString();

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errData => { 
                        throw new Error(errData.message || `Error HTTP ${response.status}: ${response.statusText}`);
                    }).catch(() => { 
                        throw new Error(`Error HTTP ${response.status}: ${response.statusText}. Respuesta no es JSON o está vacía.`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if(mensajeContainer) mensajeContainer.style.display = 'none';
                if (data.success && data.data) {
                    if(dashboardContentDiv) dashboardContentDiv.style.display = 'block';
                    actualizarDashboardUI(data.data);
                } else {
                    mostrarMensaje(data.message || 'No se pudieron cargar los datos del dashboard.', 'error');
                     if(dashboardContentDiv) dashboardContentDiv.style.display = 'none'; 
                }
            })
            .catch(error => {
                console.error('Error al cargar datos del dashboard:', error);
                mostrarMensaje('Error crítico al cargar datos: ' + error.message, 'error');
                if(dashboardContentDiv) dashboardContentDiv.style.display = 'none'; 
            });
    }

    function actualizarDashboardUI(data) {
        console.log('Datos recibidos para el dashboard UI:', data); // Línea de depuración

        if (!data) {
            console.error("No hay datos para actualizar el UI del dashboard.");
            mostrarMensaje('No se recibieron datos para el dashboard.', 'info');
            return;
        }
        
        const kpiMontoTotalEl = document.getElementById('kpiMontoTotal');
        const kpiCantidadTotalEl = document.getElementById('kpiCantidadTotal');
        const kpiMontoConFacturaEl = document.getElementById('kpiMontoConFactura');
        const kpiMontoSinFacturaEl = document.getElementById('kpiMontoSinFactura');

        if (kpiMontoTotalEl) kpiMontoTotalEl.textContent = `$${parseFloat(data.totales_compras?.monto_total_compras || 0).toFixed(2)}`;
        if (kpiCantidadTotalEl) kpiCantidadTotalEl.textContent = `${parseInt(data.totales_compras?.cantidad_total_compras || 0)} compras`;
        if (kpiMontoConFacturaEl) kpiMontoConFacturaEl.textContent = `$${parseFloat(data.totales_facturas?.monto_con_factura || 0).toFixed(2)}`;
        if (kpiMontoSinFacturaEl) kpiMontoSinFacturaEl.textContent = `$${parseFloat(data.totales_facturas?.monto_sin_factura || 0).toFixed(2)}`;

        actualizarTablaDesglose('seccionPorTipoGasto', data.por_tipo_gasto, 'nombre_tipo', 'cantidad_por_tipo', 'total_por_tipo');
        actualizarTablaDesglose('seccionPorAreaUso', data.por_area_uso, 'nombre_area', 'cantidad_por_area', 'total_por_area');
        actualizarTablaDesglose('seccionPorPlataforma', data.por_plataforma, 'plataforma', 'cantidad_por_plataforma', 'total_por_plataforma');
    }

    function actualizarTablaDesglose(sectionId, items, nombreField, cantidadField, totalField) {
        const section = document.getElementById(sectionId); // Esta es la línea que fallaba
        if (!section) {
            console.warn("Sección para tabla de desglose no encontrada:", sectionId); // Esto es lo que veías en la consola
            return;
        }
        const tbody = section.querySelector('table tbody');
        if (!tbody) {
            console.warn("TBODY no encontrado en la sección:", sectionId);
            return;
        }
        
        tbody.innerHTML = ''; 

        if (items && items.length > 0) {
            items.forEach(item => {
                const row = tbody.insertRow();
                row.insertCell().textContent = item[nombreField] || 'N/D';
                row.insertCell().textContent = parseInt(item[cantidadField] || 0);
                row.insertCell().textContent = `$${parseFloat(item[totalField] || 0).toFixed(2)}`;
            });
        } else {
            const row = tbody.insertRow();
            const cell = row.insertCell();
            cell.colSpan = 3;
            cell.textContent = 'No hay datos para mostrar con los filtros actuales.';
            cell.style.textAlign = 'center';
            cell.style.fontStyle = 'italic';
        }
    }

    function mostrarMensaje(texto, tipo) { 
        if(mensajeContainer) {
            mensajeContainer.textContent = texto;
            mensajeContainer.className = 'mensaje mensaje-' + tipo; 
            mensajeContainer.style.display = 'block';
        } else {
            console.warn("Contenedor de mensajes no encontrado.")
        }
    }
</script>

<?php
require_once 'layout_footer.php'; // Incluye el pie de página del layout
?>
