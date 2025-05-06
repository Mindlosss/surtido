<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 leading-tight flex items-center justify-between">
            {{ $conteo->nombre }}
            <div class="flex items-center space-x-4">
                <link href="{{ asset('css/estilos.css') }}" rel="stylesheet">
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            </div>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-3xl font-bold">Productos a Recontar</h1>
                        <a href="{{ route('inv') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Regresar
                        </a>
                    </div>
                    <div id="segundo-conteo-grid" class="grid gap-8 mt-6">
                        @foreach ($conteo->conteoAnaqueles->groupBy('ubicacion.nombre') as $ubicacion => $anaqueles)
                            <div class="bg-gray-700 rounded-lg shadow-md p-6">
                                <h5 class="text-xl font-semibold text-gray-200 mb-4 cursor-pointer toggle-ubicacion flex justify-between items-center">
                                    <span>Ubicación: {{ $ubicacion }}</span>
                                    <svg class="w-6 h-6 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </h5>
                                <div class="anaqueles-container hidden">
                                    @foreach ($anaqueles->groupBy('anaquel') as $anaquel => $productos)
                                        <div class="bg-gray-800 rounded-lg shadow p-4 mt-4">
                                            <h6 class="text-lg font-semibold text-gray-300 mb-3 cursor-pointer toggle-anaquel flex justify-between items-center">
                                                <span>Anaquel: {{ $anaquel }}</span>
                                                <svg class="w-5 h-5 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </h6>
                                            <div class="productos-container hidden grid gap-4">
                                                @foreach ($productos as $producto)
                                                    <div class="bg-gray-900 rounded-lg shadow flex flex-col sm:flex-row items-center p-4 space-y-4 sm:space-y-0 sm:space-x-6 producto-card" data-anaquel-id="{{ $producto->id }}" data-producto-sku="{{ $producto->sku }}" style="background-color: {{ $producto->segundo_conteo == 2 ? '#2d3748' : '#2c303f' }};">
                                                        <!-- Imagen del Producto -->
                                                        <img src="https://infocontinenteferretero.com/imagenes_2022/{{ urlencode(trim($producto->sku)) }}.jpg" alt="Producto {{ $producto->sku }}" class="w-24 h-24 object-cover rounded-lg flex-shrink-0">
                                                        
                                                        <!-- Detalles del Producto -->
                                                        <div class="flex-1">
                                                            <p class="text-lg font-semibold text-gray-100">Código: {{ $producto->sku }}</p>
                                                        </div>
                                                        
                                                        <!-- Acción -->
                                                        <div class="flex items-center space-x-2">
                                                            @if($producto->segundo_conteo == 2)
                                                                <p class="cantidad-guardada text-sm font-semibold text-gray-200">Cantidad: {{ $producto->cantidad2 }}</p>
                                                                <button class="text-yellow-500 hover:text-yellow-400 transition btn-editar">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                            @else
                                                                <input type="number" class="form-input w-20 p-2 rounded-md bg-gray-700 text-white placeholder-gray-400 cantidad-input" placeholder="Cantidad">
                                                                <button class="bg-blue-600 text-white py-1 px-3 rounded-lg hover:bg-blue-500 transition btn-guardar">Guardar</button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Función para alternar la visibilidad y rotar el ícono
        function toggleVisibility(trigger, container, icon) {
            trigger.addEventListener('click', function() {
                container.classList.toggle('hidden');
                icon.classList.toggle('rotate-180');
            });
        }

        // Alternar visibilidad de ubicaciones
        document.querySelectorAll('.toggle-ubicacion').forEach(ubicacion => {
            const container = ubicacion.nextElementSibling;
            const icon = ubicacion.querySelector('svg');
            toggleVisibility(ubicacion, container, icon);
        });

        // Alternar visibilidad de anaqueles
        document.querySelectorAll('.toggle-anaquel').forEach(anaquel => {
            const container = anaquel.nextElementSibling;
            const icon = anaquel.querySelector('svg');
            toggleVisibility(anaquel, container, icon);
        });

        // Guardar cantidad
        function agregarEventoGuardar() {
            document.querySelectorAll('.btn-guardar').forEach(btn => {
                btn.addEventListener('click', function() {
                    const cantidadInput = btn.previousElementSibling;
                    const cantidad = cantidadInput.value;
                    const productoCard = btn.closest('.producto-card');
                    const anaquelId = productoCard.dataset.anaquelId;
                    const productoSku = productoCard.dataset.productoSku;

                    Swal.fire({
                        title: '¿Guardar cantidad?',
                        html: `<p>Cantidad: <strong>${cantidad}</strong></p><p>Producto: <strong>${productoSku}</strong></p>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, guardar',
                        background: '#333',
                        color: '#fff',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`/guardar-cantidad/${anaquelId}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ cantidad })
                            }).then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Guardado!',
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false,
                                        background: '#333',
                                        color: '#fff',
                                    });
                                    // Actualizar la vista para mostrar el valor guardado
                                    productoCard.querySelector('.flex-1').insertAdjacentHTML('afterend', `
                                        <div class="flex items-center space-x-2">
                                            <p class="cantidad-guardada text-sm font-semibold text-gray-200">Cantidad: ${cantidad}</p>
                                            <button class="text-yellow-500 hover:text-yellow-400 transition btn-editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    `);
                                    // Remover los elementos de input y botón guardar
                                    cantidadInput.parentElement.remove();
                                    agregarEventoEditar();
                                } else {
                                    Swal.fire({ title: 'Error', icon: 'error', background: '#333', color: '#fff' });
                                }
                            }).catch(() => Swal.fire({ title: 'Error', icon: 'error', background: '#333', color: '#fff' }));
                        }
                    });
                });
            });
        }

        // Función para agregar evento a los botones de edición
        function agregarEventoEditar() {
            document.querySelectorAll('.btn-editar').forEach(btn => {
                btn.addEventListener('click', function() {
                    const productoCard = btn.closest('.producto-card');
                    const cantidadGuardada = productoCard.querySelector('.cantidad-guardada');
                    const cantidad = cantidadGuardada.textContent.split(': ')[1];

                    // Insertar input y botón guardar
                    cantidadGuardada.parentElement.innerHTML = `
                        <input type="number" class="form-input w-20 p-2 rounded-md bg-gray-700 text-white placeholder-gray-400 cantidad-input" value="${cantidad}">
                        <button class="bg-blue-600 text-white py-1 px-3 rounded-lg hover:bg-blue-500 transition btn-guardar">Guardar</button>
                    `;
                    // Remover el botón de editar
                    btn.remove();
                    agregarEventoGuardar();
                });
            });
        }

        // Inicializar eventos
        agregarEventoEditar();
        agregarEventoGuardar();
    });
</script>

<style>
    .hidden {
        display: none;
    }
    .cursor-pointer {
        cursor: pointer;
    }
    /* Rotación de íconos cuando están activos */
    .rotate-180 {
        transform: rotate(180deg);
    }
</style>
