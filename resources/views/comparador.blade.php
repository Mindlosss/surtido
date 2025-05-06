<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 leading-tight">
                Comparar órdenes de compra
            </h2>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-6xl mx-auto px-3 sm:px-4">
            <!-- Card contenedor principal más compacto -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border-l-4 border-blue-500">
                <!-- Encabezado compacto -->
                <div class="p-4">
                    <div class="flex items-center mb-2">
                        <div class="p-2 rounded-full bg-blue-100 dark:bg-blue-900 mr-3">
                            <i class="fas fa-search text-blue-600 dark:text-blue-300 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Seleccione un Pedido</h3>
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                (Últimos 90 días de pedidos)
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Formulario de filtros compacto -->
                <form method="GET" action="{{ route('comparador') }}" class="px-4 pb-4">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                        <!-- Buscar por número de pedido -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                N° Pedido
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <i class="fas fa-hashtag text-gray-400 text-sm"></i>
                                </div>
                                <input
                                    type="text"
                                    name="pedido"
                                    placeholder="Buscar..."
                                    value="{{ request('pedido') }}"
                                    class="pl-8 w-full p-1.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-transparent"
                                >
                            </div>
                        </div>

                        <!-- Filtro por proveedor compacto -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Proveedor
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <i class="fas fa-building text-gray-400 text-sm"></i>
                                </div>
                                <select
                                    name="provider"
                                    class="pl-8 w-full p-1.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-transparent appearance-none"
                                >
                                    <option value="all">Todos</option>
                                    @foreach($providers as $prov)
                                        <option value="{{ strtolower(trim($prov)) }}" {{ request('provider') == strtolower(trim($prov)) ? 'selected' : '' }}>
                                            {{ $prov }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Filtros de fecha compactos -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Desde
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <i class="fas fa-calendar-alt text-gray-400 text-sm"></i>
                                </div>
                                <input
                                    type="date"
                                    name="date_from"
                                    value="{{ request('date_from') }}"
                                    class="pl-8 w-full p-1.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-transparent"
                                >
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Hasta
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                    <i class="fas fa-calendar-alt text-gray-400 text-sm"></i>
                                </div>
                                <input
                                    type="date"
                                    name="date_to"
                                    value="{{ request('date_to') }}"
                                    class="pl-8 w-full p-1.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-transparent"
                                >
                            </div>
                        </div>

                        <!-- Botón Filtrar compacto -->
                        <div class="flex items-end">
                            <button
                                type="submit"
                                class="w-full h-[36px] bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-1 px-3 rounded-md transition-all flex items-center justify-center"
                            >
                                <i class="fas fa-filter mr-1.5 text-xs"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Formulario principal compacto -->
                <form id="pedidoForm" action="{{ route('comparador.comparar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="pedido" id="selected_pedido" value="">

                    <!-- Tabla de Pedidos compacta -->
                    <div class="px-4 pb-4">
                        <div class="overflow-x-auto rounded-md border border-gray-200 dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm" id="ordersTable">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            <i class="fas fa-hashtag mr-1 text-xs"></i>PEDIDO
                                        </th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            <i class="far fa-calendar-alt mr-1 text-xs"></i>FECHA
                                        </th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            <i class="fas fa-id-card mr-1 text-xs"></i>PROVEEDOR
                                        </th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            <i class="fas fa-signature mr-1 text-xs"></i>NOMBRE
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($paginatedOrders as $order)
                                        <tr 
                                            data-pedido="{{ $order->PEDIDO }}" 
                                            class="pedido-container cursor-pointer transition-colors duration-100 hover:bg-gray-50 dark:hover:bg-gray-700 text-xs"
                                        >
                                            <td class="px-3 py-2 whitespace-nowrap font-medium text-gray-900 dark:text-gray-200">
                                                {{ number_format($order->PEDIDO, 0, '', '') }}
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-gray-600 dark:text-gray-300">
                                                {{ $order->FECHA_OC }}
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-gray-600 dark:text-gray-300">
                                                {{ number_format($order->PROV, 0, '', '') }}
                                            </td>
                                            <td class="px-3 py-2 whitespace-nowrap text-gray-600 dark:text-gray-300">
                                                {{ $order->NOMBRE }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación compacta -->
                        <div class="mt-3 text-xs">
                            {{ $paginatedOrders->appends(request()->query())->links() }}
                        </div>
                    </div>

                    <!-- Sección de carga de archivo super compacta -->
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    <i class="fas fa-file-upload mr-1 text-xs"></i>Archivo XML
                                </label>
                                <input 
                                    type="file" 
                                    name="xml_file" 
                                    id="xml_file" 
                                    accept=".xml" 
                                    class="block w-full text-xs text-gray-600 dark:text-gray-300
                                        file:mr-2 file:py-1 file:px-3
                                        file:rounded-md file:border-0
                                        file:text-xs file:font-medium
                                        file:bg-blue-50 dark:file:bg-blue-900 file:text-blue-700 dark:file:text-blue-200
                                        hover:file:bg-blue-100 dark:hover:file:bg-blue-800
                                        cursor-pointer p-0.5"
                                >
                            </div>

                            <div class="flex items-end">
                                <button
                                    type="submit"
                                    id="compareButton"
                                    disabled
                                    class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium py-2 px-4 rounded-md transition-all flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <i class="fas fa-balance-scale-left mr-1.5 text-xs"></i> Comparar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Script para manejar la selección de la fila -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('.pedido-container');
            const selectedInput = document.getElementById('selected_pedido');
            const compareButton = document.getElementById('compareButton');
            const fileInput = document.getElementById('xml_file');

            rows.forEach(row => {
                row.addEventListener('click', function() {
                    rows.forEach(r => r.classList.remove('bg-blue-100', 'dark:bg-blue-900'));
                    row.classList.add('bg-blue-100', 'dark:bg-blue-900');
                    selectedInput.value = row.getAttribute('data-pedido');
                    
                    if(fileInput.files.length > 0) {
                        compareButton.disabled = false;
                    }
                });
            });

            fileInput.addEventListener('change', function() {
                compareButton.disabled = !(this.files.length > 0 && selectedInput.value !== '');
            });
        });
    </script>

    <!-- SweetAlert2 para mostrar errores -->
    @if ($errors->any())
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: '{!! implode("<br>", $errors->all()) !!}',
                    background: '#1e1e2f',
                    color: '#fff',
                    confirmButtonColor: '#3085d6',
                    iconColor: '#f87171',
                    confirmButtonText: 'Entendido',
                    width: '500px'
                });
            });
        </script>
    @endif
</x-app-layout>