<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('GESTOR DE INVENTARIO') }}
            <link href="{{ asset('css/estilos.css') }}" rel="stylesheet">
            <!-- Incluye SweetAlert2 desde un CDN -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8 flex" style="height: 77vh;">
            <!-- Barra lateral -->
            <div class="w-1/5 bg-gray-800 text-white p-4 rounded-lg shadow-lg flex flex-col justify-between"
                style="height: 100%;">
                <div>
                    <h3 class="font-semibold text-lg mb-4">SELECCIONA UN CONTEO</h3>
                    <ul class="space-y-4">
                        <li>
                            <select id="select-conteo" class="w-full bg-gray-700 text-white py-2 px-4 rounded">
                                <option value="">Seleccionar Conteo</option>
                                @foreach ($conteos as $conteo)
                                    <option value="{{ $conteo->id }}">{{ $conteo->nombre }}</option>
                                @endforeach
                            </select>
                        </li>
                        <h3 class="font-semibold text-lg mb-4">SELECCIONA UN ANAQUEL</h3>
                        <ul class="space-y-4">

                            <!-- Switch Mostrar solo contados -->
                            <div class="w-full text-white py-0 px-0 rounded mb-4">
                                <label class="flex items-center cursor-pointer">
                                    <div class="relative">
                                        <!-- Input -->
                                        <input id="solo-contados" type="checkbox" class="sr-only">
                                        <!-- Line -->
                                        <div class="block bg-gray-700 w-14 h-8 rounded-full"></div>
                                        <!-- Dot -->
                                        <div
                                            class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition transform">
                                        </div>
                                    </div>
                                    <!-- Label -->
                                    <div class="ml-3 text-gray-300 font-medium">
                                        Mostrar solo contados
                                    </div>
                                </label>
                            </div>


                            <li>
                                <select id="select-ubicacion" class="w-full bg-gray-700 text-white py-2 px-4 rounded"
                                    disabled>
                                    <option value="">Seleccionar Ubicación</option>
                                </select>
                            </li>
                            <!-- Sección del select de Anaqueles -->
                            <li>
                                <select id="select-anaquel" class="w-full bg-gray-700 text-white py-2 px-4 rounded"
                                    disabled>
                                    <option value="">Seleccionar Anaquel</option>
                                    <option value="all">Todos los anaqueles</option> <!-- Nueva opción -->
                                </select>
                            </li>


                        </ul>
                </div>

                <div class="space-y-4">
                    <button id="btn-aceptar"
                        class="w-full bg-gray-600 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded">
                        Aceptar
                    </button>
                    <button id="btn-limpiar"
                        class="w-full bg-gray-600 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded">
                        Limpiar
                    </button>
                    <button id="btn-exportar"
                        class="w-full bg-green-600 hover:bg-green-500 text-white font-bold py-2 px-4 rounded">
                        EXPORTAR EXCEL
                    </button>
                    <button id="btn-marcar-segundo-conteo"
                        class="w-full bg-red-600 hover:bg-red-500 text-white font-bold py-2 px-4 rounded">
                        Marcar para Segundo Conteo
                    </button>
                    <button id="btn-resumen"
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded">
                        Resumen
                    </button>


                </div>


            </div>

            <!-- Contenido principal -->
            <div class="w-4/5 pl-4 text-gray-900 dark:text-gray-100 flex flex-col" style="height: 100%;">
                <!-- Barra de botones -->
                <div class="bg-gray-800 text-white p-4 rounded-lg mb-4 flex-shrink-0 flex justify-between">
                    <div class="flex space-x-2">
                        <button id="btn-todo" class="conteo-card disabled-button" disabled>
                            TODOS
                        </button>
                        <button id="btn-positivos" class="conteo-card disabled-button" disabled>
                            DIFERENCIA (+)
                        </button>
                        <button id="btn-negativos" class="conteo-card disabled-button" disabled>
                            DIFERENCIA (-)
                        </button>
                        <button id="btn-cuadrado" class="conteo-card disabled-button" disabled>
                            IGUALADO
                        </button>
                        <button id="btn-existencia-0" class="conteo-card disabled-button" disabled>
                            EXISTENCIA 0
                        </button>
                        <!-- Nuevo botón para mostrar/ocultar la columna de costos -->
                        <button id="btn-toggle-costo" class="conteo-card">
                            <img id="eye-icon" src="{{ asset('images/crossed-eye.png') }}" alt="Toggle Cost Column"
                                class="h-6 w-6">
                        </button>

                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            <p id="row-count" class="text-white text-sm">Total: 0</p>
                            <p id="showing-count" class="text-white text-sm">Mostrando: 0</p>
                        </div>
                    </div>
                </div>

                <!-- Tabla con scroll -->
                <div class="flex-grow overflow-auto">
                    <table class="min-w-full bg-gray-700 text-white rounded-lg shadow-lg">
                        <thead>
                            <tr class="bg-gray-600">
                                <th class="border w-2/12 px-4 py-1 cursor-pointer" data-column="ARTICULO"
                                    data-type="string">Nombre <span class="sort-arrow">▲</span></th>
                                <th class="border w-3/12 px-4 py-1 cursor-pointer" data-column="DESCRIPCION"
                                    data-type="string">Descripción <span class="sort-arrow">▲</span></th>
                                <th class="border w-1.5/12 px-4 py-1 cursor-pointer" data-column="CODIGO_BARRAS"
                                    data-type="string">Barcode <span class="sort-arrow">▲</span></th>
                                <th class="border w-1.5/12 px-4 py-1 cursor-pointer" data-column="NIVEL"
                                    data-type="string">N-1 <span class="sort-arrow">▲</span></th>
                                <th class="border w-1.5/12 px-4 py-1 cursor-pointer" data-column="NIVEL2"
                                    data-type="string">N-2 <span class="sort-arrow">▲</span></th>
                                <!-- Columna adicional para NIVEL2 -->
                                <th class="border w-1/12 px-4 py-1 cursor-pointer text-center" data-column="EXISTENCIA"
                                    data-type="number">E-S <span class="sort-arrow">▲</span></th>
                                <th class="border w-1/12 px-4 py-1 cursor-pointer text-center"
                                    data-column="EXISTENCIA_CONTEO" data-type="number">E-C <span
                                        class="sort-arrow">▲</span></th>
                                <th class="border w-1/12 px-4 py-1 cursor-pointer text-center"
                                    data-column="RECTIFICACION" data-type="number">E-C2 <span
                                        class="sort-arrow">▲</span></th>
                                <th class="border w-1/12 px-4 py-1 cursor-pointer text-center"
                                    data-column="DIFERENCIA" data-type="number">Dif <span class="sort-arrow">▲</span>
                                </th>
                                <th class="border w-1.5/12 px-4 py-1 cursor-pointer text-center" data-column="COSTO"
                                    data-type="number" style="display: none;">Costo <span class="sort-arrow">▲</span>
                                </th>
                            </tr>
                        </thead>


                        <tbody>
                            <!-- Aquí van a spawnear los productos -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Confirmación de Segundo Conteo -->
    <div id="modal-segundo-conteo"
        class="fixed inset-0 bg-gray-900 bg-opacity-90 flex items-center justify-center z-50 hidden">
        <div class="bg-gray-800 text-white rounded-lg shadow-xl w-4/5 lg:w-2/3">
            <div class="px-6 py-4 border-b border-gray-700 flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-100">Confirmar Segundo Conteo</h2>
                <button id="close-modal" class="text-gray-300 hover:text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="px-6 py-4 overflow-y-auto" style="max-height: 400px;">
                <table class="min-w-full bg-gray-800 text-gray-100">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 border-b border-gray-700 text-left leading-4">ID</th>
                            <th class="px-4 py-2 border-b border-gray-700 text-left leading-4">Descripción</th>
                            <th class="px-4 py-2 border-b border-gray-700 text-center leading-4">E-S</th>
                            <th class="px-4 py-2 border-b border-gray-700 text-center leading-4">E-C</th>
                            <th class="px-4 py-2 border-b border-gray-700 text-center leading-4">Dif</th>
                        </tr>
                    </thead>
                    <tbody id="modal-product-list">
                        <!-- Productos serán insertados aquí -->
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-700">
                <p class="font-semibold text-gray-300">Total de productos con diferencia: <span
                        id="modal-total-diferencias">0</span></p>
                <div class="mt-4 flex justify-end">
                    <button id="confirmar-segundo-conteo"
                        class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded-lg mr-2">Confirmar</button>
                    <button id="cancelar-segundo-conteo"
                        class="bg-gray-600 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Resumen -->
    <div id="modal-resumen"
        class="fixed inset-0 bg-gray-900 bg-opacity-90 flex items-center justify-center z-50 hidden">
        <div class="bg-gray-800 text-white rounded-lg shadow-xl w-2/5 lg:w-1/3">
            <div class="px-6 py-4 border-b border-gray-700 flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-100">Resumen de Inventario</h2>
                <button id="close-resumen-modal" class="text-gray-300 hover:text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="px-6 py-4 overflow-y-auto" style="max-height: 400px;">
                <!-- Contenido del resumen -->
                <table class="min-w-full bg-gray-800 text-gray-100">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 border-b border-gray-700 text-left leading-4">Resumen</th>
                            <th class="px-4 py-2 border-b border-gray-700 text-right leading-4">Códigos</th>
                            <th class="px-4 py-2 border-b border-gray-700 text-right leading-4">Resultados</th>
                            <th class="px-4 py-2 border-b border-gray-700 text-right leading-4">%</th>
                        </tr>
                    </thead>
                    <tbody id="modal-resumen-content">
                        <!-- Resumen será insertado aquí -->
                    </tbody>
                </table>
            </div>
            <!-- Botones de acción para el modal de resumen -->
            <div class="px-6 py-4 border-t border-gray-700 flex justify-end">
                <button id="btn-exportar-excel-resumen"
                    class="bg-green-600 hover:bg-green-500 text-white font-bold py-2 px-4 rounded-lg mr-2">Exportar
                    Excel</button>
                <button id="btn-exportar-pdf-resumen"
                    class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded-lg mr-2">Exportar
                    PDF</button>
                <button id="btn-cerrar-resumen"
                    class="bg-gray-600 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg">Cerrar</button>
            </div>
        </div>
    </div>


