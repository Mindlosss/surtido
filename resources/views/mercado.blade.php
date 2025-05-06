<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">

            
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('PEDIDOS MERCADO LIBRE') }}
            </h2>
            <button id="toggle-completed-orders" class="hidden sm:block bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 rounded-lg shadow-md border border-blue-500 flex items-center space-x-2 transition-transform transform duration-300 ease-in-out">
                <span>Mostrar completados</span>
            </button>
        </div>
    </x-slot>

    <div class="py-7">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg flex">
                <!-- Contenedor principal de pedidos por surtir -->
                <div id="pedidos-container" class="w-full p-3 pr-0 text-gray-900 dark:text-gray-100 relative">
                    <!-- Contenedor de la lista de pedidos con scroll -->
                    <div class="scroll-container" id="pedidos-list">
                        <!-- Aqui spawnearán los pedidos sin completar con js -->
                    </div> <!-- Fin del contenedor scrollable -->
                </div>

                <!-- Contenedor de pedidos completados (inicialmente oculto) -->
                <div class="w-1/3 p-3 text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-700 rounded-lg shadow-lg ml-3 hidden" id="panel-completados">
                    <h3 class="text-lg font-semibold mb-4 text-center">Últimos pedidos completados</h3>
                    <!-- Contenedor scroleable para pedidos completados -->
                    <div class="scroll-container2" id="pedidos-completados-list">
                        <!-- Aqui spawnearán los pedidos completados con js -->
                    </div>
                </div> <!-- Fin del panel de pedidos completados -->
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let currentPedidos = [];
        
        //FUNCION PARA ACTUALIZAR PEDIDOS
        function fetchPedidos() {
            $.ajax({
                url: "{{ route('mercado.fetch') }}", // Ajusta la ruta según tu definición
                method: "GET",
                success: function (data) {
                    let pedidosList = $('#pedidos-list');
                    let pedidosCompletadosList = $('#pedidos-completados-list');
                    let newPedidoFound = false;
                    pedidosList.empty();
                    pedidosCompletadosList.empty();
        
                    let pedidosPorSurtir = data.pedidosPorSurtir || [];
                    let pedidosSurtidos = data.pedidosSurtidos || [];
        
                    // Agrupamos por FACTURA
                    let pedidosGroupedByFactura = pedidosPorSurtir.reduce((grouped, pedido) => {
                        if (!grouped[pedido.FACTURA]) {
                            grouped[pedido.FACTURA] = [];
                        }
                        grouped[pedido.FACTURA].push(pedido);
                        return grouped;
                    }, {});

                    // Insertar el título de la sección
                    if (Object.keys(pedidosGroupedByFactura).length > 0) {
                        pedidosList.append(`<h2 class="text-xl font-bold mb-2">Pedidos por surtir</h2>`);
                    }

                    // Mostrar pedidos por surtir
                    for (let factura in pedidosGroupedByFactura) {
                        let pedidos = pedidosGroupedByFactura[factura];
                        let isNewPedido = !currentPedidos.includes(parseInt(factura));
                        let fechaGregoriana = pedidos[0].FechaGregoriana;

                        if (isNewPedido) {
                            newPedidoFound = true;
                        }
    
                        let pedidoHtml = `
                            <div class="bg-white dark:bg-gray-900 shadow-md rounded-lg border border-transparent overflow-hidden relative pedido-container ${isNewPedido ? 'new-pedido' : ''} mb-3">
                                <div class="p-3">
                                    <div class="flex justify-between items-center">
                                        <h3 class="text-lg font-semibold">#${parseInt(factura)}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">${fechaGregoriana}</p>
                                        <div class="flex space-x-2">
                                            <button class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-1 px-3 rounded-lg shadow-md marcar-surtido-btn" data-factura="${parseInt(factura)}" style="flex: 1;">
                                                Surtir
                                            </button>
                                            <button onclick="window.open('http://10.10.10.103/Facturas/Factura_GCI040331KZ2_${parseInt(factura)}M_${fechaGregoriana}.pdf')" 
                                                    class="bg-green-500 hover:bg-green-600 text-white font-semibold py-1 px-3 rounded-lg shadow-md" style="flex: 1;">
                                                Factura
                                            </button>
                                        </div>
                                    </div>
                                    <hr class="my-2 border-gray-300 dark:border-gray-600">`;
        
                        pedidos.forEach(pedido => {
                            pedidoHtml += `
                                <div class="flex items-center mb-3">
                                    <div class="w-1/12">
                                        <img src="https://infocontinenteferretero.com/imagenes_2022/${pedido.ARTICULO.trim()}.jpg" 
                                            alt="${pedido.DESCRIP}" 
                                            class="w-full h-full object-cover rounded"
                                            onerror="this.onerror=null;this.src='{{ asset('images/notfound2.jpg') }}';">
                                    </div>
                                    <div class="w-9/12 pl-3">
                                        <p class="text-lg text-gray-600 dark:text-gray-400"><strong>Artículo:</strong> ${pedido.ARTICULO}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Producto:</strong> ${pedido.DESCRIP}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Ubicación:</strong> Tono: ${pedido.TONO}, Calibre: ${pedido.CALIBRE}, Caja: ${pedido.CAJA}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Cantidad:</strong> ${parseInt(pedido.CANTIDAD)} unidades</p>
                                    </div>
                                </div>`;
                        });
        
                        pedidoHtml += `</div></div>`;
                        pedidosList.append(pedidoHtml);
                    }
        
                    // Mostrar pedidos completados
                    if (pedidosSurtidos.length > 0) {
                        pedidosCompletadosList.append(`<h4 class="font-bold mb-2">Pedidos surtidos</h4>`);
                    }
                    pedidosSurtidos.forEach(pedido => {
                        let pedidoCompletadoHtml = `
                            <div class="bg-white dark:bg-gray-900 shadow-md rounded-lg mb-3 p-3 border border-transparent pedido-container">
                                <h4 class="text-md font-semibold">Pedido #${parseInt(pedido.FACTURA)}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Completado el ${pedido.FechaGregoriana}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Artículos:</strong> ${pedido.num_articulos}</p>
                            </div>`;
                        pedidosCompletadosList.append(pedidoCompletadoHtml);
                    });
        
                    if (newPedidoFound) {
                        let sound = document.getElementById('newOrderSound');
                        sound.play();
                    }
        
                    currentPedidos = Object.keys(pedidosGroupedByFactura).map(factura => parseInt(factura));
                },
                error: function (xhr, status, error) {
                    console.error("Error al obtener los pedidos:", error);
                }
            });
        }

        //FUNCION PARA MARCAR COMO SURTIDO
        $(document).on('click', '.marcar-surtido-btn', function () {
            let factura = $(this).data('factura');

            let productosHtml = '';
            $(this).closest('.pedido-container').find('.flex.items-center').each(function () {
                let articulo = $(this).find('p:contains("Artículo")').text().replace('Artículo:', '').trim();
                let descripcion = $(this).find('p:contains("Producto")').text().replace('Producto:', '').trim();
                let cantidad = $(this).find('p:contains("Cantidad")').text().replace('Cantidad:', '').trim();

                if (articulo && descripcion && cantidad) {
                    productosHtml += `
                        <div style="margin-bottom: 10px; text-align: left;">
                            <p><strong>Artículo:</strong> ${articulo}</p>
                            <p><strong>Descripción:</strong> ${descripcion}</p>
                            <p><strong>Cantidad:</strong> ${cantidad}</p>
                        </div>
                        <hr style="border-color: #444;">
                    `;
                }
            });

            Swal.fire({
                title: '¿Estás seguro?',
                html: `
                    <p>Vas a marcar como surtido el pedido #${factura}.</p>
                    <div style="text-align: left; margin-top: 20px;">
                        <h3>Productos:</h3>
                        ${productosHtml}
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
                        url: "{{ route('mercado.marcarComoSurtido') }}", // Ajustar la ruta según tu definición
                        method: "POST",
                        data: {
                            factura: factura,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire({
                                    title: '¡Marcado!',
                                    text: 'El pedido ha sido marcado como surtido.',
                                    icon: 'success',
                                    background: '#1a1a1a',
                                    color: '#ffffff'
                                });
                                fetchPedidos();
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: response.message || 'Hubo un error al marcar el pedido como surtido.',
                                    icon: 'error',
                                    background: '#1a1a1a',
                                    color: '#ffffff'
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("Error al marcar como surtido:", xhr.responseText);
                            Swal.fire({
                                title: 'Error',
                                text: 'Error al marcar como surtido.',
                                icon: 'error',
                                background: '#1a1a1a',
                                color: '#ffffff'
                            });
                        }
                    });
                }
            });
        });

        //FUNCION BASE LLAMA A FETCHPEDIDOS EN CUANTO CARGA LA PAGINA Y LO REPITE CADA 10 SEGUNDOS
        $(document).ready(function () {
            fetchPedidos();
            setInterval(fetchPedidos, 10000);

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
                    toggleButton.show();
                }
            }

            $('#toggle-completed-orders').click(function () {
                $('#panel-completados').toggleClass('hidden');
                $('#pedidos-container').toggleClass('w-full w-2/3');
            });

            handleOrientation();
            $(window).resize(handleOrientation);
        });
    </script>
</x-app-layout>
