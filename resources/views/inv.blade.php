<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('CONTEO DE INVENTARIO') }}
            <link href="{{ asset('css/estilos.css') }}" rel="stylesheet">
            <!-- Incluye SweetAlert2 desde un CDN -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </h2>
    </x-slot>

    <div class="py-7">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-5 text-gray-900 dark:text-gray-100">
                    <!-- Grid of conteo cards -->
                    <div id="conteo-grid" class="grid">
                        <div class="conteo-card" id="nuevo-conteo">
                            <div class="conteo-card-content">
                                <span class="plus-sign">+</span>
                            </div>
                        </div>

                        @foreach ($conteos as $conteo)
                            @php
                                $hasSegundoConteo = $conteo->conteoAnaqueles->whereIn('segundo_conteo', [1, 2])->count() > 0;
                            @endphp
                            <div class="conteo-card-wrapper {{ $hasSegundoConteo ? 'con-segundo-conteo' : '' }}">
                                <div class="conteo-card {{ $hasSegundoConteo ? 'tres-cuartos' : 'completo' }}" id="conteo-{{ $conteo->id }}">
                                    <div class="conteo-card-content">
                                        <p>{{ $conteo->nombre }}</p>
                                        <p>Fecha: {{ \Carbon\Carbon::parse($conteo->fecha_hora)->format('d/m/y H:i') }}</p>
                                    </div>
                                </div>
                                @if ($hasSegundoConteo)
                                    <div class="conteo-card segundo-conteo-card" id="segundo-conteo-{{ $conteo->id }}">
                                        <div class="conteo-card-content">
                                            <p>Segundo Conteo</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Modal de creación -->
                    <div id="create-modal" class="fixed inset-0 flex items-center justify-center hidden bg-black bg-opacity-50">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden w-2/2">
                            <div id="create-modal-content" class="px-6 py-4">
                                <div class="flex justify-between items-center">
                                    <h2 class="text-lg font-medium text-white-800 dark:text-white-200">Nuevo conteo</h2>
                                    <button id="close-modal" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">&times;</button>
                                </div>
                                <form id="create-form" action="{{ route('conteos.store') }}" method="POST">
                                    @csrf
                                    <div class="mt-4">
                                        <label for="nombre" class="block text-sm font-medium text-white-700">Nombre del Conteo</label>
                                        <input type="text" name="nombre" id="nombre" class="mt-1 block w-full text-black" required>
                                    </div>
                                    <div class="mt-4">
                                        <label for="fecha_hora" class="block text-sm font-medium text-white-700">Fecha y Hora</label>
                                        <input type="datetime-local" name="fecha_hora" id="fecha_hora" class="mt-1 block w-full text-black" required>
                                    </div>
                                    <div class="mt-6 flex justify-center space-x-4">
                                        <button type="button" id="cancel-create" class="btn-eliminar">Cancelar</button>
                                        <button type="submit" class="btn-aceptar">Guardar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-center mt-6 space-x-4">
                        <button id="aceptar-btn" class="btn-aceptar" disabled>Aceptar</button>
                        <button id="eliminar-btn" class="btn-eliminar" disabled>Eliminar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.conteo-card');
        const aceptarBtn = document.getElementById('aceptar-btn');
        const eliminarBtn = document.getElementById('eliminar-btn');
        const createModal = document.getElementById('create-modal');
        const closeModal = document.getElementById('close-modal');
        const cancelCreateBtn = document.getElementById('cancel-create');
        const createForm = document.getElementById('create-form');
        let selectedCard = null;

        cards.forEach(card => {
            card.addEventListener('click', function() {
                if (selectedCard) {
                    selectedCard.classList.remove('selected');
                }
                if (selectedCard !== this) {
                    this.classList.add('selected');
                    selectedCard = this;
                    aceptarBtn.disabled = false;
                    eliminarBtn.disabled = this.id == 'nuevo-conteo';
                } else {
                    selectedCard = null;
                    aceptarBtn.disabled = true;
                    eliminarBtn.disabled = true;
                }
            });
        });

        aceptarBtn.addEventListener('click', function() {
            if (selectedCard) {
                if (selectedCard.id === 'nuevo-conteo') {
                    createModal.classList.remove('hidden');
                } else if (selectedCard.classList.contains('segundo-conteo-card')) {
                    const conteoId = selectedCard.id.replace('segundo-conteo-', '');
                    window.location.href = `/conteos/${conteoId}/segundo_conteo`;
                } else {
                    const conteoId = selectedCard.id.replace('conteo-', '');
                    window.location.href = `/conteos/${conteoId}/ubicacion`;
                }
            }
        });

        closeModal.addEventListener('click', function() {
            createModal.classList.add('hidden');
        });

        cancelCreateBtn.addEventListener('click', function() {
            createModal.classList.add('hidden');
        });

        eliminarBtn.addEventListener('click', function() {
            if (selectedCard && selectedCard.id !== 'nuevo-conteo') {
                const conteoId = selectedCard.id.replace('conteo-', '');
                const conteoNombre = selectedCard.querySelector('p:nth-child(1)').innerText;
                const conteoFecha = selectedCard.querySelector('p:nth-child(2)').innerText;

                Swal.fire({
                    title: '¿Estás seguro?',
                    html: `<p>Vas a eliminar el conteo: <strong>${conteoNombre}</strong></p><p>Fecha: ${conteoFecha}</p>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminarlo!',
                    cancelButtonText: 'Cancelar',
                    background: '#333',
                    color: '#fff',
                    iconColor: '#f8bb86'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const url = `/conteos/${conteoId}`;
                        fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        }).then(response => {
                            return response.json();
                        }).then(data => {
                            if (data.success) {
                                selectedCard.parentElement.remove();
                                selectedCard = null;
                                aceptarBtn.disabled = true;
                                eliminarBtn.disabled = true;
                                Swal.fire({
                                    title: 'Eliminado!',
                                    text: 'El conteo y todos sus registros asociados han sido eliminados.',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false,
                                    background: '#333',
                                    color: '#fff',
                                    iconColor: '#a5dc86'
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Hubo un problema al eliminar el conteo.',
                                    icon: 'error',
                                    timer: 1500,
                                    showConfirmButton: false,
                                    background: '#333',
                                    color: '#fff',
                                    iconColor: '#f27474'
                                });
                            }
                        }).catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Hubo un problema al eliminar el conteo.',
                                icon: 'error',
                                timer: 1500,
                                showConfirmButton: false,
                                background: '#333',
                                color: '#fff',
                                iconColor: '#f27474'
                            });
                        });
                    }
                });
            }
        });

        @if (session('success'))
        Swal.fire({
            title: 'Creado!',
            text: '{{ session('success') }}',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false,
            background: '#333',
            color: '#fff',
            iconColor: '#a5dc86'
        });
        @endif
    });
</script>
