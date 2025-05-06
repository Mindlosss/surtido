<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white leading-tight">
                Resultado de la Comparación
            </h2>
            <a href="{{ route('comparador') }}" class="flex items-center text-sm bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-200">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
        </div>
    </x-slot>

    <div class="py-7">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <!-- Resumen General Mejorado -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Resumen ERP -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border-t-4 border-indigo-500">
                    <div class="p-5">
                        <div class="flex items-center mb-3">
                            <div class="p-2 rounded-full bg-indigo-100 dark:bg-indigo-900 mr-3">
                                <i class="fas fa-database text-indigo-600 dark:text-indigo-300"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Datos ERP</h3>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-300">Productos totales</span>
                                <span class="font-semibold text-gray-800 dark:text-white">{{ $totalErpProducts }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-300">Productos únicos</span>
                                <span class="font-semibold text-gray-800 dark:text-white">{{ $uniqueErpProducts }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-300">Importe total</span>
                                <span class="font-semibold text-gray-800 dark:text-white">
                                    ${{ number_format($erpHeader->IMP_TOTAL, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumen XML -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border-t-4 border-purple-500">
                    <div class="p-5">
                        <div class="flex items-center mb-3">
                            <div class="p-2 rounded-full bg-purple-100 dark:bg-purple-900 mr-3">
                                <i class="fas fa-file-code text-purple-600 dark:text-purple-300"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Datos XML</h3>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-300">Productos totales</span>
                                <span class="font-semibold text-gray-800 dark:text-white">{{ $totalXmlProducts }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-300">Productos únicos</span>
                                <span class="font-semibold text-gray-800 dark:text-white">{{ $uniqueXmlProducts }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-300">Importe total (IVA incluido.)</span>
                                <span class="font-semibold text-gray-800 dark:text-white">
                                    ${{ number_format(array_sum(array_column($combinedProducts, 'xml_total')) * 1.16, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumen de Diferencias -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border-t-4 border-blue-500">
                    <div class="p-5">
                        <div class="flex items-center mb-3">
                            <div class="p-2 rounded-full bg-blue-100 dark:bg-blue-900 mr-3">
                                <i class="fas fa-balance-scale text-blue-600 dark:text-blue-300"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Resumen Diferencias</h3>
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-300">Productos faltantes</span>
                                <span class="font-semibold text-red-600 dark:text-red-400">{{ count($missingInXml) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-300">Productos adicionales</span>
                                <span class="font-semibold text-purple-600 dark:text-purple-400">{{ count($extraInXml) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-300">Diferencias cantidad</span>
                                <span class="font-semibold text-yellow-600 dark:text-yellow-400">{{ count($quantityDifferences) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjeta de Información del Pedido Mejorada -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border-l-4 border-blue-500">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 mr-4">
                            <i class="fas fa-info-circle text-blue-600 dark:text-blue-300"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">Detalles del Pedido {{ $erpHeader->PEDIDO }}</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-300 mb-1">PROVEEDOR</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $erpHeader->PROV}}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-300 mb-1">NOMBRE</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $erpHeader->NOMBRE }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-300 mb-1">FECHA</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ \Carbon\Carbon::parse($erpHeader->FECHA_OC)->format('d/m/Y') }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-300 mb-1">ESTADO</p>
                            <p class="text-sm font-semibold {{ $totalErpProducts == $totalXmlProducts && empty($missingInXml) && empty($extraInXml) && empty($quantityDifferences) ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                                {{ $totalErpProducts == $totalXmlProducts && empty($missingInXml) && empty($extraInXml) && empty($quantityDifferences) ? 'CONCILIADO' : 'PENDIENTE' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros y Controles Mejorados -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                <div class="p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center">
                            <label for="searchProduct" class="mr-2 text-sm text-gray-600 dark:text-gray-300">Buscar:</label>
                            <input type="text" id="searchProduct" placeholder="Código de producto..." 
                                   class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <label for="filterStatus" class="mr-2 text-sm text-gray-600 dark:text-gray-300">Filtrar:</label>
                                <select id="filterStatus" class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-md focus:ring-1 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                    <option value="all">Todos</option>
                                    <option value="match">Conciliados</option>
                                    <option value="quantity">Dif. Cantidad</option>
                                    <option value="price">Dif. Precio</option>
                                    <option value="missing">Faltantes</option>
                                    <option value="extra">Adicionales</option>
                                </select>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="toggleHideComplete" class="mr-2 rounded text-blue-600 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600" checked>
                                <label for="toggleHideComplete" class="text-sm text-gray-600 dark:text-gray-300">Ocultar completados</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Comparación Mejorada -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Producto</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cantidad</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Precio Unitario</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Precio Total</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Diferencia</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($combinedProducts as $prod)
                                @php
                                    // Inicializar arrays para múltiples estados
                                    $statuses = [];
                                    $statusClasses = [];
                                    $statusIcons = [];
                                    $statusTexts = [];
                                    $differences = [];
                                    $differenceClasses = [];
                                    $differenceIcons = [];

                                    // Verificar si el producto está faltante en el XML
                                    if($prod['xml_quantity'] === null) {
                                        $statuses[] = 'missing';
                                        $statusClasses[] = 'bg-red-50 dark:bg-red-900/30';
                                        $statusIcons[] = 'fa-times-circle text-red-500 dark:text-red-400';
                                        $statusTexts[] = 'Faltante';
                                        $differences[] = $prod['erp_quantity'];
                                        $differenceClasses[] = 'text-red-600 dark:text-red-400';
                                        $differenceIcons[] = 'fa-minus';
                                    }
                                    // Verificar si el producto es adicional en el XML
                                    elseif($prod['erp_quantity'] === null) {
                                        $statuses[] = 'extra';
                                        $statusClasses[] = 'bg-purple-50 dark:bg-purple-900/30';
                                        $statusIcons[] = 'fa-plus-circle text-purple-500 dark:text-purple-400';
                                        $statusTexts[] = 'Adicional';
                                        $differences[] = $prod['xml_quantity'];
                                        $differenceClasses[] = 'text-purple-600 dark:text-purple-400';
                                        $differenceIcons[] = 'fa-plus';
                                    }
                                    
                                    // Verificar diferencias en cantidades
                                    $quantityDiff = 0;
                                    if($prod['erp_quantity'] !== null && $prod['xml_quantity'] !== null) {
                                        $quantityDiff = $prod['xml_quantity'] - $prod['erp_quantity'];
                                        if($quantityDiff != 0) {
                                            $statuses[] = 'quantity';
                                            $statusClasses[] = 'bg-yellow-50 dark:bg-yellow-900/30';
                                            $statusIcons[] = 'fa-exclamation-circle text-yellow-500 dark:text-yellow-400';
                                            $statusTexts[] = 'Dif. Cantidad';
                                            $differences[] = $quantityDiff . ' uds.';
                                            $differenceClass = $quantityDiff > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400';
                                            $differenceClasses[] = $differenceClass;
                                            $differenceIcons[] = $quantityDiff > 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                                        }
                                    }

                                    // Verificar diferencias en precios unitarios (con tolerancia del 1%)
                                    $priceDiff = 0;
                                    if($prod['erp_unit'] !== null && $prod['xml_unit'] !== null) {
                                        $priceDiff = $prod['xml_unit'] - $prod['erp_unit'];
                                        if(abs($priceDiff) > ($prod['erp_unit'] * 0.01)) {
                                            $statuses[] = 'price';
                                            if($priceDiff < 0) {
                                                $statusClasses[] = 'bg-blue-50 dark:bg-blue-900/30';
                                                $statusIcons[] = 'fa-arrow-down text-blue-500 dark:text-blue-400';
                                                $statusTexts[] = 'Precio favorable';
                                                $differences[] = '$' . number_format(abs($priceDiff), 2);
                                                $differenceClasses[] = 'text-blue-600 dark:text-blue-400';
                                                $differenceIcons[] = 'fa-arrow-down';
                                            } else {
                                                $statusClasses[] = 'bg-red-50 dark:bg-red-900/30';
                                                $statusIcons[] = 'fa-arrow-up text-red-500 dark:text-red-400';
                                                $statusTexts[] = 'Precio desfavorable';
                                                $differences[] = '$' . number_format(abs($priceDiff), 2);
                                                $differenceClasses[] = 'text-red-600 dark:text-red-400';
                                                $differenceIcons[] = 'fa-arrow-up';
                                            }
                                        }
                                    }
                                    
                                    // Determinar clase final (tomamos la primera si hay múltiples)
                                    $finalStatusClass = $statusClasses[0] ?? 'bg-green-50 dark:bg-green-900/30';
                                    $finalStatusText = implode(' + ', $statusTexts) ?: 'Conciliado';
                                @endphp
                                
                                <tr class="{{ $finalStatusClass }} hover:opacity-90 transition-opacity" 
                                    data-status="{{ implode(' ', $statuses) }}"
                                    data-product="{{ $prod['key'] }}"
                                    data-erp-quantity="{{ $prod['erp_quantity'] ?? 0 }}">
                                    <!-- Código del Producto -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-800 dark:text-white">
                                                {{ $prod['key'] }}
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- Cantidad -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col space-y-1">
                                            <div class="flex items-center">
                                                <span class="inline-block w-3 h-3 rounded-full bg-indigo-500 mr-1"></span>
                                                <span class="text-xs text-gray-600 dark:text-gray-300">ERP: {{ $prod['erp_quantity'] ?? '-' }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <span class="inline-block w-3 h-3 rounded-full bg-purple-500 mr-1"></span>
                                                <span class="text-xs text-gray-600 dark:text-gray-300">XML: {{ $prod['xml_quantity'] ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- Precio Unitario -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col space-y-1">
                                            <div class="text-xs text-gray-600 dark:text-gray-300">
                                                ERP: ${{ $prod['erp_unit'] !== null ? number_format($prod['erp_unit'], 2) : '-' }}
                                            </div>
                                            <div class="text-xs font-medium {{ in_array('price', $statuses) ? $differenceClasses[array_search('price', $statuses)] : 'text-gray-600 dark:text-gray-300' }}">
                                                XML: ${{ $prod['xml_unit'] !== null ? number_format($prod['xml_unit'], 2) : '-' }}
                                            </div>
                                            @if(in_array('price', $statuses))
                                            <div class="text-xs {{ $differenceClasses[array_search('price', $statuses)] }}">
                                                {{ $priceDiff > 0 ? '+' : '' }}{{ number_format($priceDiff, 2) }} ({{ number_format(abs($priceDiff / $prod['erp_unit'] * 100), 2) }}%)
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <!-- Precio Total -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col space-y-1">
                                            <div class="text-xs text-gray-600 dark:text-gray-300">
                                                ERP: ${{ $prod['erp_total'] !== null ? number_format($prod['erp_total'], 2) : '-' }}
                                            </div>
                                            <div class="text-xs font-medium {{ in_array('price', $statuses) ? $differenceClasses[array_search('price', $statuses)] : 'text-gray-600 dark:text-gray-300' }}">
                                                XML: ${{ $prod['xml_total'] !== null ? number_format($prod['xml_total'], 2) : '-' }}
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- Estado -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center flex-wrap gap-2">
                                            @foreach($statusIcons as $icon)
                                                <i class="fas {{ $icon }}"></i>
                                            @endforeach
                                            <span class="text-xs font-medium dark:text-white">{{ $finalStatusText }}</span>
                                        </div>
                                    </td>
                                    
                                    <!-- Diferencia -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col gap-1">
                                            @foreach($differences as $index => $diff)
                                                <div class="flex items-center {{ $differenceClasses[$index] }}">
                                                    <i class="fas {{ $differenceIcons[$index] }} mr-1 text-xs"></i>
                                                    <span class="text-xs font-medium">{{ $diff }}</span>
                                                </div>
                                            @endforeach
                                            @if(empty($differences))
                                                <span class="text-xs dark:text-gray-300">-</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Resumen de Ahorros/Pérdidas -->
            @php
                $savings = 0;
                $losses = 0;
                
                foreach($combinedProducts as $prod) {
                    if($prod['erp_unit'] !== null && $prod['xml_unit'] !== null) {
                        $diff = $prod['erp_unit'] - $prod['xml_unit'];
                        if($diff > 0) {
                            $savings += $diff * ($prod['xml_quantity'] ?? 0);
                        } elseif($diff < 0) {
                            $losses += abs($diff) * ($prod['xml_quantity'] ?? 0);
                        }
                    }
                }
            @endphp
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Ahorros -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border-l-4 border-green-500">
                    <div class="p-5">
                        <div class="flex items-center mb-2">
                            <div class="p-2 rounded-full bg-green-100 dark:bg-green-900 mr-3">
                                <i class="fas fa-piggy-bank text-green-600 dark:text-green-300"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Ahorros Potenciales</h3>
                        </div>
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400 mb-1">
                            ${{ number_format($savings, 2) }}
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Productos con precio más bajo en el XML
                        </p>
                    </div>
                </div>
                
                <!-- Pérdidas -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden border-l-4 border-red-500">
                    <div class="p-5">
                        <div class="flex items-center mb-2">
                            <div class="p-2 rounded-full bg-red-100 dark:bg-red-900 mr-3">
                                <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-300"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Pérdidas Potenciales</h3>
                        </div>
                        <div class="text-2xl font-bold text-red-600 dark:text-red-400 mb-1">
                            ${{ number_format($losses, 2) }}
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Productos con precio más alto en el XML
                        </p>
                    </div>
                </div>
            </div>

            <!-- Sección de Discrepancias Detallada -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden" id="discrepanciesSection">
                <div class="p-6">
                    <div class="flex items-center mb-6">
                        <div class="p-3 rounded-full bg-orange-100 dark:bg-orange-900 mr-4">
                            <i class="fas fa-exclamation-triangle text-orange-600 dark:text-orange-300"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white">Discrepancias Detalladas</h3>
                    </div>
                    
                    <!-- Pestañas para diferentes tipos de discrepancias -->
                    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                        <ul class="flex flex-wrap -mb-px" id="discrepancyTabs" role="tablist">
                            <li class="mr-2" role="presentation">
                                <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" 
                                        id="missing-tab" data-tabs-target="#missing" type="button" role="tab" 
                                        aria-controls="missing" aria-selected="false">
                                    <span class="flex items-center dark:text-white">
                                        <i class="fas fa-times-circle text-red-500 mr-2"></i>
                                        Faltantes ({{ count($missingInXml) + count(array_filter($quantityDifferences, function($diff) { return ($diff['xml_quantity'] - $diff['erp_quantity']) < 0; })) }})
                                    </span>
                                </button>
                            </li>
                            <li class="mr-2" role="presentation">
                                <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" 
                                        id="extra-tab" data-tabs-target="#extra" type="button" role="tab" 
                                        aria-controls="extra" aria-selected="false">
                                    <span class="flex items-center dark:text-white">
                                        <i class="fas fa-plus-circle text-purple-500 mr-2"></i>
                                        Adicionales ({{ count($extraInXml) + count(array_filter($quantityDifferences, function($diff) { return ($diff['xml_quantity'] - $diff['erp_quantity']) > 0; })) }})
                                    </span>
                                </button>
                            </li>
                            <li class="mr-2" role="presentation">
                                <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" 
                                        id="quantity-tab" data-tabs-target="#quantity" type="button" role="tab" 
                                        aria-controls="quantity" aria-selected="false">
                                    <span class="flex items-center dark:text-white">
                                        <i class="fas fa-equals text-yellow-500 mr-2"></i>
                                        Dif. Cantidad ({{ count($quantityDifferences) }})
                                    </span>
                                </button>
                            </li>
                            <li class="mr-2" role="presentation">
                                <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" 
                                        id="price-tab" data-tabs-target="#price" type="button" role="tab" 
                                        aria-controls="price" aria-selected="false">
                                    <span class="flex items-center dark:text-white">
                                        <i class="fas fa-dollar-sign text-blue-500 mr-2"></i>
                                        Dif. Precio
                                    </span>
                                </button>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Contenido de las pestañas -->
                    <div id="discrepancyContent">
                        <!-- Pestaña de Productos Faltantes (modificada) -->
                        <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-700" id="missing" role="tabpanel" aria-labelledby="missing-tab">
                            @php
                                // Productos completamente faltantes en XML
                                $realMissing = $missingInXml;
                                
                                // Productos con cantidad menor en XML (diferencia negativa significativa)
                                $quantityShort = array_filter($quantityDifferences, function($diff) {
                                    return ($diff['xml_quantity'] - $diff['erp_quantity']) < 0;
                                });
                                
                                $allMissing = array_merge(
                                    $realMissing,
                                    array_keys($quantityShort)
                                );
                            @endphp
                            
                            @if(count($allMissing))
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-100 dark:bg-gray-600">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Producto</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipo</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cantidad ERP</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cantidad XML</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Diferencia</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($allMissing as $key)
                                                @php
                                                    $product = collect($combinedProducts)->firstWhere('key', $key);
                                                    $isRealMissing = in_array($key, $realMissing);
                                                    $difference = $isRealMissing ? 
                                                        $product['erp_quantity'] : 
                                                        ($product['xml_quantity'] - $product['erp_quantity']);
                                                @endphp
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-white">
                                                        {{ $key }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                                        @if($isRealMissing)
                                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                                Faltante
                                                            </span>
                                                        @else
                                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                                Cantidad menor
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                                        {{ $product['erp_quantity'] ?? '-' }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                                        {{ $isRealMissing ? '-' : $product['xml_quantity'] }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600 dark:text-red-400">
                                                        {{ $difference }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <i class="fas fa-check-circle text-green-500 text-4xl mb-3"></i>
                                    <p class="text-gray-600 dark:text-gray-300">No hay productos faltantes o con cantidad menor en el XML</p>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Pestaña de Productos Adicionales (modificada) -->
                        <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-700" id="extra" role="tabpanel" aria-labelledby="extra-tab">
                            @php
                                // Productos completamente adicionales (no existen en ERP)
                                $realExtra = $extraInXml;
                                
                                // Productos con cantidad mayor en XML (diferencia positiva significativa)
                                $quantityExtra = array_filter($quantityDifferences, function($diff) {
                                    return ($diff['xml_quantity'] - $diff['erp_quantity']) > 0;
                                });
                                
                                $allExtra = array_merge(
                                    $realExtra,
                                    array_keys($quantityExtra)
                                );
                            @endphp
                            
                            @if(count($allExtra))
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-100 dark:bg-gray-600">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Producto</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipo</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cantidad ERP</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cantidad XML</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Diferencia</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($allExtra as $key)
                                                @php
                                                    $product = collect($combinedProducts)->firstWhere('key', $key);
                                                    $isRealExtra = in_array($key, $realExtra);
                                                    $difference = $isRealExtra ? 
                                                        $product['xml_quantity'] : 
                                                        ($product['xml_quantity'] - $product['erp_quantity']);
                                                @endphp
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-white">
                                                        {{ $key }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                                        @if($isRealExtra)
                                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                                                Adicional completo
                                                            </span>
                                                        @else
                                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                                Cantidad mayor
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                                        {{ $isRealExtra ? '-' : $product['erp_quantity'] }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                                        {{ $product['xml_quantity'] ?? '-' }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-purple-600 dark:text-purple-400">
                                                        +{{ $difference }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <i class="fas fa-check-circle text-green-500 text-4xl mb-3"></i>
                                    <p class="text-gray-600 dark:text-gray-300">No hay productos adicionales o con cantidad mayor en el XML</p>
                                </div>
                            @endif
                        </div>

                        <!-- Pestaña de Diferencias en Cantidad -->
                        <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-700" id="quantity" role="tabpanel" aria-labelledby="quantity-tab">
                            @if(count($quantityDifferences))
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-100 dark:bg-gray-600">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Producto</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ERP</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">XML</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Diferencia</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Impacto</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($quantityDifferences as $key => $diff)
                                                @php
                                                    $product = collect($combinedProducts)->firstWhere('key', $key);
                                                    $impact = ($diff['xml_quantity'] - $diff['erp_quantity']) * $product['erp_unit'];
                                                    $impactClass = $impact > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400';
                                                    // Verificar si también tiene diferencia de precio
                                                    $hasPriceDiff = abs($product['xml_unit'] - $product['erp_unit']) > ($product['erp_unit'] * 0.01);
                                                @endphp
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-white">{{ $key }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">{{ $diff['erp_quantity'] }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">{{ $diff['xml_quantity'] }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $diff['xml_quantity'] > $diff['erp_quantity'] ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                                        {{ $diff['xml_quantity'] - $diff['erp_quantity'] }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $impactClass }}">
                                                        ${{ number_format(abs($impact), 2) }}
                                                        @if($hasPriceDiff)
                                                            <span class="text-blue-500 ml-2"><i class="fas fa-dollar-sign"></i> + Dif. Precio</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <i class="fas fa-check-circle text-green-500 text-4xl mb-3"></i>
                                    <p class="text-gray-600 dark:text-gray-300">No hay diferencias en cantidades entre el ERP y el XML</p>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Pestaña de Diferencias en Precio -->
                        <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-700" id="price" role="tabpanel" aria-labelledby="price-tab">
                            @php
                                $priceDifferences = array_filter($combinedProducts, function($prod) {
                                    return $prod['erp_unit'] !== null && 
                                           $prod['xml_unit'] !== null && 
                                           abs($prod['xml_unit'] - $prod['erp_unit']) > ($prod['erp_unit'] * 0.01);
                                });
                            @endphp
                            
                            @if(count($priceDifferences))
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-100 dark:bg-gray-600">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Producto</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ERP</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">XML</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Diferencia</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Impacto</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($priceDifferences as $prod)
                                                @php
                                                    $difference = $prod['xml_unit'] - $prod['erp_unit'];
                                                    $impact = $difference * $prod['xml_quantity'];
                                                    $statusClass = $difference < 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400';
                                                    $statusIcon = $difference < 0 ? 'fa-arrow-down' : 'fa-arrow-up';
                                                    // Verificar si también tiene diferencia de cantidad
                                                    $hasQuantityDiff = ($prod['xml_quantity'] - $prod['erp_quantity']) != 0;
                                                @endphp
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-white">{{ $prod['key'] }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">${{ number_format($prod['erp_unit'], 2) }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">${{ number_format($prod['xml_unit'], 2) }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $statusClass }}">
                                                        <i class="fas {{ $statusIcon }} mr-1"></i>
                                                        ${{ number_format(abs($difference), 2) }} ({{ number_format(abs($difference / $prod['erp_unit'] * 100), 2) }}%)
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $statusClass }}">
                                                        ${{ number_format(abs($impact), 2) }}
                                                        @if($hasQuantityDiff)
                                                            <span class="text-yellow-500 ml-2"><i class="fas fa-equals"></i> + Dif. Cantidad</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <i class="fas fa-check-circle text-green-500 text-4xl mb-3"></i>
                                    <p class="text-gray-600 dark:text-gray-300">No hay diferencias significativas en precios (mayores al 1%)</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts para la interactividad -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tabs de discrepancias
            const tabs = document.querySelectorAll('[data-tabs-target]');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const target = document.querySelector(this.getAttribute('data-tabs-target'));
                    
                    // Oculta todos los contenidos
                    document.querySelectorAll('#discrepancyContent > div').forEach(content => {
                        content.classList.add('hidden');
                    });
                    
                    // Muestra el contenido seleccionado
                    target.classList.remove('hidden');
                    
                    // Actualiza los estilos de las pestañas
                    tabs.forEach(t => {
                        t.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-500');
                        t.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
                    });
                    
                    this.classList.remove('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300', 'dark:hover:text-gray-300');
                    this.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-500');
                });
            });
            
            // Filtros de la tabla
            const filterStatus = document.getElementById('filterStatus');
            const searchProduct = document.getElementById('searchProduct');
            const toggleHideComplete = document.getElementById('toggleHideComplete');
            
            function filterTable() {
                const statusFilter = filterStatus.value;
                const searchTerm = searchProduct.value.toLowerCase();
                const hideComplete = toggleHideComplete.checked;
                
                document.querySelectorAll('tbody tr').forEach(row => {
                    const statuses = row.getAttribute('data-status').split(' ');
                    const product = row.getAttribute('data-product').toLowerCase();
                    const erpQuantity = parseFloat(row.getAttribute('data-erp-quantity'));
                    
                    // Aplicar filtros
                    const statusMatch = statusFilter === 'all' || 
                                      (statusFilter === 'match' && statuses.length === 0) ||
                                      statuses.includes(statusFilter);
                    const searchMatch = product.includes(searchTerm);
                    const completeMatch = !hideComplete || (hideComplete && erpQuantity !== 0);
                    
                    if (statusMatch && searchMatch && completeMatch) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                });
            }
            
            // Event listeners para los filtros
            filterStatus.addEventListener('change', filterTable);
            searchProduct.addEventListener('input', filterTable);
            toggleHideComplete.addEventListener('change', filterTable);
            
            // Activar la primera pestaña
            if(tabs.length > 0) {
                tabs[0].click();
            }
            
            // Aplicar filtros al cargar
            filterTable();
        });
    </script>
</x-app-layout>