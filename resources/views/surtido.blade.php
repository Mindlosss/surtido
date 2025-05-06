<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">

            <!-- Título principal -->
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-2 sm:mb-0">
                {{ __('Pedidos') }}
            </h2>
        
            <!-- Contenedor de filtros y acciones -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4">
        
                <!-- Form de Filtro por ubicación -->
                <form method="GET" action="{{ route('surtido') }}" class="w-full sm:w-auto mb-2 sm:mb-0">
                    <label for="ubicacion" class="sr-only">Ubicación</label>
                    <select name="ubicacion" id="ubicacion"
                            class="border rounded py-1 px-4 dark:bg-gray-700 dark:text-gray-200 w-full sm:w-auto"
                            onchange="this.form.submit()">
                        <option value="almacen" {{ $ubicacion === 'almacen' ? 'selected' : '' }}>Almacén</option>
                        <option value="bodega" {{ $ubicacion === 'bodega' ? 'selected' : '' }}>Bodega</option>
                        <option value="matriz" {{ $ubicacion === 'matriz' ? 'selected' : '' }}>Matriz (Exhibición)</option>
                        <option value="milwaukee" {{ $ubicacion === 'milwaukee' ? 'selected' : '' }}>Milwaukee (Exhibición)</option>
                        <option value="makita" {{ $ubicacion === 'makita' ? 'selected' : '' }}>Makita (Exhibición)</option>
                        <option value="sucursalm" {{ $ubicacion === 'sucursalm' ? 'selected' : '' }}>Sucursal M (Exhibición)</option>
                        <option value="california" {{ $ubicacion === 'california' ? 'selected' : '' }}>California (Exhibición)</option>
                    </select>
                </form>
        
                <!-- Botón de búsqueda con mismo estilo que "Mostrar completados" -->
                <button id="open-search-modal"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-4 py-1 rounded-lg shadow-md border border-gray-500 flex items-center space-x-2 transition-transform transform duration-300 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M11 4a7 7 0 014.95 11.95l4.24 4.24a1
                                 1 0 01-1.42 1.42l-4.24-4.24A7 7
                                 0 1111 4z"/>
                    </svg>
                    <span>Buscar pedido</span>
                </button>
        
                <!-- Botón para mostrar pedidos completados -->
                <button id="toggle-completed-orders"
                        class="hidden sm:flex bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-1 rounded-lg shadow-md border border-blue-500 items-center space-x-2 transition-transform transform duration-300 ease-in-out">
                    <span>Mostrar completados</span>
                </button>
        
            </div>
        </div>
        
    </x-slot>

    <!-- Contenido principal -->
    <div class="py-7">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg flex">
                
                <!-- Contenedor principal de pedidos por surtir -->
                <div id="pedidos-container" class="w-full p-3 pr-0 text-gray-900 dark:text-gray-100 relative">
                    <div class="scroll-container" id="pedidos-list">
                        <!-- Aquí se renderizan los pedidos sin completar (sin colapsar) con JS -->
                    </div>
                </div>

                <!-- Contenedor de pedidos completados -->
                <div class="w-1/3 p-3 text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-700 rounded-lg shadow-lg ml-3 hidden"
                     id="panel-completados">
                    <h3 class="text-lg font-semibold mb-4 text-center">Progreso de pedidos</h3>
                    <div class="scroll-container2" id="pedidos-completados-list">
                        <!-- Aquí se renderizan los pedidos completados (colapsable), mezclando facturas y notas -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para la búsqueda -->
    <div id="search-modal" class="fixed z-50 inset-0 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 relative z-10 w-full sm:max-w-md md:max-w-lg lg:max-w-2xl">
                <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-100">Buscar Factura/Nota</h2>
                <form id="search-form">
                    @csrf
                    <label for="search-input" class="block text-gray-700 dark:text-gray-200 mb-2">
                        Número de factura o nota:
                    </label>
                    <input type="text" id="search-input" name="numero"
                           class="w-full border rounded py-2 px-3 dark:bg-gray-700 dark:text-gray-200"
                           placeholder="Ejemplo: 12345">
                    <div class="flex justify-end mt-4">
                        <button type="button" id="close-search-modal"
                                class="mr-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 underline">
                            Cerrar
                        </button>
                        <button type="submit"
                                class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg shadow-md border border-blue-500">
                            Buscar
                        </button>
                    </div>
                </form>
                <hr class="my-4 border-gray-300 dark:border-gray-600">
                <div id="search-results" class="max-h-64 overflow-y-auto"></div>
            </div>
        </div>
    </div>

    <style>
        .new-pedido {
            animation: fadeIn 1s ease-in-out, flashRed 2s 10;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translatex(-60px);
            }
            to {
                opacity: 1;
                transform: translatex(0);
            }
        }

        @keyframes flashRed {
            0%, 100% {
                border-color: transparent;
            }
            50% {
                border-color: rgb(255, 32, 32);
            }
        }

        .pedido-container:hover {
            border-color: rgb(32, 128, 255);
            transition: border-color 0.3s ease-in-out;
        }
        .pedido-container {
            border-radius: 0.5rem;
            transition: border-color 0.3s ease-in-out;
        }

        /* Para pedidos completados colapsables */
        .pedido-header {
            cursor: pointer;
        }
        .pedido-body.hidden {
            display: none;
        }

        /* Scrollbars */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #2d3748;
        }
        ::-webkit-scrollbar-thumb {
            background: #4a5568;
            border-radius: 8px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #718096;
        }

        .scroll-container {
            max-height: 75vh;
            overflow-y: auto;
            padding-right: 10px;
        }
        .scroll-container2 {
            max-height: 70vh;
            overflow-y: auto;
            padding-right: 10px;
        }
    </style>

    <audio id="newOrderSound" src="{{ asset('audio/new-order.mp3') }}" preload="auto"></audio>

    <!-- Scripts (jQuery, sweetalert, etc.) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Arrays para rastrear pedidos nuevos
        let currentFacturas = [];
        let currentNotas = [];

        // Objeto global para recordar qué pedidos completados están abiertos
        let openCompletions = {}; 
        // Ejemplo: { 'factura_12345': true, 'nota_456': false }

        // =========================
        // FUNCION PRINCIPAL
        // =========================
        function fetchPedidos() {
            let ubicacion = $('#ubicacion').val();

            $.ajax({
                url: "{{ route('surtido.fetch') }}",
                method: "GET",
                data: { ubicacion },
                success: function(data) {
                    // Contenedores
                    let pedidosList = $('#pedidos-list');
                    let pedidosCompletadosList = $('#pedidos-completados-list');
                    let newPedidoFound = false;

                    // Limpiar
                    pedidosList.empty();
                    pedidosCompletadosList.empty();

                    // Extraer
                    let facturasPorSurtir = data.facturasPorSurtir || [];
                    let notasPorSurtir = data.notasPorSurtir || [];
                    let facturasSurtidas = data.facturasSurtidas || [];
                    let notasSurtidas = data.notasSurtidas || [];

                    // =========================
                    // 1. PEDIDOS POR SURTIR (SIN COLAPSAR)
                    // =========================
                    let todosPorSurtir = [
                        ...facturasPorSurtir.map(p => ({
                            ...p,
                            tipo: 'factura',
                            numero: p.FACTURA,
                            hora: p.HORA_CFD_FORMATEADA
                        })),
                        ...notasPorSurtir.map(p => ({
                            ...p,
                            tipo: 'nota',
                            numero: p.NOTA,
                            hora: p.HORA_FORMATEADA
                        }))
                    ];

                    // Ordenar
                    todosPorSurtir.sort((a, b) => {
                        let dateA = new Date(a.FechaGregoriana + ' ' + a.hora);
                        let dateB = new Date(b.FechaGregoriana + ' ' + b.hora);
                        return dateA - dateB;
                    });

                    // Agrupar
                    let pedidosGrouped = todosPorSurtir.reduce((grouped, pedido) => {
                        let key = pedido.tipo + '_' + pedido.numero;
                        if (!grouped[key]) {
                            grouped[key] = [];
                        }
                        grouped[key].push(pedido);
                        return grouped;
                    }, {});

                    // Render Título
                    pedidosList.append(`<h2 class="text-xl font-bold mb-2">Pedidos por surtir</h2>`);

                    // Render de cada pedido (factura/nota)
                    for (let key in pedidosGrouped) {
                        let pedidoGroup = pedidosGrouped[key];
                        let tipo = pedidoGroup[0].tipo;
                        let numero = parseInt(pedidoGroup[0].numero);
                        let fechaGregoriana = pedidoGroup[0].FechaGregoriana;
                        let hora = pedidoGroup[0].hora;
                        let agente = pedidoGroup[0].NombreAgente || '';
                        let titulo = (tipo === 'factura') ? `Factura #${numero}` : `Nota #${numero}`;

                        // ¿Es nuevo?
                        let isNewPedido = false;
                        if (tipo === 'factura') {
                            if (!currentFacturas.includes(numero)) {
                                isNewPedido = true;
                            }
                        } else {
                            if (!currentNotas.includes(numero)) {
                                isNewPedido = true;
                            }
                        }
                        if (isNewPedido) {
                            newPedidoFound = true;
                        }

                        let pedidoHtml = `
                          <div class="bg-white dark:bg-gray-900 shadow-md rounded-lg border border-transparent overflow-hidden relative pedido-container ${isNewPedido ? 'new-pedido' : ''} mb-3">
                            <div class="p-3">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-lg font-semibold">${titulo}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">${agente}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">${fechaGregoriana} / ${hora}</p>
                                </div>
                                <hr class="my-2 border-gray-300 dark:border-gray-600">
                                <div class="divide-y divide-gray-600 dark:divide-gray-500">
                        `;

                        // Productos
                        pedidoGroup.forEach(pedido => {
                            pedidoHtml += `
                                <div class="flex items-start py-3">
                                    <div class="w-1/12">
                                        <img src="https://infocontinenteferretero.com/imagenes_2022/${pedido.ARTICULO ? pedido.ARTICULO.trim() : ''}.jpg"
                                             alt="${pedido.DESCRIP}"
                                             class="w-full h-full object-cover rounded"
                                             onerror="this.onerror=null;this.src='{{ asset('images/notfound2.jpg') }}';">
                                    </div>
                                    <div class="w-8/12 pl-3">
                                        <p class="text-lg text-gray-600 dark:text-gray-400"><strong>Artículo:</strong> ${pedido.ARTICULO}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Producto:</strong> ${pedido.DESCRIP}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Ubicación:</strong> ${pedido.TONO || ''} | ${pedido.CALIBRE || ''} | ${pedido.CAJA || 'N/A'}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Cantidad:</strong> ${parseInt(pedido.CANTIDAD)} unidades</p>
                                    </div>
                                    <div class="w-3/12 flex flex-col items-end">
                                        <button class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-1 px-3 rounded-lg shadow-md marcar-surtido-producto-btn"
                                                data-tipo="${tipo}" data-numero="${pedido.NUMERO}" data-estado="1" style="margin-top:10px;">
                                            Surtir
                                        </button>
                                        <button class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-1 px-3 rounded-lg shadow-md marcar-surtido-producto-btn"
                                                data-tipo="${tipo}" data-numero="${pedido.NUMERO}" data-estado="2" style="margin-top:10px;">
                                            NF
                                        </button>
                                    </div>
                                </div>
                            `;
                        });

                        pedidoHtml += `
                                </div>
                            </div>
                          </div>
                        `;

                        pedidosList.append(pedidoHtml);

                        // Actualizar arrays
                        if (tipo === 'factura') {
                            if (!currentFacturas.includes(numero)) {
                                currentFacturas.push(numero);
                            }
                        } else {
                            if (!currentNotas.includes(numero)) {
                                currentNotas.push(numero);
                            }
                        }
                    }

                    // Si hay un pedido nuevo, reproducir sonido
                    if (newPedidoFound) {
                        let sound = document.getElementById('newOrderSound');
                        sound.play();
                    }

                    // =========================
                    // 2. PEDIDOS COMPLETADOS (COLAPSABLES)
                    // =========================
                    let todosCompletados = [
                        ...facturasSurtidas.map(f => ({
                            ...f,
                            tipo: 'factura',
                            numero: f.FACTURA,
                            hora: f.HORA_CFD_FORMATEADA // a veces se llama HORA_CFD_FORMATEADA
                        })),
                        ...notasSurtidas.map(n => ({
                            ...n,
                            tipo: 'nota',
                            numero: n.NOTA,
                            hora: n.HORA_FORMATEADA
                        }))
                    ];

                    // Ordenar por fecha/hora
                    todosCompletados.sort((a, b) => {
                        let dateA = new Date(a.FechaGregoriana + ' ' + a.hora);
                        let dateB = new Date(b.FechaGregoriana + ' ' + b.hora);
                        return dateB - dateA;
                    });

                    // Agrupar
                    let completadosGrouped = todosCompletados.reduce((grouped, item) => {
                        let key = item.tipo + '_' + item.numero;
                        if (!grouped[key]) {
                            grouped[key] = [];
                        }
                        grouped[key].push(item);
                        return grouped;
                    }, {});

                    pedidosCompletadosList.append(`<h4 class="font-bold mb-2">Últimos</h4>`);

                    // Generar HTML
                    for (let key in completadosGrouped) {
                        let group = completadosGrouped[key];
                        let tipo = group[0].tipo;
                        let numero = parseInt(group[0].numero);
                        let fechaGregoriana = group[0].FechaGregoriana;
                        let hora = group[0].hora;
                        let agente = group[0].NombreAgente || '';
                        let titulo = (tipo === 'factura') ? `Factura #${numero}` : `Nota #${numero}`;

                        // ¿Está abierto este pedido, según openCompletions?
                        let isOpen = openCompletions[key] === true; 
                        let bodyHiddenClass = isOpen ? '' : 'hidden'; 

                        let pedidoHtml = `
                            <div class="bg-white dark:bg-gray-900 shadow-md rounded-lg mb-3 border border-transparent pedido-container">
                                <!-- Cabecera colapsable, guarda el data-key para identificar -->
                                <div class="pedido-header p-3" data-key="${key}">
                                    <div class="flex justify-between items-center">
                                        <h4 class="text-md font-semibold">${titulo}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">${fechaGregoriana} / ${hora}</p>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Agente: ${agente}</p>
                                </div>
                                <!-- Cuerpo -->
                                <div class="pedido-body ${bodyHiddenClass} p-3">
                                    <hr class="my-2 border-gray-300 dark:border-gray-600">
                                    <div class="divide-y divide-gray-600 dark:divide-gray-500">
                        `;

                        // Productos
                        group.forEach(item => {
                            let estadoStr = (item.B_SURTIDO == 1)
                                ? 'Surtido'
                                : ((item.B_SURTIDO == 2) ? 'No encontrado' : 'Pendiente');

                            pedidoHtml += `
                                <div class="flex items-start py-3">
                                    <div class="w-1/12">
                                        <img src="https://infocontinenteferretero.com/imagenes_2022/${item.ARTICULO ? item.ARTICULO.trim() : ''}.jpg"
                                             alt="${item.DESCRIP}"
                                             class="w-full h-full object-cover rounded"
                                             onerror="this.onerror=null;this.src='{{ asset('images/notfound2.jpg') }}';">
                                    </div>
                                    <div class="w-11/12 pl-3">
                                        <p class="text-lg text-gray-600 dark:text-gray-400">
                                            <strong>Artículo:</strong> ${item.ARTICULO}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            <strong>Producto:</strong> ${item.DESCRIP}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            <strong>Cantidad:</strong> ${parseInt(item.CANTIDAD)} unidades
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            <strong>Ubicación:</strong> ${item.TONO || ''} | ${item.CALIBRE || ''} | ${item.CAJA || 'N/A'}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            <strong>Estado:</strong> ${estadoStr}
                                        </p>
                                    </div>
                                </div>
                            `;
                        });

                        pedidoHtml += `
                                    </div>
                                </div>
                            </div>
                        `;

                        pedidosCompletadosList.append(pedidoHtml);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error al obtener los pedidos:", error);
                }
            });
        }

        // =========================================================
        // Al hacer click en .pedido-header, alternamos su open/close
        // =========================================================
        $(document).on('click', '.pedido-header', function() {
            let key = $(this).data('key');  // p.ej. "factura_254349" o "nota_112"
            let body = $(this).siblings('.pedido-body');

            // Toggle visual
            body.toggleClass('hidden');
            // Actualizar estado en openCompletions
            openCompletions[key] = !body.hasClass('hidden');
            // true => abierto (sin 'hidden'), false => cerrado
        });

        // =========================================================
        // MARCAR UN PRODUCTO COMO SURTIDO / NO ENCONTRADO
        // =========================================================
        $(document).on('click', '.marcar-surtido-producto-btn', function() {
            let tipo = $(this).data('tipo');
            let numero = $(this).data('numero');
            let estado = $(this).data('estado'); // 1 = surtido, 2 = no encontrado
            let ubicacion = $('#ubicacion').val();

            let parentDiv = $(this).closest('.flex.items-start');
            let articulo = parentDiv.find('p:contains("Artículo:")').text().replace('Artículo:', '').trim();
            let descripcion = parentDiv.find('p:contains("Producto:")').text().replace('Producto:', '').trim();
            let cantidad = parentDiv.find('p:contains("Cantidad:")').text().replace('Cantidad:', '').trim();
            let accion = estado === 1 ? 'surtido' : 'no encontrado';

            let productoHtml = `
                <div style="margin-bottom: 10px; text-align: left;">
                    <p><strong>Artículo:</strong> ${articulo}</p>
                    <p><strong>Descripción:</strong> ${descripcion}</p>
                    <p><strong>Cantidad:</strong> ${cantidad}</p>
                </div>
            `;

            Swal.fire({
                title: '¿Estás seguro?',
                html: `
                    <p>Vas a marcar este producto como "${accion}".</p>
                    <div style="text-align: left; margin-top: 20px;">
                        <h3>Producto:</h3>
                        ${productoHtml}
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                background: '#2d3748',
                color: '#cbd5e0'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('surtido.marcarComoSurtido') }}",
                        method: "POST",
                        data: {
                            tipo,
                            numero,
                            estado,
                            ubicacion,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: '¡Marcado!',
                                    text: `El producto ha sido marcado como "${accion}".`,
                                    icon: 'success',
                                    background: '#1a1a1a',
                                    color: '#ffffff'
                                });
                                fetchPedidos();
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: response.message || 'Hubo un error al marcar el producto.',
                                    icon: 'error',
                                    background: '#1a1a1a',
                                    color: '#ffffff'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error:", xhr.responseText);
                            Swal.fire({
                                title: 'Error',
                                text: 'Error al marcar el producto.',
                                icon: 'error',
                                background: '#1a1a1a',
                                color: '#ffffff'
                            });
                        }
                    });
                }
            });
        });

        // =========================================================
        // MODAL DE BÚSQUEDA
        // =========================================================
        $('#open-search-modal').click(function() {
            $('#search-modal').removeClass('hidden');
            setTimeout(function() { $('#search-input').focus(); }, 100);
        });

        $('#close-search-modal').click(function() {
            $('#search-modal').addClass('hidden');
            $('#search-input').val('');
            $('#search-results').empty();
        });

        // =========================================================
        // BÚSQUEDA
        // =========================================================
        $('#search-form').submit(function(e) {
            e.preventDefault();
            let numero = $('#search-input').val().trim();
            if (!numero) {
                $('#search-results').html('<p class="text-red-500">Por favor, ingresa un número.</p>');
                return;
            }
            $('#search-results').html('<p class="text-gray-500">Buscando...</p>');
            $('#search-input').blur();

            $.ajax({
                url: "{{ route('surtido.buscar') }}",
                method: "GET",
                data: { numero },
                success: function(data) {
                    if (data.length === 0) {
                        $('#search-results').html('<p class="text-red-500">No se encontraron resultados.</p>');
                    } else {
                        let html = '<h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-100">Resultados:</h3>';
                        data.forEach(pedido => {
                            let titulo = (pedido.tipo === 'factura' ? 'Factura #' : 'Nota #') + parseInt(pedido.numero);
                            html += `
                                <div class="bg-white dark:bg-gray-700 shadow-md rounded-lg mb-3 p-3 border border-transparent">
                                    <h4 class="text-xl font-bold text-gray-800 dark:text-gray-100">${titulo}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">${pedido.FechaGregoriana} / ${pedido.hora}</p>
                                    <hr class="my-2 border-gray-300 dark:border-gray-600">
                            `;
                            pedido.items.forEach(item => {
                                html += `
                                    <div class="flex items-center mb-3">
                                        <div class="w-1/12">
                                            <img src="https://infocontinenteferretero.com/imagenes_2022/${item.ARTICULO ? item.ARTICULO.trim() : ''}.jpg"
                                                 alt="${item.DESCRIP}"
                                                 class="w-full h-full object-cover rounded"
                                                 onerror="this.onerror=null;this.src='{{ asset('images/notfound2.jpg') }}';">
                                        </div>
                                        <div class="w-9/12 pl-3">
                                            <p class="text-md text-gray-700 dark:text-gray-200"><strong>Artículo:</strong> ${item.ARTICULO}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-300"><strong>Producto:</strong> ${item.DESCRIP}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-300"><strong>Cantidad:</strong> ${parseInt(item.CANTIDAD)} unidades</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                                <strong>Surtido:</strong> ${
                                                    item.B_SURTIDO == 1
                                                        ? 'Sí'
                                                        : (item.B_SURTIDO == 2 ? 'No encontrado' : 'No')
                                                }
                                            </p>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                                <strong>Ubicación:</strong> Tono: ${item.TONO || ''}, Calibre: ${item.CALIBRE || ''}, Caja: ${item.CAJA || ''}
                                            </p>
                                        </div>
                                    </div>
                                `;
                            });
                            html += `</div>`;
                        });
                        $('#search-results').html(html);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error en la búsqueda:", error);
                    $('#search-results').html(
                        '<p class="text-red-500">Ocurrió un error al realizar la búsqueda.</p>'
                    );
                }
            });
        });

        // =========================================================
        // AL INICIAR
        // =========================================================
        $(document).ready(function() {
            fetchPedidos();
            // Auto-refresh cada 10s
            setInterval(fetchPedidos, 10000);

            // Manejo del panel completados (responsive)
            function handleOrientation() {
                const toggleButton = $('#toggle-completed-orders');
                const completedPanel = $('#panel-completados');
                const pedidosContainer = $('#pedidos-container');

                if (window.matchMedia("(orientation: portrait)").matches) {
                    toggleButton.hide();
                    if (!completedPanel.hasClass('hidden')) {
                        completedPanel.addClass('hidden');
                        pedidosContainer.removeClass('w-2/3').addClass('w-full');
                    }
                } else {
                    // Horizontal
                    toggleButton.show();
                }
            }

            $('#toggle-completed-orders').click(function() {
                $('#panel-completados').toggleClass('hidden');
                $('#pedidos-container').toggleClass('w-full w-2/3');
            });

            handleOrientation();
            $(window).resize(handleOrientation);
        });
    </script>
</x-app-layout>