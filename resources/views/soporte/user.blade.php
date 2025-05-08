<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 leading-tight">Mis Tickets</h2>
            <button id="btn-create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Crear Ticket</button>
        </div>
    </x-slot>

    <div class="py-7">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <form method="GET" action="{{ route('soporte') }}" class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
                    <input
                        name="search"
                        value="{{ request('search') }}"
                        type="text"
                        placeholder="Buscar mis tickets..."
                        class="w-full sm:w-64 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 placeholder-gray-500 dark:placeholder-gray-400 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4 sm:mb-0"/>
                    <div class="flex space-x-2">
                        <select
                            name="status"
                            class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos</option>
                            <option value="Abierto"{{ request('status')=='Abierto'?' selected':'' }}>Abierto</option>
                            <option value="En Progreso"{{ request('status')=='En Progreso'?' selected':'' }}>En Progreso</option>
                            <option value="Cerrado"{{ request('status')=='Cerrado'?' selected':'' }}>Cerrado</option>
                        </select>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">Filtrar</button>
                        <a href="{{ route('soporte') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg shadow">Limpiar</a>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table id="tickets-table" class="min-w-full table-auto dark:text-gray-200">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-left">ID</th>
                                <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-left">Asunto</th>
                                <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-left">Estado</th>
                                <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-left">Creado</th>
                                <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $t)
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">{{ $t->id }}</td>
                                    <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">{{ $t->asunto }}</td>
                                    <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">{{ $t->estado }}</td>
                                    <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">{{ $t->created_at->format('Y-m-d') }}</td>
                                    <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-center">
                                        <button type="button" class="text-blue-600 hover:underline btn-view" data-ticket='@json($t)'>Ver</button>
                                    </td>
                                </tr>
                            @endforeach
                            @if($tickets->isEmpty())
                                <tr><td colspan="5" class="text-center py-4">No tienes tickets.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Crear Ticket -->
        <div id="modal-create" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50" style="display:none;">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-11/12 max-w-md p-6 relative">
                <button data-close class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 text-2xl leading-none">&times;</button>
                <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Crear Nuevo Ticket</h3>
                <form method="POST" action="{{ route('soporte.tickets.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-gray-700 dark:text-gray-200">Área</label>
                        <select name="area" required
                                class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" disabled selected hidden>Selecciona un área</option>
                            <optgroup label="Área administrativa">
                                <option>Diseño</option>
                                <option>Recursos Humanos</option>
                                <option>Ecommerce</option>
                                <option>Contabilidad</option>
                                <option>Administración</option>
                                <option>Crédito y cobranza</option>
                                <option>Compras</option>
                            </optgroup>
                            <optgroup label="Sucursales">
                                <option>Makita</option>
                                <option>Milwaukee</option>
                                <option>California</option>
                                <option>Matriz</option>
                                <option>Multimarca</option>    
                                <option>Taller California</option>
                            </optgroup>
                            <optgroup label="Almacenes">
                                <option>Almacén</option>
                                <option>Bodega</option>
                            </optgroup>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-200">Asunto</label>
                        <select name="asunto" required
                                class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" disabled selected hidden>Selecciona un asunto</option>
                            <option label="INNOVASMART">INNOVASMART</option>
                            <option label="INNOVAD-IA">INNOVAD-IA</option>
                            <option>Internet y telefonía</option>
                            <option>Reportes</option>
                            <option>Hardware</option>
                            <option>Software</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-200">Descripción</label>
                        <textarea name="descripcion" placeholder="Detalles del ticket..." rows="3" required
                                  class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Guardar</button>
                        <button type="button" data-close class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg shadow">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Ver Ticket -->
        <div id="modal-view" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50" style="display:none;">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-11/12 max-w-md p-6 relative">
                <button data-close class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 text-2xl leading-none">&times;</button>
                <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Ticket #<span id="view-id"></span></h3>
                <div class="space-y-4">
                    <div>
                        <h4 class="font-medium text-gray-700 dark:text-gray-200">Área</h4>
                        <p id="view-area" class="text-gray-800 dark:text-gray-200"></p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700 dark:text-gray-200">Asunto</h4>
                        <p id="view-asunto" class="text-gray-800 dark:text-gray-200"></p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700 dark:text-gray-200">Descripción</h4>
                        <p id="view-descripcion" class="text-gray-800 dark:text-gray-200"></p>
                    </div>
                    <div class="flex justify-between">
                        <div>
                            <h4 class="font-medium text-gray-700 dark:text-gray-200">Estado</h4>
                            <span id="view-estado" class="inline-block px-2 py-1 text-sm font-semibold rounded-full"></span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-700 dark:text-gray-200">Creado</h4>
                            <p id="view-creado" class="text-gray-800 dark:text-gray-200"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mostrar/ocultar modals y llenar datos de 'Ver'
            const btnCreate = document.getElementById('btn-create');
            const modalCreate = document.getElementById('modal-create');
            const modalView = document.getElementById('modal-view');
            document.querySelectorAll('[data-close]').forEach(btn => btn.addEventListener('click', () => {
                modalCreate.style.display = 'none';
                modalView.style.display = 'none';
            }));
            btnCreate?.addEventListener('click', () => modalCreate.style.display = 'flex');
            document.querySelectorAll('.btn-view').forEach(btn => {
                btn.addEventListener('click', () => {
                    const t = JSON.parse(btn.dataset.ticket);
                    document.getElementById('view-id').textContent = t.id;
                    document.getElementById('view-area').textContent = t.area;
                    document.getElementById('view-asunto').textContent = t.asunto;
                    document.getElementById('view-descripcion').textContent = t.descripcion;
                    document.getElementById('view-estado').textContent = t.estado;
                    document.getElementById('view-estado').className = t.estado==='Abierto'
                        ? 'inline-block px-2 py-1 text-sm font-semibold rounded-full bg-green-200 text-green-800 dark:bg-green-600 dark:text-green-100'
                        : 'inline-block px-2 py-1 text-sm font-semibold rounded-full bg-yellow-200 text-yellow-800 dark:bg-yellow-600 dark:text-yellow-100';
                    document.getElementById('view-creado').textContent = t.created_at || t.creado;
                    modalView.style.display = 'flex';
                });
            });
        });
    </script>
</x-app-layout>
