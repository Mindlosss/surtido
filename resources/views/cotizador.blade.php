<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('CATÁLOGO DE PRODUCTOS') }}
            </h2>
            <button id="toggleFilters" class="md:hidden bg-blue-500 text-white px-3 py-1 rounded">
                Filtros
            </button>
        </div>
    </x-slot>

    <div class="py-5">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8 flex flex-col md:flex-row">
            <!-- Panel de Filtros -->
            <aside id="filterPanel"
                   class="w-full md:w-1/5 bg-white dark:bg-gray-800 shadow rounded-lg p-4 hidden md:block">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">Filtros</h3>

                <!-- Filtro de Búsqueda -->
                <div class="mb-4">
                    <input type="text"
                           id="searchInput"
                           placeholder="Buscar producto..."
                           class="w-full p-2 border rounded dark:bg-gray-700 dark:text-gray-300">
                </div>

                <!-- Filtro por Precio  -->
                <div class="mb-4">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Rango de Precio
                    </label>
                    <input type="range" min="0" max="5000" class="w-full mt-2">
                </div>

                <!-- Filtro por Categoría (Dinámico) -->
                <div class="mb-4">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Categoría
                    </label>
                    <select id="selectCat" class="w-full p-2 mt-2 border rounded dark:bg-gray-700 dark:text-gray-300">
                        <!-- Se llenará dinámicamente con JS -->
                    </select>
                </div>

                <!-- Filtro por Clasificación (CLASIF_PROD_ABC) -->
                <div class="mb-4">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Clasificación MRP
                    </label>
                    <select id="selectClasif" class="w-full p-2 mt-2 border rounded dark:bg-gray-700 dark:text-gray-300">
                        <!-- Se llenará dinámicamente con JS -->
                    </select>
                </div>

                <!-- Filtro por Marca -->
                <div class="mb-4">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Marca
                    </label>
                    <select id="selectMarca" class="w-full p-2 mt-2 border rounded dark:bg-gray-700 dark:text-gray-300">
                        <!-- Se llenará dinámicamente con JS -->
                    </select>
                </div>

                <!-- Filtro por Disponibilidad (ejemplo estático, sin lógica de backend) -->
                <div>
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Disponibilidad
                    </label>
                    <div class="flex items-center mt-2">
                        <input type="checkbox" id="disponible" class="mr-2">
                        <label for="disponible" class="text-gray-700 dark:text-gray-300 text-sm">
                            Solo disponibles
                        </label>
                    </div>
                </div>
            </aside>

            <!-- Contenedor de Productos (posición 'relative' para centrar el spinner) -->
            <div class="relative w-full md:w-4/5 md:ml-6 bg-white dark:bg-gray-800 shadow rounded-lg p-4">
                <!-- Resumen de filtros -->
                <div id="filterSummary" class="text-sm text-gray-600 dark:text-gray-300 mb-2 hidden">
                    <!-- Se llenará dinámicamente -->
                </div>

                <!-- Spinner de carga (absoluto, centrado, más grande) -->
                <div
                    id="loadingSpinner"
                    class="absolute inset-0 flex items-center justify-center z-50 hidden"
                >
                    <div class="flex items-center">
                        <svg class="animate-spin h-12 w-12 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8v4l3.5-3.5L12 4z">
                            </path>
                        </svg>
                        <span class="text-gray-500 text-lg">Buscando...</span>
                    </div>
                </div>

                <div
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-4 h-[73vh] overflow-y-auto"
                    id="productos-container"
                >
                    <!-- Los productos se cargarán aquí dinámicamente -->
                </div>

                <!-- Mensaje cuando no hay resultados -->
                <div id="no-results" class="hidden text-center py-6">
                    <p class="text-gray-600 dark:text-gray-400">
                        No se encontraron productos con ese criterio de búsqueda
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Div oculto para la URL del fallback de imagen -->
    <div id="assetUrl" data-url="{{ asset('images/notfound2.jpg') }}" style="display: none;"></div>

    <!-- Plantilla de la tarjeta de producto -->
    <template id="product-card-template">
        <div
            class="relative bg-white dark:bg-gray-700 rounded-lg shadow p-4 flex flex-col hover:shadow-lg transition-shadow product-card">
            <div class="flex items-center">
                <img src="" alt="" class="w-16 h-16 object-cover rounded-md mr-3 product-image">
                <div>
                    <h3 class="text-md font-semibold text-gray-800 dark:text-gray-200 product-description"></h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 product-brand"></p>
                </div>
            </div>
            <div class="mt-3 border-t border-gray-300 dark:border-gray-600 pt-3 text-sm">
                <p class="font-semibold text-gray-800 dark:text-gray-300 product-price"></p>
                <p class="text-xs text-gray-500 dark:text-gray-400 product-stock"></p>
                <!-- Nuevo: mostrar el estatus del producto -->
                <p class="text-xs text-gray-500 dark:text-gray-400 product-status"></p>
            </div>
            <div class="mt-3 flex flex-col gap-2">
                <div class="flex gap-2">
                    <button class="bg-blue-500 text-white px-3 py-1 rounded text-sm w-full">
                        Solicitar
                    </button>
                    <button class="bg-green-500 text-white px-3 py-1 rounded text-sm w-full">
                        Cotizar
                    </button>
                </div>
                <button class="bg-gray-600 text-white px-3 py-1 rounded text-sm w-full ubicaciones-btn">
                    Ver ubicaciones
                </button>
            </div>
        </div>
    </template>

    <!-- Modal de Ubicaciones -->
    <div id="ubicacionesModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-11/12 md:w-2/3 lg:w-1/2 max-h-[80vh] overflow-y-auto p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Ubicaciones del Producto</h3>
                <button id="closeModal"
                        class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <table class="w-full text-sm text-left text-gray-600 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-2 py-1">TONO</th>
                    <th class="px-2 py-1">CALIBRE</th>
                    <th class="px-2 py-1">CAJA</th>
                    <th class="px-2 py-1">EXISTENCIA</th>
                </tr>
                </thead>
                <tbody id="ubicacionesModalBody"></tbody>
            </table>
        </div>
    </div>

    <style>
        /* Animación para el modal */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #ubicacionesModal {
            animation: fadeIn 0.3s ease-out;
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
    </style>

    <script>
        const assetUrl     = document.getElementById('assetUrl').dataset.url;
        const searchInput  = document.getElementById('searchInput');
        const selectCat    = document.getElementById('selectCat');
        const selectClasif = document.getElementById('selectClasif');
        const selectMarca  = document.getElementById('selectMarca');
        const container    = document.getElementById('productos-container');
        const noResults    = document.getElementById('no-results');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const filterSummary  = document.getElementById('filterSummary');

        // Al cargar la página, obtenemos las opciones de filtros (Categoría, Clasif, Marca)
        window.addEventListener('DOMContentLoaded', () => {
            fetch('/cotizador/filtros')
                .then(response => response.json())
                .then(data => {
                    // data.clasifs, data.marcas, data.cats
                    populateSelect(selectClasif, data.clasifs, 'clasif');
                    populateSelect(selectMarca,  data.marcas,  'marca');
                    populateSelect(selectCat,    data.cats,    'cat');
                })
                .catch(error => console.error('Error cargando filtros:', error));
        });

        // Función para inyectar opciones en un <select>
        function populateSelect(selectEl, itemsArray, fieldName) {
            // Limpia opciones anteriores
            selectEl.innerHTML = '';

            // Opción "Todas"
            const optAll = document.createElement('option');
            optAll.value = '';
            optAll.textContent = 'Todas';
            selectEl.appendChild(optAll);

            // Crear option por cada valor distinto
            itemsArray.forEach(item => {
                if (item[fieldName]) {
                    const opt = document.createElement('option');
                    opt.value = item[fieldName];
                    opt.textContent = item[fieldName];
                    selectEl.appendChild(opt);
                }
            });
        }

        // Debounce para no saturar con demasiadas peticiones
        function debounce(func, timeout = 400) {
            let timer;
            return (...args) => {
                clearTimeout(timer);
                timer = setTimeout(() => { func.apply(this, args); }, timeout);
            };
        }

        // Función principal para aplicar filtros y buscar
        function applyFilters() {
            const searchTerm = searchInput.value.trim();
            const cat        = selectCat.value;
            const clasif     = selectClasif.value;
            const marca      = selectMarca.value;

            // Mostrar u ocultar el resumen de filtros
            filterSummary.textContent = `Resumen de filtros → Búsqueda: ${searchTerm || 'N/A'} | `
                                      + `Categoría: ${cat || 'Todas'} | `
                                      + `Clasif: ${clasif || 'Todas'} | `
                                      + `Marca: ${marca || 'Todas'}`;
            filterSummary.classList.remove('hidden');

            // Resultados a partir de 4 caracteres
            if (searchTerm.length < 4) {
                container.innerHTML = '';
                noResults.classList.add('hidden');
                loadingSpinner.classList.add('hidden');
                return;
            }

            // Antes de la petición, mostrar spinner y limpiar contenedor
            noResults.classList.add('hidden');
            container.innerHTML = '';
            loadingSpinner.classList.remove('hidden');

            // Construir la URL con los parámetros
            const url = `/cotizador/search?search=${encodeURIComponent(searchTerm)}`
                      + `&cat=${encodeURIComponent(cat)}`
                      + `&clasif=${encodeURIComponent(clasif)}`
                      + `&marca=${encodeURIComponent(marca)}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    // Ocultamos el spinner
                    loadingSpinner.classList.add('hidden');

                    // Si no hay resultados
                    if (data.length === 0) {
                        noResults.classList.remove('hidden');
                        container.innerHTML = '';
                        return;
                    } else {
                        noResults.classList.add('hidden');
                    }

                    // Renderizar productos
                    data.forEach(producto => {
                        const card = document.getElementById('product-card-template')
                            .content
                            .cloneNode(true);

                        card.querySelector('.product-description').textContent = producto.DESCRIPCION;
                        card.querySelector('.product-brand').textContent       = `Marca: ${producto.MARCA}`;
                        card.querySelector('.product-price').textContent       = `$${parseFloat(producto.PRECIO_1_NETO).toFixed(2)}`;
                        card.querySelector('.product-stock').textContent       = `Stock total: ${producto.EX_T} unidades`;
                        card.querySelector('.product-status').textContent      = `Estatus: ${producto.ESTATUS || 'N/A'}`;

                        const imageElement = card.querySelector('.product-image');
                        // Intentar cargar imagen por CODIGO, o usar fallback
                        imageElement.src = `https://infocontinenteferretero.com/imagenes_2022/${producto.CODIGO.trim()}.jpg`;
                        imageElement.onerror = function() {
                            this.src = assetUrl;
                        };

                        // Botón de ubicaciones
                        const ubicacionesBtn = card.querySelector('.ubicaciones-btn');
                        ubicacionesBtn.addEventListener('click', () => {
                            showUbicacionesModal(producto.ubicaciones);
                        });

                        container.appendChild(card);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    loadingSpinner.classList.add('hidden');
                    noResults.classList.remove('hidden');
                    container.innerHTML = '';
                });
        }

        // Listener para campo de búsqueda (con debounce)
        searchInput.addEventListener('input', debounce(applyFilters, 500));

        // Listeners para cambios en los select de Categoría, Clasificación y Marca
        selectCat.addEventListener('change', applyFilters);
        selectClasif.addEventListener('change', applyFilters);
        selectMarca.addEventListener('change', applyFilters);

        // Toggle filters para mobile
        document.getElementById('toggleFilters').addEventListener('click', function() {
            const filterPanel = document.getElementById('filterPanel');
            filterPanel.classList.toggle('hidden');
        });

        // Modal de Ubicaciones
        function showUbicacionesModal(ubicaciones) {
            const modal = document.getElementById('ubicacionesModal');
            const modalBody = document.getElementById('ubicacionesModalBody');

            // Limpiar contenido anterior
            modalBody.innerHTML = '';

            // Rellenar modal con ubicaciones
            ubicaciones.forEach(ubicacion => {
                const row = document.createElement('tr');
                row.className = 'bg-white border-b dark:bg-gray-800 dark:border-gray-700';
                row.innerHTML = `
                    <td class="px-2 py-1">${ubicacion.TONO || 'N/A'}</td>
                    <td class="px-2 py-1">${ubicacion.CALIBRE || 'N/A'}</td>
                    <td class="px-2 py-1">${ubicacion.CAJA || 'N/A'}</td>
                    <td class="px-2 py-1">${ubicacion.EX}</td>
                `;
                modalBody.appendChild(row);
            });

            // Mostrar el modal
            modal.classList.remove('hidden');

            // Cerrar el modal
            const closeModal = () => {
                modal.classList.add('hidden');
            };

            document.getElementById('closeModal').addEventListener('click', closeModal);
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeModal();
                }
            });
        }
    </script>
</x-app-layout>
