<?php
$page_title = "Reporte Anual de Gastos por Plataforma"; // Título específico para esta página
require_once 'layout_header.php'; // Incluir el nuevo header
?>

<div class="container mx-auto px-0 md:px-4 py-0">

    <div class="navegacion-botones my-4"> 
        <a href="captura_gasto.php" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-150 ease-in-out inline-block mb-2 md:mb-0">
            <i class="fas fa-plus mr-2"></i>Registrar Gasto
        </a>
        <a href="listar_gastos.php" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-150 ease-in-out inline-block mb-2 md:mb-0">
            <i class="fas fa-list-ul mr-2"></i>Listado Completo
        </a>
        <a href="dashboard_vista.php" class="bg-purple-500 hover:bg-purple-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-150 ease-in-out inline-block">
            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
        </a>
    </div>

    <div class="filtros-container"> 
        <div class="filtro-grupo">
            <label for="anioReporte">Seleccione el Año:</label>
            <input type="number" id="anioReporte" value="<?php echo date('Y'); ?>" min="2000" max="<?php echo date('Y') + 5; ?>" class="mt-1">
        </div>
        <div class="filtro-grupo"> 
            <button id="btnVerReporteAnual">Ver Reporte</button>
        </div>
    </div>

    <div id="mensajeContainer" class="mensaje" style="display:none;"></div>
    
    <div id="resultadosReporteContainer" class="mt-6 bg-white p-4 md:p-6 rounded-lg shadow-md overflow-x-auto" style="display:none;">
        </div>

</div>

<script>
    const anioReporteInput = document.getElementById('anioReporte');
    const btnVerReporteAnual = document.getElementById('btnVerReporteAnual');
    const resultadosReporteContainer = document.getElementById('resultadosReporteContainer');
    const mensajeContainer = document.getElementById('mensajeContainer');

    if (btnVerReporteAnual) {
        btnVerReporteAnual.addEventListener('click', function() {
            const anio = anioReporteInput.value;
            if (!anio || anio < 2000 || anio > (new Date().getFullYear() + 5) ) {
                mostrarMensaje('Por favor, ingrese un año válido.', 'error');
                return;
            }

            mostrarMensaje('Cargando reporte anual...', 'info');
            if(resultadosReporteContainer) {
                resultadosReporteContainer.style.display = 'none';
                resultadosReporteContainer.innerHTML = ''; 
            }

            const url = `obtener_resumen_gastos_api.php?anio=${anio}`;

            fetch(url)
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
                    if(mensajeContainer) mensajeContainer.style.display = 'none'; 
                    if (data.success && data.reporte_anual) {
                        if(resultadosReporteContainer) resultadosReporteContainer.style.display = 'block';
                        mostrarReporteAnualPlataforma(data.reporte_anual, data.anio_consultado, data.meses_nombres);
                    } else {
                        mostrarMensaje(data.message || 'No se encontraron datos para el reporte o ocurrió un error.', 'info');
                         if(resultadosReporteContainer) resultadosReporteContainer.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error al obtener reporte anual:', error);
                    mostrarMensaje('Error al cargar el reporte: ' + error.message, 'error');
                     if(resultadosReporteContainer) resultadosReporteContainer.style.display = 'none';
                });
        });
    }

    function mostrarMensaje(texto, tipo) { 
        if(mensajeContainer) {
            mensajeContainer.textContent = texto;
            mensajeContainer.className = 'mensaje mensaje-' + tipo; 
            mensajeContainer.style.display = 'block';
        }
    }

    function escapeHtml(unsafe) {
        if (typeof unsafe !== 'string') {
            if (typeof unsafe === 'number' || typeof unsafe === 'boolean') {
                return unsafe.toString();
            }
            return '';
        }
        return unsafe
             .replace(/&/g, "&amp;")
             .replace(/</g, "&lt;")
             .replace(/>/g, "&gt;")
             .replace(/"/g, "&quot;")
             .replace(/'/g, "&#039;");
    }

    function mostrarReporteAnualPlataforma(reporte, anio, mesesNombres) {
        if(!resultadosReporteContainer) return;

        let html = `<h2 class="text-xl font-semibold text-gray-700 mb-4">Reporte de Gastos por Plataforma - Año ${escapeHtml(anio.toString())}</h2>`;
        
        if (reporte && reporte.length > 0) {
            html += `<table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider border-r border-gray-300">Plataforma</th>`;
            
            mesesNombres.forEach(nombreMes => {
                html += `<th class="px-3 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider border-r border-gray-300">${escapeHtml(nombreMes)}</th>`;
            });
            
            html += `<th class="px-3 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider bg-gray-200">Total Anual</th>
                     </tr></thead>
                     <tbody class="bg-white divide-y divide-gray-200">`;

            const totalesMensuales = array_fill(1, 12, 0.00); // Para la fila de totales al final
            let granTotalAnual = 0.00;

            reporte.forEach(itemPlataforma => {
                html += `<tr>
                            <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-800 border-r border-gray-300">${escapeHtml(itemPlataforma.plataforma)}</td>`;
                for (let i = 1; i <= 12; i++) {
                    const gastoMes = parseFloat(itemPlataforma.gastos_mensuales[i] || 0).toFixed(2);
                    html += `<td class="px-3 py-2 whitespace-nowrap text-sm text-gray-700 text-right border-r border-gray-300">${gastoMes == 0.00 ? '-' : '$' + gastoMes}</td>`;
                    totalesMensuales[i] += parseFloat(itemPlataforma.gastos_mensuales[i] || 0);
                }
                const totalAnualPlat = parseFloat(itemPlataforma.total_anual_plataforma || 0).toFixed(2);
                html += `<td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 text-right font-semibold bg-gray-100">$${totalAnualPlat}</td>
                         </tr>`;
                granTotalAnual += parseFloat(itemPlataforma.total_anual_plataforma || 0);
            });

            // Fila de Totales Generales por Mes
            html += `<tr class="bg-gray-200 font-semibold text-gray-800">
                        <td class="px-3 py-2 text-left text-sm uppercase border-r border-gray-300">Total General</td>`;
            for (let i = 1; i <= 12; i++) {
                html += `<td class="px-3 py-2 text-right text-sm border-r border-gray-300">$${totalesMensuales[i].toFixed(2)}</td>`;
            }
            html += `<td class="px-3 py-2 text-right text-sm font-bold">$${granTotalAnual.toFixed(2)}</td>
                     </tr>`;

            html += `</tbody></table>`;
        } else {
            html += `<p class="text-gray-600 italic">No se encontraron gastos para el año ${escapeHtml(anio.toString())}.</p>`;
        }
        resultadosReporteContainer.innerHTML = html;
    }

    // Pequeña función helper para simular array_fill de PHP en JS
    function array_fill(startIndex, num, mixedVal) {
        let i = 0, key, arr = {}; // Usar objeto para claves no necesariamente secuenciales desde 0
        if (startIndex === 0) { // Si es array indexado desde 0
            arr = [];
            for (i = 0; i < num; i++) {
                arr[i] = mixedVal;
            }
        } else { // Si es asociativo o indexado desde otro número (como 1 para meses)
             for (i = 0; i < num; i++) {
                key = startIndex + i;
                arr[key] = mixedVal;
            }
        }
        return arr;
    }

    // Cargar reporte para el año actual al iniciar (opcional)
    // document.addEventListener('DOMContentLoaded', function() {
    //    if (btnVerReporteAnual) btnVerReporteAnual.click();
    // });

</script>

<?php
require_once 'layout_footer.php'; // Incluir el nuevo footer
?>
