<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('CATÁLOGO DE PRODUCTOS') }}
            </h2>
            <button id="toggleFilters" class="md:hidden bg-blue-500 text-white px-3 py-1 rounded focus:outline-none focus:ring-2 focus:ring-blue-300">
                Filtros
            </button>
        </div>
    </x-slot>

    <div class="py-5">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8 flex flex-col md:flex-row gap-4">
            <!-- Panel de Filtros -->
            <aside id="filterPanel" class="w-full md:w-1/5 bg-white dark:bg-gray-800 shadow-lg rounded-lg p-4 hidden md:block">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Filtros</h3>
                <div class="space-y-4">
                    <input type="text" id="searchInput" placeholder="Buscar producto..."
                           class="w-full p-2 border rounded dark:bg-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-400" />
                    <div>
                        <label for="maxPrice" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Precio máximo</label>
                        <input type="number" id="maxPrice" min="0" step="0.01" placeholder="0.00"
                               class="w-full p-2 border rounded dark:bg-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-400" />
                    </div>
                    <select id="selectCat" class="w-full p-2 border rounded dark:bg-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-400">
                        <option value="">Selecciona una categoría</option>
                    </select>
                    <select id="selectClasif" class="w-full p-2 border rounded dark:bg-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-400">
                        <option value="">Selecciona una clasificación MRP</option>
                    </select>
                    <select id="selectMarca" class="w-full p-2 border rounded dark:bg-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-400">
                        <option value="">Selecciona una marca</option>
                    </select>
                    <label class="inline-flex items-center">
                        <input type="checkbox" id="disponible" class="form-checkbox h-4 w-4 text-blue-600" />
                        <span class="ml-2 text-gray-700 dark:text-gray-300 text-sm">Solo disponibles</span>
                    </label>
                </div>
            </aside>

            <!-- Contenedor de Productos -->
            <section class="relative w-full md:w-4/5 bg-white dark:bg-gray-800 shadow-lg rounded-lg p-4">
                <div class="flex items-center justify-between mb-4">
                    <div id="filterSummary" class="text-sm text-gray-600 dark:text-gray-300"></div>
                    <div id="resultCount" class="text-sm font-medium text-gray-800 dark:text-gray-200">Resultados: 0</div>
                </div>
                <div id="productos-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 overflow-y-auto h-[70vh] pr-2"></div>
                <div id="no-results" class="hidden text-center py-6">
                    <p class="text-gray-600 dark:text-gray-400">No se encontraron productos según los filtros.</p>
                </div>
            </section>
        </div>
    </div>

    <!-- Fallback imagen -->
    <div id="assetUrl" data-url="{{ asset('images/notfound2.jpg') }}" class="hidden"></div>

    <!-- Plantilla de tarjeta -->
    <template id="product-card-template">
        <article class="bg-white dark:bg-gray-700 rounded-lg shadow hover:shadow-lg transition p-4 flex flex-col" tabindex="0">
            <div class="flex items-center gap-3">
                <img src="" alt="Imagen de producto" class="w-16 h-16 object-cover rounded-md product-image" />
                <div class="flex-1">
                    <h3 class="text-md font-semibold text-gray-800 dark:text-gray-200 product-description"></h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 product-brand"></p>
                </div>
            </div>
            <div class="mt-3 border-t border-gray-300 dark:border-gray-600 pt-3 text-sm flex-1">
                <p class="font-semibold text-gray-800 dark:text-gray-300 product-price"></p>
                <p class="text-xs text-gray-500 dark:text-gray-400 product-stock"></p>
                <p class="text-xs text-gray-500 dark:text-gray-400 product-status"></p>
            </div>
            <div class="mt-3 space-y-2">
                <button class="w-full py-1 rounded text-sm bg-blue-600 hover:bg-blue-700 text-white focus:outline-none">Solicitar</button>
                <button class="w-full py-1 rounded text-sm bg-green-600 hover:bg-green-700 text-white focus:outline-none">Cotizar</button>
                <button class="w-full py-1 rounded text-sm bg-gray-600 hover:bg-gray-700 text-white focus:outline-none ubicaciones-btn">Ver ubicaciones</button>
            </div>
        </article>
    </template>

    <!-- Modal Ubicaciones -->
    <div id="ubicacionesModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-11/12 md:w-2/3 lg:w-1/2 max-h-[80vh] overflow-y-auto p-6 animate-fadeIn">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Ubicaciones del Producto</h3>
                <button id="closeModal" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <table class="w-full text-sm text-left text-gray-600 dark:text-gray-400">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-2 py-1">Tono</th>
                        <th class="px-2 py-1">Calibre</th>
                        <th class="px-2 py-1">Caja</th>
                        <th class="px-2 py-1">Existencia</th>
                    </tr>
                </thead>
                <tbody id="ubicacionesModalBody"></tbody>
            </table>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #2d3748; }
        ::-webkit-scrollbar-thumb { background: #4a5568; border-radius: 8px; }
        ::-webkit-scrollbar-thumb:hover { background: #718096; }
    </style>

    <script>
    (function() {
        const assetUrl = document.getElementById('assetUrl').dataset.url;
        const searchInput = document.getElementById('searchInput');
        const selects = {
            cat: document.getElementById('selectCat'),
            clasif: document.getElementById('selectClasif'),
            marca: document.getElementById('selectMarca')
        };
        const disponibleChk = document.getElementById('disponible');
        const maxPriceInput = document.getElementById('maxPrice');
        const filterSummary = document.getElementById('filterSummary');
        const resultCount = document.getElementById('resultCount');
        const container = document.getElementById('productos-container');
        const noResults = document.getElementById('no-results');
        const tmpl = document.getElementById('product-card-template');

        document.addEventListener('DOMContentLoaded', () => {
            selects.cat.innerHTML = '<option value="">Selecciona una categoría</option>';
            selects.clasif.innerHTML = '<option value="">Selecciona una clasificación MRP</option>';
            selects.marca.innerHTML = '<option value="">Selecciona una marca</option>';
            fetch('/cotizador/filtros')
                .then(res => res.json())
                .then(data => {
                    populateSelect(selects.cat, data.cats, 'cat');
                    populateSelect(selects.clasif, data.clasifs, 'clasif');
                    populateSelect(selects.marca, data.marcas, 'marca');
                });
        });

        function populateSelect(sel, items, field) {
            const frag = document.createDocumentFragment();
            items.forEach(it => {
                if (it[field]) {
                    const opt = document.createElement('option'); opt.value = it[field]; opt.textContent = it[field]; frag.appendChild(opt);
                }
            }); sel.appendChild(frag);
        }

        function debounce(fn, delay=300) { let timer; return (...args) => { clearTimeout(timer); timer = setTimeout(() => fn(...args), delay); }; }

        function showLoading() { Swal.fire({ title: 'Buscando productos', html: `<div class="flex flex-col items-center"><div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-blue-500 mb-3"></div><p class="text-gray-300 text-sm">Cargando productos...</p></div>`, background: '#1e1e2f', color: '#fff', showConfirmButton: false, allowOutsideClick: false }); }
        function hideLoading() { Swal.close(); }

        function applyFilters() {
            const term = searchInput.value.trim();
            if (term.length < 3) { container.classList.add('hidden'); noResults.classList.add('hidden'); resultCount.textContent = 'Resultados: 0'; return; }
            const params = new URLSearchParams({ search: term, cat: selects.cat.value, clasif: selects.clasif.value, marca: selects.marca.value });
            showLoading();
            fetch(`/cotizador/search?${params}`)
                .then(res => res.json())
                .then(data => {
                    hideLoading(); let results = data;
                    if (disponibleChk.checked) results = results.filter(p => p.EX_T > 0);
                    const maxPrice = parseFloat(maxPriceInput.value);
                    if (!isNaN(maxPrice)) results = results.filter(p => parseFloat(p.PRECIO_1_NETO) <= maxPrice);
                    const catVal = selects.cat.value;
                    if (catVal) results = results.filter(p => p.CATEGORIA === catVal);
                    resultCount.textContent = `Resultados: ${results.length}`;
                    filterSummary.textContent = `Filtros → Búsqueda: ${term||'N/A'} | Cat: ${selects.cat.value||'Todas'} | Cls: ${selects.clasif.value||'Todas'} | Marca: ${selects.marca.value||'Todas'} | Disponibles: ${disponibleChk.checked?'Sí':'No'} | Precio ≤ ${maxPriceInput.value||'N/A'}`;
                    if (!results.length) { container.classList.add('hidden'); noResults.classList.remove('hidden'); return; }
                    noResults.classList.add('hidden'); container.classList.remove('hidden');
                    const frag = document.createDocumentFragment(); results.forEach(prod => { const card = tmpl.content.cloneNode(true);
                        card.querySelector('.product-description').textContent = prod.DESCRIPCION;
                        card.querySelector('.product-brand').textContent = prod.MARCA;
                        card.querySelector('.product-price').textContent = `$${parseFloat(prod.PRECIO_1_NETO).toFixed(2)}`;
                        card.querySelector('.product-stock').textContent = `Stock: ${prod.EX_T}`;
                        card.querySelector('.product-status').textContent = `Estatus: ${prod.ESTATUS || 'N/A'}`;
                        const img = card.querySelector('.product-image');
                        img.src = `https://infocontinenteferretero.com/imagenes_2022/${prod.CODIGO.trim()}.jpg`;
                        img.onerror = () => img.src = assetUrl;
                        card.querySelector('.ubicaciones-btn').addEventListener('click', () => showLocations(prod.ubicaciones));
                        frag.appendChild(card);
                    });
                    container.innerHTML = ''; container.appendChild(frag);
                })
                .catch(() => { hideLoading(); container.classList.add('hidden'); noResults.classList.remove('hidden'); });
        }

        searchInput.addEventListener('input', debounce(applyFilters));
        disponibleChk.addEventListener('change', applyFilters);
        Object.values(selects).forEach(sel => sel.addEventListener('change', applyFilters));
        maxPriceInput.addEventListener('input', debounce(applyFilters));
        document.getElementById('toggleFilters').addEventListener('click', () => document.getElementById('filterPanel').classList.toggle('hidden'));

        // Modal Ubicaciones
        const modal = document.getElementById('ubicacionesModal');
        const modalBody = document.getElementById('ubicacionesModalBody');
        document.getElementById('closeModal').addEventListener('click', () => modal.classList.add('hidden'));
        modal.addEventListener('click', e => e.target === modal && modal.classList.add('hidden'));
        function showLocations(locs) {
            modalBody.innerHTML = '';
            locs.forEach(loc => {
                const tr = document.createElement('tr'); tr.className = 'odd:bg-white even:bg-gray-100 dark:odd:bg-gray-800 dark:even:bg-gray-700';
                tr.innerHTML = `<td class="px-2 py-1">${loc.TONO||'N/A'}</td><td class="px-2 py-1">${loc.CALIBRE||'N/A'}</td><td class="px-2 py-1">${loc.CAJA||'N/A'}</td><td class="px-2 py-1">${loc.EX}</td>`;
                modalBody.appendChild(tr);
            }); modal.classList.remove('hidden');
        }
    })();
    </script>
</x-app-layout>
