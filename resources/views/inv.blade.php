<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 leading-tight">
                <i class="fas fa-clipboard-list mr-2"></i>CONTEO DE INVENTARIO
            </h2>
        </div>
    </x-slot>

    <div class="py-8 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Card principal -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border-l-4 border-blue-500">
                <div class="p-6">
                    <!-- Grid de conteos -->
                    <div id="conteo-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <!-- Card para nuevo conteo -->
                        <div 
                            id="nuevo-conteo" 
                            class="conteo-card bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 border-2 border-dashed border-blue-300 dark:border-blue-600 hover:border-blue-500 transition-all duration-200"
                        >
                            <div class="conteo-card-content flex flex-col items-center justify-center h-full p-4">
                                <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-700 flex items-center justify-center mb-2">
                                    <i class="fas fa-plus text-blue-600 dark:text-blue-300 text-xl"></i>
                                </div>
                                <span class="text-blue-600 dark:text-blue-300 font-medium">Nuevo Conteo</span>
                            </div>
                        </div>

                        @foreach ($conteos as $conteo)
                            @php
                                $hasSegundoConteo = $conteo->conteoAnaqueles->whereIn('segundo_conteo', [1, 2])->count() > 0;
                                $cardClass = $hasSegundoConteo ? 'w-3/4' : 'w-full';
                            @endphp
                            
                            <div class="relative conteo-card-wrapper group">
                                <!-- Card principal del conteo -->
                                <div 
                                    id="conteo-{{ $conteo->id}}"
                                    class="conteo-card bg-white dark:bg-gray-700 rounded-lg shadow-md overflow-hidden border-l-4 border-blue-500 hover:shadow-lg transition-shadow duration-200 {{ $cardClass }} h-full"
                                >
                                    <div class="conteo-card-content p-4 flex flex-col h-full">
                                        <div class="flex justify-between items-start mb-2">
                                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $conteo->nombre }}</h3>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-600 px-2 py-1 rounded-full">
                                                {{ \Carbon\Carbon::parse($conteo->fecha_hora)->format('d/m/y') }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                                            <i class="far fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($conteo->fecha_hora)->format('H:i') }}
                                        </p>
                                        <div class="mt-auto flex justify-between items-center">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $conteo->conteoAnaqueles->count() }} registros
                                            </span>
                                            @if($hasSegundoConteo)
                                                <span class="text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded-full">
                                                    2° conteo
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Card de segundo conteo (si existe) -->
                                @if ($hasSegundoConteo)
                                    <div 
                                        id="segundo-conteo-{{ $conteo->id}}"
                                        class="conteo-card absolute top-0 right-0 w-1/4 h-full bg-green-100 dark:bg-green-900 border-l-2 border-green-300 dark:border-green-600 rounded-r-lg overflow-hidden opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                    >
                                        <div class="conteo-card-content flex items-center justify-center h-full">
                                            <span class="text-green-800 dark:text-green-200 text-sm font-medium rotate-90 whitespace-nowrap">
                                                2° Conteo
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Modal de creación -->
                    <div id="create-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black bg-opacity-50">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl overflow-hidden w-full max-w-md mx-4">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">
                                        <i class="fas fa-plus-circle mr-2 text-blue-500"></i>Nuevo conteo
                                    </h2>
                                    <button id="close-modal" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 text-2xl">&times;</button>
                                </div>
                                
                                <form id="create-form" action="{{ route('conteos.store') }}" method="POST">
                                    @csrf
                                    <div class="space-y-4">
                                        <div>
                                            <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Nombre del Conteo
                                            </label>
                                            <input 
                                                type="text" 
                                                name="nombre" 
                                                id="nombre" 
                                                class="w-full p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                required
                                            >
                                        </div>
                                        
                                        <div>
                                            <label for="fecha_hora" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Fecha y Hora
                                            </label>
                                            <input 
                                                type="datetime-local" 
                                                name="fecha_hora" 
                                                id="fecha_hora" 
                                                class="w-full p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                required
                                            >
                                        </div>
                                    </div>
                                    
                                    <div class="mt-6 flex justify-end space-x-3">
                                        <button 
                                            type="button" 
                                            id="cancel-create" 
                                            class="px-4 py-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 rounded-lg transition-colors duration-200"
                                        >
                                            Cancelar
                                        </button>
                                        <button 
                                            type="submit" 
                                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 flex items-center"
                                        >
                                            <i class="fas fa-save mr-2"></i> Guardar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex justify-center mt-8 space-x-4">
                        <button 
                            id="aceptar-btn" 
                            disabled
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <i class="fas fa-check-circle mr-2"></i> Aceptar
                        </button>
                        <button 
                            id="eliminar-btn" 
                            disabled
                            class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200 flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <i class="fas fa-trash-alt mr-2"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.conteo-card:not(#nuevo-conteo)');
            const nuevoConteoCard = document.getElementById('nuevo-conteo');
            const aceptarBtn = document.getElementById('aceptar-btn');
            const eliminarBtn = document.getElementById('eliminar-btn');
            const createModal = document.getElementById('create-modal');
            const closeModal = document.getElementById('close-modal');
            const cancelCreateBtn = document.getElementById('cancel-create');
            const createForm = document.getElementById('create-form');
            let selectedCard = null;

            // Selección de cards
            function selectCard(card) {
                if (selectedCard) {
                    selectedCard.classList.remove('ring-2', 'ring-blue-500', 'shadow-lg');
                }
                
                selectedCard = card;
                card.classList.add('ring-2', 'ring-blue-500', 'shadow-lg');
                
                aceptarBtn.disabled = false;
                eliminarBtn.disabled = card === nuevoConteoCard;
            }

            // Evento para seleccionar cards normales
            cards.forEach(card => {
                card.addEventListener('click', function() {
                    selectCard(this);
                });
            });

            // Evento para el card de nuevo conteo
            nuevoConteoCard.addEventListener('click', function() {
                selectCard(this);
            });

            // Botón Aceptar
            aceptarBtn.addEventListener('click', function() {
                if (!selectedCard) return;

                if (selectedCard === nuevoConteoCard) {
                    createModal.classList.remove('hidden');
                } else if (selectedCard.id.startsWith('segundo-conteo-')) {
                    const conteoId = selectedCard.id.replace('segundo-conteo-', '');
                    window.location.href = `/conteos/${conteoId}/segundo_conteo`;
                } else {
                    const conteoId = selectedCard.id.replace('conteo-', '');
                    window.location.href = `/conteos/${conteoId}/ubicacion`;
                }
            });

            // Cerrar modal
            closeModal.addEventListener('click', function() {
                createModal.classList.add('hidden');
            });

            cancelCreateBtn.addEventListener('click', function() {
                createModal.classList.add('hidden');
            });

            // Botón Eliminar
            eliminarBtn.addEventListener('click', function() {
                if (!selectedCard || selectedCard === nuevoConteoCard) return;

                const conteoId = selectedCard.id.replace('conteo-', '');
                const conteoNombre = selectedCard.querySelector('h3').innerText;
                const conteoFecha = selectedCard.querySelector('span:nth-child(2)').innerText;

                Swal.fire({
                    title: '¿Eliminar este conteo?',
                    html: `<div class="text-left">
                             <p class="font-medium">${conteoNombre}</p>
                             <p class="text-sm text-gray-600 dark:text-gray-300">${conteoFecha}</p>
                           </div>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    background: '#1e1e2f',
                    color: '#fff',
                    iconColor: '#f8bb86'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/conteos/${conteoId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                selectedCard.parentElement.remove();
                                selectedCard = null;
                                aceptarBtn.disabled = true;
                                eliminarBtn.disabled = true;
                                
                                Swal.fire({
                                    title: '¡Eliminado!',
                                    text: 'El conteo ha sido eliminado.',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false,
                                    background: '#1e1e2f',
                                    color: '#fff',
                                    iconColor: '#a5dc86'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Error',
                                text: 'Ocurrió un error al eliminar el conteo',
                                icon: 'error',
                                background: '#1e1e2f',
                                color: '#fff',
                                iconColor: '#f27474'
                            });
                        });
                    }
                });
            });

            // Mostrar notificación si hay mensaje de éxito
            @if (session('success'))
                Swal.fire({
                    title: '¡Éxito!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                    background: '#1e1e2f',
                    color: '#fff',
                    iconColor: '#a5dc86'
                });
            @endif
        });
    </script>
</x-app-layout>