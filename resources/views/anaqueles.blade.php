<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $conteo->nombre }} > {{ $ubicacion->nombre }}
            <link href="{{ asset('css/estilos.css') }}" rel="stylesheet">
            <!-- Incluye SweetAlert2 desde un CDN -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center">
                        <h1>Lista de Anaqueles</h1>
                        <a href="{{ route('conteos.ubicacion', ['id' => $conteo->id]) }}" class="btn-comun">Regresar</a>
                    </div>
                    <ul>
                        <div class="orders-container">
                            @forelse($anaqueles as $anaquel)
                                <div class="order cursor-pointer relative" data-anaquel="{{ $anaquel }}">
                                    <li class="flex justify-between items-center">
                                        <span>{{ $anaquel }}</span>
                                        <div class="toggle-zone flex items-center" data-toggle="productos-{{ $anaquel }}">
                                            <i class="fas fa-chevron-down"></i>
                                        </div>
                                    </li>
                                    <div class="productos-container hidden mt-2 ml-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg" id="productos-{{ $anaquel }}">
                                        @forelse ($productos[$anaquel] ?? [] as $producto)
                                            <div class="producto-item text-sm text-gray-700 dark:text-gray-300">
                                                <span class="font-semibold">- Producto:</span> {{ $producto->barcode }}, <span class="font-semibold">Cantidad:</span> {{ $producto->cantidad }}
                                            </div>
                                        @empty
                                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                                No se han contado productos en este anaquel.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            @empty
                                <li>No hay tonos disponibles.</li>
                            @endforelse
                        </div>
                    </ul>

                    <!-- Modal de escaneo -->
                    <div id="scan-modal" class="fixed inset-0 flex items-center justify-center hidden bg-black bg-opacity-50 z-50">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden w-2/2">
                            <div id="scan-modal-content" class="px-6 py-4">
                                <div class="flex justify-between items-center">
                                    <h2 id="anaquel-header" class="text-lg font-medium text-gray-800 dark:text-gray-200"></h2>
                                    <button id="close-scan-modal" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">&times;</button>
                                </div>
                                <h3 class="text-md font-medium text-gray-600 dark:text-gray-300 mt-4">Escanear Producto</h3>
                                <form id="scan-form" action="{{ route('conteos.storeAnaquel', ['conteo_id' => $conteo->id]) }}" method="POST">
                                    @csrf
                                    <div class="mt-4">
                                        <label for="barcode" class="block text-sm font-medium text-white-700">Barcode</label>
                                        <div class="mt-1 flex">
                                            <input type="text" name="barcode" id="barcode" class="block w-full text-white bg-gray-700" required>
                                            <button type="button" id="buscar-producto" class="btn-comun ml-2">Buscar</button>
                                            <button type="button" id="limpiar-barcode" class="btn-eliminar ml-2 hidden">Limpiar</button>
                                        </div>
                                    </div>
                                    <div id="producto-detalles" class="hidden">
                                        <div class="mt-4" style="max-width: 300px;">
                                            <span id="sku_producto" class="block text-sm font-medium text-white-700"></span>
                                            <span id="nombre_producto" class="block text-sm font-medium text-white-700"></span>
                                        </div>
                                        <div class="mt-4">
                                            <img id="imagen_producto" src="" alt="Imagen del Producto" class="product-image mx-auto" onerror="this.onerror=null; this.src='{{ asset('images/herramientas.png') }}'">
                                        </div>
                                        <div class="mt-4 flex items-center space-x-2">
                                            <button type="button" id="open-calculator" class="text-white bg-gray-600 hover:bg-gray-500 rounded-l px-4 py-2">
                                                <i class="fas fa-calculator"></i>
                                            </button>
                                            <input type="number" name="cantidad" id="cantidad" class="mt-1 block w-full text-white bg-gray-700 rounded-r" required>
                                        </div>
                                    </div>
                                    <div id="producto-no-encontrado" class="hidden mt-4 font-medium">
                                        Producto no encontrado.
                                        <button id="add-product-btn" class="btn-aceptar mt-2">Añadir</button>
                                    </div>
                                    <input type="hidden" name="anaquel" id="anaquel" value="">
                                    <input type="hidden" name="ubicacion_id" id="ubicacion_id" value="{{ $ubicacion_id }}">
                                    <div class="mt-6 flex justify-center space-x-4">
                                        <button type="button" id="cancel-scan" class="btn-eliminar">Cancelar</button>
                                        <button type="button" id="siguiente-btn" class="btn-aceptar" disabled>Siguiente</button>
                                        <button type="submit" id="guardar-btn" class="btn-aceptar" disabled>Guardar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal de añadir producto -->
                    <div id="add-product-modal" class="fixed inset-0 flex items-center justify-center hidden bg-black bg-opacity-50 z-50">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden w-2/2">
                            <div class="px-6 py-4">
                                <div class="flex justify-between items-center">
                                    <h2 class="text-lg font-medium text-gray-800 dark:text-gray-200">Añadir Producto</h2>
                                    <button id="close-add-product-modal" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">&times;</button>
                                </div>
                                <form id="add-product-form" method="POST" action="{{ route('productos.storeBarcode') }}">
                                    @csrf
                                    <div class="mt-4">
                                        <label for="sku" class="block text-sm font-medium text-white-700">SKU</label>
                                        <input type="text" name="sku" id="sku" class="mt-1 block w-full text-white bg-gray-700" required>
                                    </div>
                                    <div class="mt-4">
                                        <label for="new-barcode" class="block text-sm font-medium text-white-700">Código de Barras</label>
                                        <input type="text" name="new-barcode" id="new-barcode" class="mt-1 block w-full text-white bg-gray-700" readonly>
                                    </div>
                                    <div class="mt-6 flex justify-center space-x-4">
                                        <button type="button" id="cancel-add-product" class="btn-eliminar">Cancelar</button>
                                        <button type="submit" class="btn-aceptar">Guardar</button>
                                    </div>
                                </form>
                                
                            </div>
                        </div>
                    </div>



                    <!-- Modal de calculadora -->
                    <div id="calculator-modal" class="fixed inset-0 flex items-center justify-center hidden bg-black bg-opacity-50 z-50">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden w-72">
                            <div class="px-6 py-4">
                                <div class="flex justify-between items-center">
                                    <h2 class="text-lg font-medium text-gray-800 dark:text-gray-200">Calculadora</h2>
                                    <button id="close-calculator-modal" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">&times;</button>
                                </div>
                                <div class="mt-4">
                                    <input type="text" id="calculator-display" class="w-full text-white bg-gray-700 text-right px-4 py-2 rounded" readonly>
                                </div>
                                <div class="grid grid-cols-4 gap-2 mt-4">
                                    <button class="calculator-btn bg-gray-500 text-white" data-value="7">7</button>
                                    <button class="calculator-btn bg-gray-500 text-white" data-value="8">8</button>
                                    <button class="calculator-btn bg-gray-500 text-white" data-value="9">9</button>
                                    <button class="calculator-btn bg-gray-600 text-white" data-value="/">/</button>
                                    <button class="calculator-btn bg-gray-500 text-white" data-value="4">4</button>
                                    <button class="calculator-btn bg-gray-500 text-white" data-value="5">5</button>
                                    <button class="calculator-btn bg-gray-500 text-white" data-value="6">6</button>
                                    <button class="calculator-btn bg-gray-600 text-white" data-value="*">*</button>
                                    <button class="calculator-btn bg-gray-500 text-white" data-value="1">1</button>
                                    <button class="calculator-btn bg-gray-500 text-white" data-value="2">2</button>
                                    <button class="calculator-btn bg-gray-500 text-white" data-value="3">3</button>
                                    <button class="calculator-btn bg-gray-600 text-white" data-value="-">-</button>
                                    <button class="calculator-btn bg-gray-500 text-white" data-value="0">0</button>
                                    <button class="calculator-btn bg-gray-500 text-white" data-value=".">.</button>
                                    <button class="calculator-btn bg-red-500 text-white" data-value="C">C</button>
                                    <button class="calculator-btn bg-gray-600 text-white" data-value="+">+</button>
                                    <button class="calculator-btn col-span-3 bg-green-600 text-white" data-value="=">=</button>
                                    <button class="calculator-btn bg-red-500 text-white" id="calculator-backspace">←</button>
                                </div>
                                <div class="mt-4">
                                    <button id="use-calculator-result" class="w-full bg-blue-500 text-white py-2 rounded">Usar resultado</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const anaquelRows = document.querySelectorAll('.order');
    const scanModal = document.getElementById('scan-modal');
    const closeScanModal = document.getElementById('close-scan-modal');
    const cancelScan = document.getElementById('cancel-scan');
    const anaquelInput = document.getElementById('anaquel');
    const anaquelHeader = document.getElementById('anaquel-header');
    const barcodeInput = document.getElementById('barcode');
    const cantidadInput = document.getElementById('cantidad');
    const productoDetalles = document.getElementById('producto-detalles');
    const nombreProductoSpan = document.getElementById('nombre_producto');
    const skuProductoSpan = document.getElementById('sku_producto');
    const imagenProducto = document.getElementById('imagen_producto');
    const productoNoEncontrado = document.getElementById('producto-no-encontrado');
    const guardarBtn = document.getElementById('guardar-btn');
    const siguienteBtn = document.getElementById('siguiente-btn');
    const scanForm = document.getElementById('scan-form');
    const buscarProductoBtn = document.getElementById('buscar-producto');
    const limpiarBarcodeBtn = document.getElementById('limpiar-barcode');

    const addProductBtn = document.getElementById('add-product-btn');
    const addProductModal = document.getElementById('add-product-modal');
    const closeAddProductModal = document.getElementById('close-add-product-modal');
    const cancelAddProduct = document.getElementById('cancel-add-product');
    const skuInput = document.getElementById('sku');
    const newBarcodeInput = document.getElementById('new-barcode');

    const calculatorModal = document.getElementById('calculator-modal');
    const closeCalculatorModal = document.getElementById('close-calculator-modal');
    const openCalculatorBtn = document.getElementById('open-calculator');
    const calculatorDisplay = document.getElementById('calculator-display');
    const calculatorBtns = document.querySelectorAll('.calculator-btn');
    const calculatorBackspace = document.getElementById('calculator-backspace');
    const useCalculatorResultBtn = document.getElementById('use-calculator-result');

    openCalculatorBtn.addEventListener('click', function() {
        calculatorModal.classList.remove('hidden');
        calculatorDisplay.value = cantidadInput.value || '0';
    });

    closeCalculatorModal.addEventListener('click', function() {
        calculatorModal.classList.add('hidden');
    });

    calculatorBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const value = this.getAttribute('data-value');
            if (value === 'C') {
                calculatorDisplay.value = '0';
            } else if (value === '=') {
                try {
                    calculatorDisplay.value = eval(calculatorDisplay.value) || '0';
                } catch {
                    calculatorDisplay.value = 'Error';
                }
            } else {
                if (calculatorDisplay.value === '0' || calculatorDisplay.value === 'Error') {
                    calculatorDisplay.value = value;
                } else {
                    calculatorDisplay.value += value;
                }
            }
        });
    });

    // Corregido: Manejo del botón de borrar para evitar "nul"
    calculatorBackspace.addEventListener('click', function() {
        if (calculatorDisplay.value && calculatorDisplay.value.length > 1 && calculatorDisplay.value !== 'Error') {
            calculatorDisplay.value = calculatorDisplay.value.slice(0, -1);
        } else {
            calculatorDisplay.value = '0';  // Si queda vacío después de borrar, establecer a '0'
        }
    });

    useCalculatorResultBtn.addEventListener('click', function() {
        cantidadInput.value = calculatorDisplay.value;
        calculatorModal.classList.add('hidden');
        checkGuardarBtnState();
    });

    const toggles = document.querySelectorAll('.toggle-zone');
    toggles.forEach(toggle => {
        toggle.addEventListener('click', function(event) {
            event.stopPropagation();
            const targetId = this.getAttribute('data-toggle');
            const target = document.getElementById(targetId);
            const icon = this.querySelector('i');
            if (target.classList.contains('hidden')) {
                target.classList.remove('hidden');
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                target.classList.add('hidden');
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        });
    });

    anaquelRows.forEach(row => {
        row.addEventListener('click', function(event) {
            if (!event.target.closest('.productos-container')) {
                const anaquel = this.getAttribute('data-anaquel');
                anaquelInput.value = anaquel;
                anaquelHeader.textContent = anaquel;
                scanModal.classList.remove('hidden');
                barcodeInput.focus();
                productoDetalles.classList.add('hidden');
                productoNoEncontrado.classList.add('hidden');
                guardarBtn.disabled = true;
                siguienteBtn.disabled = true;
                barcodeInput.value = '';
                cantidadInput.value = '';
            }
        });
    });

    closeScanModal.addEventListener('click', function() {
        scanModal.classList.add('hidden');
    });

    cancelScan.addEventListener('click', function() {
        scanModal.classList.add('hidden');
    });

    barcodeInput.addEventListener('change', buscarProducto);
    buscarProductoBtn.addEventListener('click', buscarProducto);

    function buscarProducto() {
        let barcode = barcodeInput.value;
        barcode = barcode.replace(/'/g, '-');
        fetch('{{ route('conteos.obtenerProducto') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ barcode: barcode })
        })
        .then(response => response.json())
        .then(data => {
            if (data) {
                nombreProductoSpan.textContent = 'DESCRIP: ' + data.DESCRIP;
                skuProductoSpan.textContent = 'SKU: ' + data.ARTICULO;
                const articuloTrimmed = data.ARTICULO.trim();
                imagenProducto.src = 'https://infocontinenteferretero.com/imagenes_2022/' + encodeURIComponent(articuloTrimmed) + '.jpg';
                productoDetalles.classList.remove('hidden');
                productoNoEncontrado.classList.add('hidden');
                limpiarBarcodeBtn.classList.add('hidden');
            } else {
                nombreProductoSpan.textContent = '';
                skuProductoSpan.textContent = '';
                imagenProducto.src = '{{ asset('images/herramientas.png') }}';
                productoDetalles.classList.add('hidden');
                productoNoEncontrado.classList.remove('hidden');
                limpiarBarcodeBtn.classList.remove('hidden');
            }
            checkGuardarBtnState();
        })
        .catch(error => {
            console.error('Error:', error);
            nombreProductoSpan.textContent = '';
            skuProductoSpan.textContent = '';
            imagenProducto.src = '{{ asset('images/herramientas.png') }}';
            productoDetalles.classList.add('hidden');
            productoNoEncontrado.classList.remove('hidden');
            limpiarBarcodeBtn.classList.remove('hidden');
            checkGuardarBtnState();
        });
    }

    limpiarBarcodeBtn.addEventListener('click', function() {
        barcodeInput.value = '';
        limpiarBarcodeBtn.classList.add('hidden');
        productoNoEncontrado.classList.add('hidden');
        barcodeInput.focus();
    });

    barcodeInput.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            buscarProducto();
            barcodeInput.blur();
        }
    });

    cantidadInput.addEventListener('input', checkGuardarBtnState);

    function checkGuardarBtnState() {
        const barcodeNotEmpty = barcodeInput.value.trim() !== '';
        const cantidadNotEmpty = cantidadInput.value.trim() !== '';
        guardarBtn.disabled = !barcodeNotEmpty || !cantidadNotEmpty || !nombreProductoSpan.textContent;
        siguienteBtn.disabled = guardarBtn.disabled;
    }

    function guardarProducto(operation) {
        return fetch(scanForm.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                barcode: barcodeInput.value,
                cantidad: cantidadInput.value,
                anaquel: anaquelInput.value,
                ubicacion_id: document.getElementById('ubicacion_id').value,
                operation: operation
            })
        })
        .then(response => response.json());
    }

    function confirmarOperacionYGuardarProducto(closeModal) {
    mostrarConfirmacionCarga();
    guardarProducto('check')
        .then(data => {
            ocultarConfirmacionCarga();
            if (data.success) {
                if (data.exists) {
                    const conteoAnaquel = data.conteoAnaquel;
                    return Swal.fire({
                        title: 'Producto ya registrado',
                        html: `<p>SKU: ${conteoAnaquel.sku}</p>
                               <p>Barcode: ${conteoAnaquel.barcode}</p>
                               <p>Cantidad: ${conteoAnaquel.cantidad}</p>
                               <p>¿Deseas actualizar la cantidad o sumarla?</p>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Actualizar',
                        cancelButtonText: 'Sumar',
                        background: '#333',
                        color: '#fff',
                        iconColor: '#a5dc86',
                        allowOutsideClick: false
                    }).then((result) => {
                        const operation = result.isConfirmed ? 'update' : 'sum';
                        return guardarProducto(operation);
                    });
                }
                return guardarProducto('insert');
            } else {
                throw new Error(data.message || 'Error al guardar el producto');
            }
        })
        .then(result => {
            if (result && result.success) {
                Swal.fire({
                    title: 'Guardado!',
                    text: result.message,
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    background: '#333',
                    color: '#fff',
                    iconColor: '#a5dc86'
                }).then(() => {
                    if (closeModal) {
                        scanModal.classList.add('hidden');
                    } else {
                        resetForm();
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error al guardar el producto:', error);
            ocultarConfirmacionCarga();
            Swal.fire({
                title: 'Error!',
                text: 'Hubo un problema al guardar el producto.',
                icon: 'error',
                timer: 1500,
                showConfirmButton: false,
                background: '#333',
                color: '#fff',
                iconColor: '#f27474'
            });
            // Agregar un log detallado cuando falla
            console.log('Error detallado:', {
                barcode: barcodeInput.value,
                cantidad: cantidadInput.value,
                anaquel: anaquelInput.value,
                ubicacion_id: document.getElementById('ubicacion_id').value,
                error_message: error.message,
            });
        });
}


    siguienteBtn.addEventListener('click', function() {
        if (!siguienteBtn.disabled) {
            confirmarOperacionYGuardarProducto(false);
        }
    });

    guardarBtn.addEventListener('click', function(event) {
        event.preventDefault();
        if (!guardarBtn.disabled) {
            confirmarOperacionYGuardarProducto(true);
        }
    });

    function mostrarConfirmacionCarga() {
        Swal.fire({
            title: 'Guardando...',
            text: 'Por favor espera',
            allowOutsideClick: false,
            showConfirmButton: false,
            background: '#333',
            color: '#fff',
            willOpen: () => {
                Swal.showLoading();
            }
        });
    }

    function ocultarConfirmacionCarga() {
        Swal.close();
    }

    function resetForm() {
        barcodeInput.value = '';
        cantidadInput.value = '';
        productoDetalles.classList.add('hidden');
        productoNoEncontrado.classList.add('hidden');
        guardarBtn.disabled = true;
        siguienteBtn.disabled = true;
        barcodeInput.focus();
    }

    addProductBtn.addEventListener('click', function() {
        addProductModal.classList.remove('hidden');
        newBarcodeInput.value = barcodeInput.value;
        skuInput.focus();
    });

    closeAddProductModal.addEventListener('click', function() {
        addProductModal.classList.add('hidden');
    });

    cancelAddProduct.addEventListener('click', function() {
        addProductModal.classList.add('hidden');
    });

    const addProductForm = document.getElementById('add-product-form');
    
    addProductForm.addEventListener('submit', function(event) {
        event.preventDefault();
        validarSkuAntesDeGuardar();
    });

    function validarSkuAntesDeGuardar() {
        const sku = skuInput.value.trim();

        if (sku === '') {
            alert('Por favor, ingrese un SKU.');
            return;
        }

        fetch('{{ route('conteos.validarSku') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ sku: sku })
        })
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                addProductForm.submit();
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: 'El SKU no existe en la base de datos.',
                    icon: 'error',
                    background: '#333',
                    color: '#fff',
                    iconColor: '#f27474'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Hubo un problema al validar el SKU.',
                icon: 'error',
                background: '#333',
                color: '#fff',
                iconColor: '#f27474'
            });
        });
    }

    // SweetAlert2 para mensajes de éxito y error
    @if(session('success'))
        Swal.fire({
            title: '¡Producto añadido!',
            html: `<p>SKU: {{ session('success')['sku'] }}</p>
                   <p>Código de Barras: {{ session('success')['barcode'] }}</p>`,
            icon: 'success',
            background: '#333',
            color: '#fff',
            iconColor: '#a5dc86',
            confirmButtonText: 'Aceptar'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            title: 'Error!',
            text: '{{ session('error') }}',
            icon: 'error',
            background: '#333',
            color: '#fff',
            iconColor: '#f27474'
        });
    @endif
});

</script>