</x-app-layout>

<style>
    #eye-icon {
        width: 24px;
        height: 24px;
        filter: invert(100%);
        /* Esta línea es opcional si la imagen es originalmente negra */
    }

    .disabled-button {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .conteo-card {
        background-color: #141b25;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        height: 50px;
        width: 150px;
        transition: transform 0.3s, box-shadow 0.3s, background-color 0.3s;
        cursor: pointer;
    }

    .conteo-card:hover:not(.disabled-button),
    .conteo-card.selected {
        transform: translateY(-5px);
        background-color: #071122;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        border: 1px solid #3b82f6;
    }

    .conteo-card.selected {
        border: 1px solid #3b82f6;
        background-color: #071122;
    }

    .sort-arrow {
        margin-left: 5px;
        display: inline-block;
    }

    th {
        position: relative;
    }

    th:hover .sort-arrow {
        display: inline-block;
    }

    th .sort-arrow {
        display: none;
    }

    .truncate {
        max-width: 150px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Estilos para el switch activado */
    input:checked~.dot {
        transform: translateX(100%);
        background-color: #ffffff;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* Estilos para el switch desactivado */
    .dot {
        transition: transform 0.3s ease-in-out, background-color 0.3s ease-in-out;
        background-color: #ffffff52;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Variables de elementos de la interfaz
        const selectConteo = document.getElementById('select-conteo');
        const selectUbicacion = document.getElementById('select-ubicacion');
        const selectAnaquel = document.getElementById('select-anaquel');
        const btnAceptar = document.getElementById('btn-aceptar');
        const btnLimpiar = document.getElementById('btn-limpiar');
        const btnTodo = document.getElementById('btn-todo');
        const btnPositivos = document.getElementById('btn-positivos');
        const btnNegativos = document.getElementById('btn-negativos');
        const btnCuadrado = document.getElementById('btn-cuadrado');
        const btnExistencia0 = document.getElementById('btn-existencia-0');
        const btnMarcarSegundoConteo = document.getElementById('btn-marcar-segundo-conteo');
        const btnResumen = document.getElementById('btn-resumen');
        const tbody = document.querySelector('table tbody');
        const rowCount = document.getElementById('row-count');
        const showingCount = document.getElementById('showing-count');
        const soloContados = document.getElementById('solo-contados');
        const buttonsToEnable = [btnTodo, btnPositivos, btnNegativos, btnCuadrado, btnExistencia0];

        const modalSegundoConteo = document.getElementById('modal-segundo-conteo');
        const closeModal = document.getElementById('close-modal');
        const confirmarSegundoConteo = document.getElementById('confirmar-segundo-conteo');
        const cancelarSegundoConteo = document.getElementById('cancelar-segundo-conteo');
        const modalProductList = document.getElementById('modal-product-list');
        const modalTotalDiferencias = document.getElementById('modal-total-diferencias');

        const modalResumen = document.getElementById('modal-resumen');
        const closeResumenModal = document.getElementById('close-resumen-modal');
        const modalResumenContent = document.getElementById('modal-resumen-content');
        const btnExportarExcelResumen = document.getElementById('btn-exportar-excel-resumen');
        const btnExportarPdfResumen = document.getElementById('btn-exportar-pdf-resumen');
        const btnCerrarResumen = document.getElementById('btn-cerrar-resumen');

        const btnExportar = document.getElementById('btn-exportar');

        let lastSortedColumn = null;
        let isAscending = true;

        document.querySelectorAll('th[data-column]').forEach(header => {
            header.addEventListener('click', () => {
                const column = header.getAttribute('data-column');
                const type = header.getAttribute('data-type');

                // Alternar dirección de orden y recordar columna ordenada
                if (lastSortedColumn === column) {
                    isAscending = !isAscending;
                } else {
                    isAscending = true;
                    lastSortedColumn = column;
                }

                // Aplicar ordenación
                sortTable(column, type, isAscending);
            });
        });



        function sortTable(column, type, isAscending) {
            const rows = Array.from(tbody.querySelectorAll('tr'));

            rows.sort((a, b) => {
                const cellA = a.querySelector(`[data-column="${column}"]`).textContent.trim();
                const cellB = b.querySelector(`[data-column="${column}"]`).textContent.trim();

                if (type === 'number') {
                    return isAscending ? cellA - cellB : cellB - cellA;
                } else {
                    return isAscending ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
                }
            });

            // Volver a agregar las filas ordenadas en el tbody
            rows.forEach(row => tbody.appendChild(row));
        }





        // Evento para aceptar y cargar productos
        btnAceptar.addEventListener('click', () => {
            const conteoId = selectConteo.value;
            const ubicacionId = selectUbicacion.value;
            const anaquel = selectAnaquel.value;

            if (conteoId && ubicacionId) {
                Swal.fire({
                    title: 'Consultando productos...',
                    text: 'Por favor, espera mientras se consultan los productos.',
                    icon: 'info',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    background: '#2d3748',
                    color: '#cbd5e0'
                });

                fetch(`/gestor/productos`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: JSON.stringify({
                            conteo_id: conteoId,
                            ubicacion_id: ubicacionId,
                            anaquel: anaquel === 'all' ? null : anaquel
                        })
                    })
                    .then(response => {
                        console.log("Estado de la respuesta:", response.status);
                        if (!response.ok) {
                            throw new Error("Error en la respuesta del servidor");
                        }
                        return response.json();
                    })
                    .then(data => {
                        Swal.close();
                        console.log("Datos recibidos:", data);

                        if (!Array.isArray(data)) {
                            throw new Error("Formato de datos incorrecto; se esperaba un array.");
                        }

                        // Limpiar el encabezado y las filas de la tabla
                        tbody.innerHTML = '';
                        data.forEach((producto, index) => {
                            try {
                                const diferencia = producto.SEGUNDO_CONTEO == 2 ?
                                    producto.CANTIDAD2 - producto.EXISTENCIA :
                                    producto.EXISTENCIA_CONTEO - producto.EXISTENCIA;
                                let diferenciaClass = '';

                                if (producto.es_nuevo) {
                                    diferenciaClass = 'bg-blue-600';
                                } else if (diferencia < 0) {
                                    diferenciaClass = 'bg-red-600';
                                } else if (diferencia > 0) {
                                    diferenciaClass = 'bg-yellow-600';
                                } else {
                                    diferenciaClass = 'bg-green-600';
                                }

                                const tr = document.createElement('tr');
                                tr.innerHTML = `
                                <td class="border px-2 py-1" data-column="ARTICULO">${producto.ARTICULO}</td>
                                <td class="border px-2 py-1 truncate" data-column="DESCRIPCION">${producto.DESCRIPCION}</td>
                                <td class="border px-2 py-1" data-column="CODIGO_BARRAS">${producto.CODIGO_BARRAS || 'No encontrado'}</td>
                                <td class="border px-2 py-1 text-center" data-column="NIVEL">${producto.NIVEL?.trim() || ''}</td>
                                <td class="border px-2 py-1 text-center" data-column="NIVEL2">${anaquel === 'all' ? (producto.NIVEL2 || '') : ''}</td> <!-- Muestra NIVEL2 solo si se seleccionan todos los anaqueles -->
                                <td class="border px-2 py-1 text-center" data-column="EXISTENCIA">${producto.EXISTENCIA}</td>
                                <td class="border px-2 py-1 text-center" data-column="EXISTENCIA_CONTEO">${producto.EXISTENCIA_CONTEO || ''}</td>
                                <td class="border px-2 py-1 text-center" data-column="RECTIFICACION">${producto.SEGUNDO_CONTEO == 2 ? producto.CANTIDAD2 : ''}</td>
                                <td class="border px-2 py-1 ${diferenciaClass} text-center" data-column="DIFERENCIA">${diferencia}</td>
                                <td class="border px-2 py-1 text-left" data-column="COSTO" style="display: none;">$${parseFloat(producto.COSTO_CAPAS).toFixed(3)}</td>
                            `;
                                tbody.appendChild(tr);
                            } catch (error) {
                                console.error("Error al procesar producto en índice:",
                                    index, producto, error);
                            }
                        });


                        rowCount.textContent = `Total: ${data.length}`;
                        enableButtons();
                        selectButton(btnTodo);
                        filterTableRows();
                        updateShowingCount();

                        // Aplicar la última ordenación, si existe
                        if (lastSortedColumn) {
                            const type = document.querySelector(
                                `th[data-column="${lastSortedColumn}"]`).getAttribute(
                                'data-type');
                            sortTable(lastSortedColumn, type, isAscending);
                        }

                        Swal.fire({
                            title: 'Consulta realizada',
                            text: `Se han cargado ${data.length} productos.`,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false,
                            background: '#2d3748',
                            color: '#cbd5e0'
                        });
                    })
                    .catch(error => {
                        console.error("Error en la carga de productos:", error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Hubo un problema al consultar los productos. Por favor, revisa la consola para más detalles.',
                            icon: 'error',
                            timer: 1500,
                            showConfirmButton: false,
                            background: '#2d3748',
                            color: '#cbd5e0'
                        });
                    });
            } else {
                Swal.fire({
                    title: 'Campos incompletos',
                    text: 'Por favor seleccione una ubicación y un anaquel.',
                    icon: 'warning',
                    timer: 1500,
                    showConfirmButton: false,
                    background: '#2d3748',
                    color: '#cbd5e0'
                });
            }
        });





        // Función para habilitar botones
        function enableButtons() {
            buttonsToEnable.forEach(button => {
                button.disabled = false;
                button.classList.remove('disabled-button');
            });
        }

        // Función para seleccionar un botón
        function selectButton(button) {
            buttonsToEnable.forEach(btn => btn.classList.remove('selected'));
            button.classList.add('selected');
        }

        // Función para obtener ubicaciones basadas en el conteo seleccionado
        function fetchUbicaciones(conteoId, soloContadosValue) {
            fetch(`/gestor/ubicaciones/${conteoId}?solo_contados=${soloContadosValue}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert('Error fetching ubicaciones: ' + data.error);
                        return;
                    }
                    selectUbicacion.innerHTML = '<option value="">Seleccionar Ubicación</option>';
                    data.forEach(ubicacion => {
                        const option = document.createElement('option');
                        option.value = ubicacion.id;
                        option.textContent = ubicacion.nombre;
                        selectUbicacion.appendChild(option);
                    });
                    selectUbicacion.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }


        // Evento para cambio de selección de conteo
        selectConteo.addEventListener('change', () => {
            const conteoId = selectConteo.value;
            const soloContadosValue = soloContados.checked ? 'true' : 'false';
            if (conteoId) {
                fetchUbicaciones(conteoId, soloContadosValue);
            } else {
                selectUbicacion.disabled = true;
                selectUbicacion.innerHTML = '<option value="">Seleccionar Ubicación</option>';
                selectAnaquel.disabled = true;
                selectAnaquel.innerHTML = '<option value="">Seleccionar Anaquel</option>';

                // Agrega la opción "Todos los anaqueles" en caso de que se esté limpiando
                const optionTodos = document.createElement('option');
                optionTodos.value = 'all';
                optionTodos.textContent = 'Todos los anaqueles';
                selectAnaquel.appendChild(optionTodos);
            }
        });


        // Evento para cambio en el switch "Mostrar solo contados"
        soloContados.addEventListener('change', () => {
            const conteoId = selectConteo.value;
            const soloContadosValue = soloContados.checked ? 'true' : 'false';
            if (conteoId) {
                fetchUbicaciones(conteoId, soloContadosValue);
            }
        });

        // Evento para seleccionar una ubicación
        selectUbicacion.addEventListener('change', () => {
            const ubicacionId = selectUbicacion.value;
            const conteoId = selectConteo.value; // Obtener el conteo_id
            const soloContadosValue = soloContados.checked ? 'true' : 'false';

            if (ubicacionId) {
                fetch(
                        `/gestor/anaqueles/${ubicacionId}?solo_contados=${soloContadosValue}&conteo_id=${conteoId}`) // Enviar conteo_id
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert('Error fetching anaqueles: ' + data.error);
                            return;
                        }
                        if (!Array.isArray(data)) {
                            alert('Unexpected response format');
                            return;
                        }
                        // Limpia el select
                        selectAnaquel.innerHTML = '<option value="">Seleccionar Anaquel</option>';

                        // Agrega la opción "Todos los anaqueles"
                        const optionTodos = document.createElement('option');
                        optionTodos.value = 'all';
                        optionTodos.textContent = 'Todos los anaqueles';
                        selectAnaquel.appendChild(optionTodos);

                        // Agrega los anaqueles obtenidos dinámicamente
                        data.forEach(anaquel => {
                            const option = document.createElement('option');
                            option.value = anaquel;
                            option.textContent = anaquel;
                            selectAnaquel.appendChild(option);
                        });
                        selectAnaquel.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            } else {
                selectAnaquel.disabled = true;
                selectAnaquel.innerHTML = '<option value="">Seleccionar Anaquel</option>';
            }
        });



        // Evento para limpiar selección y productos
        btnLimpiar.addEventListener('click', () => {
            selectConteo.value = '';
            selectUbicacion.innerHTML = '<option value="">Seleccionar Ubicación</option>';
            selectUbicacion.disabled = true;

            selectAnaquel.innerHTML = '<option value="">Seleccionar Anaquel</option>';
            selectAnaquel.disabled = true;

            // Agrega la opción "Todos los anaqueles"
            const optionTodos = document.createElement('option');
            optionTodos.value = 'all';
            optionTodos.textContent = 'Todos los anaqueles';
            selectAnaquel.appendChild(optionTodos);

            tbody.innerHTML = '';
            rowCount.textContent = 'Total: 0';
            showingCount.textContent = 'Mostrando: 0';
            buttonsToEnable.forEach(button => {
                button.disabled = true;
                button.classList.add('disabled-button');
                button.classList.remove('selected');
            });
        });


        // Función para filtrar la tabla según la opción seleccionada
        function filtrarTabla(filtro) {
            const rows = tbody.querySelectorAll('tr');
            rows.forEach(row => {
                const diferenciaCell = row.querySelector('[data-column="DIFERENCIA"]');
                const existenciaCell = row.querySelector('[data-column="EXISTENCIA"]');
                const existenciaConteoCell = row.querySelector('[data-column="EXISTENCIA_CONTEO"]');
                const esNuevo = diferenciaCell.classList.contains('bg-blue-600');
                const existenciaCero = (parseInt(existenciaCell.textContent) === 0) && (
                    existenciaConteoCell.textContent.trim() === '');

                let mostrarFila = true;
                const diferencia = parseInt(diferenciaCell.textContent);

                switch (filtro) {
                    case 'btn-positivos':
                        if (diferencia <= 0 && !esNuevo) mostrarFila = false;
                        break;
                    case 'btn-negativos':
                        if (diferencia >= 0 || esNuevo) mostrarFila = false;
                        break;
                    case 'btn-cuadrado':
                        if (diferencia !== 0 || esNuevo || parseInt(existenciaCell.textContent) === 0)
                            mostrarFila = false;
                        break;
                    case 'btn-existencia-0':
                        if (!existenciaCero) mostrarFila = false;
                        break;
                    default:
                        mostrarFila = true; // Mostrar todos
                }

                row.style.display = mostrarFila ? '' : 'none';
            });
        }


        // Función para aplicar filtro en las filas de la tabla
        function filterTableRows() {
            const selectedButton = document.querySelector('.conteo-card.selected');
            const filtro = selectedButton ? selectedButton.getAttribute('id') : 'btn-todo';
            filtrarTabla(filtro);
            updateShowingCount();
        }

        // Función para actualizar el conteo de productos mostrados
        function updateShowingCount() {
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const visibleRows = rows.filter(row => row.style.display !== 'none');
            showingCount.textContent = `Mostrando: ${visibleRows.length}`;
        }

        // Eventos para cambiar entre diferentes filtros de la tabla
        btnTodo.addEventListener('click', () => {
            selectButton(btnTodo);
            filtrarTabla('btn-todo');
            updateShowingCount();
        });
        btnPositivos.addEventListener('click', () => {
            selectButton(btnPositivos);
            filtrarTabla('btn-positivos');
            updateShowingCount();
        });
        btnNegativos.addEventListener('click', () => {
            selectButton(btnNegativos);
            filtrarTabla('btn-negativos');
            updateShowingCount();
        });
        btnCuadrado.addEventListener('click', () => {
            selectButton(btnCuadrado);
            filtrarTabla('btn-cuadrado');
            updateShowingCount();
        });
        btnExistencia0.addEventListener('click', () => {
            selectButton(btnExistencia0);
            filtrarTabla('btn-existencia-0');
            updateShowingCount();
        });




        // Función para habilitar el botón de exportación
        function enableExportButton() {
            btnExportar.disabled = false;
            btnExportar.classList.remove('disabled-button');
        }

        // Función para recoger datos de la tabla
        function collectTableData() {
            const rows = Array.from(tbody.querySelectorAll('tr'));
            return rows
                .filter(row => row.style.display !== 'none') // Solo filas visibles
                .map(row => {
                    const cells = Array.from(row.querySelectorAll('td'));
                    return {
                        ARTICULO: cells[0]?.textContent.trim() || '',
                        DESCRIPCION: cells[1]?.textContent.trim() || '',
                        CODIGO_BARRAS: cells[2]?.textContent.trim() || '',
                        NIVEL: cells[3]?.textContent.trim() || '',
                        NIVEL2: cells[4]?.textContent.trim() || '',
                        EXISTENCIA: cells[5]?.textContent.trim() || '',
                        EXISTENCIA_CONTEO: cells[6]?.textContent.trim() || '',
                        RECTIFICACION: cells[7]?.textContent.trim() || '',
                        DIFERENCIA: cells[8]?.textContent.trim() || '',
                        PRECIO: cells[9]?.textContent.trim() || ''
                    };
                });
        }




        btnExportar.addEventListener('click', () => {
            const productos = collectTableData(); // Captura exactamente lo que está en la tabla
            const ubicacionSeleccionadaNombre = document.getElementById('select-ubicacion').options[
                document.getElementById('select-ubicacion').selectedIndex].text;
            const anaquelSeleccionado = document.getElementById('select-anaquel').value;

            if (productos.length === 0) {
                Swal.fire({
                    title: 'No hay productos cargados',
                    text: 'Por favor, cargue productos antes de exportar.',
                    icon: 'warning',
                    timer: 1500,
                    showConfirmButton: false,
                    background: '#2d3748',
                    color: '#cbd5e0'
                });
                return;
            }

            fetch('/exportar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content')
                    },
                    body: JSON.stringify({
                        productos: productos,
                        ubicacion_seleccionada_nombre: ubicacionSeleccionadaNombre,
                        anaquel_seleccionado: anaquelSeleccionado
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            console.error("Error al exportar:", errorData);
                            throw new Error("Error al exportar: " + errorData.error);
                        });
                    }
                    return response.blob();
                })
                .then(blob => {
                    const url = window.URL.createObjectURL(new Blob([blob]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'productos_exportados.zip');
                    document.body.appendChild(link);
                    link.click();
                    link.parentNode.removeChild(link);
                })
                .catch(error => {
                    console.error('Error exporting data:', error);
                });
        });


        // Evento para marcar productos para segundo conteo
        btnMarcarSegundoConteo.addEventListener('click', () => {
            const anaquel = document.getElementById('select-anaquel').value;

            if (anaquel === 'all') {
                Swal.fire({
                    title: 'No permitido',
                    text: 'No se puede enviar a segundo conteo cuando se selecciona "Todos los anaqueles". Por favor, selecciona un anaquel específico.',
                    icon: 'warning',
                    timer: 2000,
                    showConfirmButton: false,
                    background: '#2d3748',
                    color: '#cbd5e0'
                });
                return;
            }

            // Continuar con el proceso normal si no es "Todos los anaqueles"
            const conteoId = document.getElementById('select-conteo').value;
            const ubicacionId = document.getElementById('select-ubicacion').value;

            const productos = Array.from(document.querySelectorAll('tbody tr')).map(row => {
                const articuloCell = row.querySelector('[data-column="ARTICULO"]');
                const descripcionCell = row.querySelector('[data-column="DESCRIPCION"]');
                const existenciaCell = row.querySelector('[data-column="EXISTENCIA"]');
                const existenciaConteoCell = row.querySelector(
                    '[data-column="EXISTENCIA_CONTEO"]');
                const diferenciaCell = row.querySelector('[data-column="DIFERENCIA"]');

                // Verifica si todas las celdas existen antes de acceder a sus propiedades
                if (!articuloCell || !descripcionCell || !existenciaCell || !
                    existenciaConteoCell || !diferenciaCell) {
                    return null; // Si alguna celda es null, devuelve null para este elemento del array
                }

                return {
                    id: articuloCell.textContent.trim(),
                    descripcion: descripcionCell.textContent.trim(),
                    existencia: parseInt(existenciaCell.textContent.trim()),
                    existencia_conteo: parseInt(existenciaConteoCell.textContent.trim()),
                    diferencia: parseInt(diferenciaCell.textContent.trim())
                };
            }).filter(producto => producto !== null && !isNaN(producto.diferencia) && producto
                .diferencia !== 0); // Filtra los elementos null y los que no tienen diferencia válida

            if (productos.length > 0) {
                let productListHTML = productos.map(producto => `
                    <tr>
                        <td class="px-4 py-2 border-b">${producto.id}</td>
                        <td class="px-4 py-2 border-b">${producto.descripcion}</td>
                        <td class="px-4 py-2 border-b text-center">${producto.existencia}</td>
                        <td class="px-4 py-2 border-b text-center">${producto.existencia_conteo}</td>
                        <td class="px-4 py-2 border-b text-center">${producto.diferencia}</td>
                    </tr>
                `).join('');

                modalProductList.innerHTML = productListHTML;
                modalTotalDiferencias.textContent = productos.length;

                modalSegundoConteo.classList.remove('hidden');
            } else {
                Swal.fire({
                    title: 'No hay productos seleccionados',
                    text: 'No se encontraron productos con diferencias.',
                    icon: 'warning',
                    timer: 1500,
                    showConfirmButton: false,
                    background: '#2d3748',
                    color: '#cbd5e0'
                });
            }
        });

        closeModal.addEventListener('click', closeModalListener);
        cancelarSegundoConteo.addEventListener('click', closeModalListener);

        confirmarSegundoConteo.addEventListener('click', confirmarSegundoConteoListener);

        function closeModalListener() {
            modalSegundoConteo.classList.add('hidden');
        }

        function confirmarSegundoConteoListener() {
            const conteoId = document.getElementById('select-conteo').value;
            const ubicacionId = document.getElementById('select-ubicacion').value;
            const anaquel = document.getElementById('select-anaquel').value;

            const productos = Array.from(document.querySelectorAll('tbody tr')).map(row => {
                const articuloCell = row.querySelector('[data-column="ARTICULO"]');
                const descripcionCell = row.querySelector('[data-column="DESCRIPCION"]');
                const existenciaCell = row.querySelector('[data-column="EXISTENCIA"]');
                const existenciaConteoCell = row.querySelector('[data-column="EXISTENCIA_CONTEO"]');
                const diferenciaCell = row.querySelector('[data-column="DIFERENCIA"]');

                // Verifica si todas las celdas existen antes de acceder a sus propiedades
                if (!articuloCell || !descripcionCell || !existenciaCell || !existenciaConteoCell || !
                    diferenciaCell) {
                    return null; // Si alguna celda es null, devuelve null para este elemento del array
                }

                return {
                    id: articuloCell.textContent.trim(),
                    descripcion: descripcionCell.textContent.trim(),
                    existencia: parseInt(existenciaCell.textContent.trim()),
                    existencia_conteo: parseInt(existenciaConteoCell.textContent.trim()),
                    diferencia: parseInt(diferenciaCell.textContent.trim())
                };
            }).filter(producto => producto !== null && !isNaN(producto.diferencia) && producto
                .diferencia !== 0); // Filtra los elementos null y los que no tienen diferencia válida

            fetch('/gestor/actualizar-segundo-conteo', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        productos,
                        conteo_id: conteoId,
                        ubicacion_id: ubicacionId,
                        anaquel
                    })
                })
                .then(response => response.json())
                .then(updateData => {
                    modalSegundoConteo.classList.add('hidden');
                    if (updateData.success) {
                        Swal.fire({
                            title: 'Actualizado!',
                            text: 'Los productos seleccionados han sido marcados para segundo conteo.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false,
                            background: '#2d3748',
                            color: '#cbd5e0'
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: updateData.error || 'Hubo un problema al marcar los productos.',
                            icon: 'error',
                            timer: 1500,
                            showConfirmButton: false,
                            background: '#2d3748',
                            color: '#cbd5e0'
                        });
                    }
                })
                .catch(error => {
                    modalSegundoConteo.classList.add('hidden');
                    Swal.fire({
                        title: 'Error!',
                        text: 'Hubo un problema al procesar la solicitud.',
                        icon: 'error',
                        timer: 1500,
                        showConfirmButton: false,
                        background: '#2d3748',
                        color: '#cbd5e0'
                    });
                    console.error('Error:', error);
                });
        }

        // Lógica del botón Resumen
        btnResumen.addEventListener('click', () => {
            const rows = Array.from(tbody.querySelectorAll('tr'));

            if (rows.length === 0) {
                Swal.fire({
                    title: 'No hay productos cargados',
                    text: 'Por favor, cargue productos antes de ver el resumen.',
                    icon: 'warning',
                    timer: 1500,
                    showConfirmButton: false,
                    background: '#2d3748',
                    color: '#cbd5e0'
                });
                return;
            }

            let totalProductos = 0;
            let codigosSobrante = 0;
            let codigosFaltante = 0;
            let codigosSinDiferencia = 0;
            let totalSobran = 0;
            let totalFaltan = 0;
            let totalSinDiferencia = 0;
            let totalCostos = 0; // Variable para almacenar la suma total de los costos

            rows.forEach(row => {
                const diferenciaCell = row.querySelector('[data-column="DIFERENCIA"]');
                const diferencia = parseFloat(diferenciaCell.textContent.trim());
                const existencia = parseFloat(row.querySelector('[data-column="EXISTENCIA"]')
                    .textContent.trim());
                const existenciaConteo = row.querySelector('[data-column="EXISTENCIA_CONTEO"]')
                    .textContent.trim();
                const costoPorUnidad = parseFloat(row.querySelector('[data-column="COSTO"]')
                    .textContent.replace('$', '').trim());

                // Verifica si el producto está pintado de azul (bg-blue-600)
                const esNuevo = diferenciaCell.classList.contains('bg-blue-600');

                // Ignorar productos con existencia en sistema 0 o pintados de azul
                if ((existencia === 0 && existenciaConteo === '') || esNuevo) {
                    return;
                }

                totalProductos++;

                const costoTotalProducto = existencia * costoPorUnidad;
                totalCostos += costoTotalProducto;

                if (diferencia > 0) {
                    codigosSobrante++;
                    totalSobran += diferencia * costoPorUnidad;
                } else if (diferencia < 0) {
                    codigosFaltante++;
                    totalFaltan += Math.abs(diferencia) * costoPorUnidad;
                } else {
                    codigosSinDiferencia++;
                    totalSinDiferencia += existencia * costoPorUnidad;
                }
            });

            const diferenciaReal = totalSobran - totalFaltan;
            const codigosConDiferencia = totalProductos - codigosSinDiferencia;

            // Si la diferencia es positiva, se suma; si es negativa, se resta
            const diferenciaAbsoluta = diferenciaReal >= 0 ?
                totalCostos + Math.abs(diferenciaReal) :
                totalCostos - Math.abs(diferenciaReal);

            modalResumenContent.innerHTML = `
                <tr>
                    <td class="px-4 py-2 border-b text-left">Productos inventariado total</td>
                    <td class="px-4 py-2 border-b text-right">${totalProductos}</td>
                    <td class="px-4 py-2 border-b text-right">$${totalCostos.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    <td class="px-4 py-2 border-b text-right">100%</td>
                </tr>
                <tr>
                    <td class="px-4 py-2 border-b text-left">Códigos c/ sobrante</td>
                    <td class="px-4 py-2 border-b text-right">${codigosSobrante}</td>
                    <td class="px-4 py-2 border-b text-right">$${totalSobran.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    <td class="px-4 py-2 border-b text-right">${((codigosSobrante / totalProductos) * 100).toFixed(2)}%</td>
                </tr>
                <tr>
                    <td class="px-4 py-2 border-b text-left">Códigos c/ faltante</td>
                    <td class="px-4 py-2 border-b text-right">${codigosFaltante}</td>
                    <td class="px-4 py-2 border-b text-right">$${totalFaltan.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    <td class="px-4 py-2 border-b text-right">${((codigosFaltante / totalProductos) * 100).toFixed(2)}%</td>
                </tr>
                <tr>
                    <td class="px-4 py-2 border-b text-left">Diferencia Real</td>
                    <td class="px-4 py-2 border-b text-right"></td>
                    <td class="px-4 py-2 border-b text-right">$${diferenciaReal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    <td class="px-4 py-2 border-b text-right"></td>
                </tr>
                <tr>
                    <td class="px-4 py-2 border-b text-left">Códigos sin diferencia</td>
                    <td class="px-4 py-2 border-b text-right">${codigosSinDiferencia}</td>
                    <td class="px-4 py-2 border-b text-right">$${totalSinDiferencia.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    <td class="px-4 py-2 border-b text-right">${((codigosSinDiferencia / totalProductos) * 100).toFixed(2)}%</td>
                </tr>
                <tr>
                    <td class="px-4 py-2 border-b text-left">Códigos con diferencia</td>
                    <td class="px-4 py-2 border-b text-right">${codigosConDiferencia}</td>
                    <td class="px-4 py-2 border-b text-right">$${(totalSobran + totalFaltan).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    <td class="px-4 py-2 border-b text-right">${((codigosConDiferencia / totalProductos) * 100).toFixed(2)}%</td>
                </tr>
                <tr>
                    <td class="px-4 py-2 border-b text-left">Diferencia absoluta</td>
                    <td class="px-4 py-2 border-b text-right"></td>
                    <td class="px-4 py-2 border-b text-right">$${diferenciaAbsoluta.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                    <td class="px-4 py-2 border-b text-right"></td>
                </tr>
            `;

            modalResumen.classList.remove('hidden');
        });

        // Lógica para el botón de mostrar/ocultar columna de costos
        const btnToggleCosto = document.getElementById('btn-toggle-costo');
        const eyeIcon = document.getElementById('eye-icon');
        let isCostVisible = false; // Controla si la columna de costo está visible o no

        // Oculta la columna de costos por defecto
        toggleCostColumnVisibility(false);

        // Lógica para mostrar/ocultar la columna de costos
        btnToggleCosto.addEventListener('click', () => {
            isCostVisible = !isCostVisible;
            toggleCostColumnVisibility(isCostVisible);

            // Cambia el ícono del ojo según el estado
            eyeIcon.src = isCostVisible ?
                "{{ asset('images/eye.png') }}" :
                "{{ asset('images/crossed-eye.png') }}";
        });

        function toggleCostColumnVisibility(visible) {
            // Encuentra el índice de la columna de costo basado en el encabezado de la tabla
            const headers = document.querySelectorAll('table thead th');
            let costoColumnIndex = -1;
            headers.forEach((header, index) => {
                if (header.getAttribute('data-column') === 'COSTO') {
                    costoColumnIndex = index;
                }
            });

            // Asegúrate de que se encontró el índice de la columna de costo
            if (costoColumnIndex !== -1) {
                const tableRows = document.querySelectorAll('table tr');

                tableRows.forEach(row => {
                    const cells = row.querySelectorAll('th, td');
                    if (cells[costoColumnIndex]) {
                        cells[costoColumnIndex].style.display = visible ? '' : 'none';
                    }
                });
            }
        }


        // Eventos para cerrar el modal de resumen
        btnCerrarResumen.addEventListener('click', () => {
            modalResumen.classList.add('hidden');
        });

        closeResumenModal.addEventListener('click', () => {
            modalResumen.classList.add('hidden');
        });

        // Eventos para exportar resumen a Excel y PDF
        btnExportarExcelResumen.addEventListener('click', () => {
            exportarResumen('excel');
        });

        btnExportarPdfResumen.addEventListener('click', () => {
            exportarResumen('pdf');
        });

        function exportarResumen(tipo) {
            const resumen = [];

            // Recopilar datos del resumen del modal
            const rows = modalResumenContent.querySelectorAll('tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length > 0) {
                    resumen.push({
                        resumen: cells[0].textContent.trim(),
                        codigos: cells[1].textContent.trim(),
                        resultados: cells[2].textContent.trim(),
                        porcentaje: cells[3].textContent.trim(),
                    });
                }
            });

            fetch('/gestor/exportar-resumen', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        resumen: resumen,
                        tipo: tipo,
                        ubicacion: document.getElementById('select-ubicacion').value,
                        anaquel: document.getElementById('select-anaquel').value
                    })
                })
                .then(response => response.blob())
                .then(blob => {
                    const url = window.URL.createObjectURL(new Blob([blob]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', tipo === 'excel' ? 'resumen_inventario.xlsx' :
                        'resumen_inventario.pdf');
                    document.body.appendChild(link);
                    link.click();
                    link.parentNode.removeChild(link);
                })
                .catch(error => {
                    console.error('Error al exportar el resumen:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo exportar el resumen.',
                        icon: 'error',
                        timer: 1500,
                        showConfirmButton: false,
                        background: '#2d3748',
                        color: '#cbd5e0'
                    });
                });
        }

    });
</script>
