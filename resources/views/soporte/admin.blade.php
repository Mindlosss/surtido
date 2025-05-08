<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 leading-tight">
            Sistema de Tickets
        </h2>
    </x-slot>

    <div class="py-7">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm dark:border-gray-700 p-6">

                {{-- Filtros de búsqueda --}}
                <form method="GET" action="{{ route('soporte') }}" class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
                    <input
                        name="search"
                        value="{{ request('search') }}"
                        type="text"
                        placeholder="Buscar tickets..."
                        class="w-full sm:w-64 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 placeholder-gray-500 dark:placeholder-gray-400 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4 sm:mb-0" />
                    <div class="flex space-x-2">
                        <select name="status" class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos</option>
                            <option value="Abierto" {{ request('status')=='Abierto'?'selected':'' }}>Abierto</option>
                            <option value="En Progreso" {{ request('status')=='En Progreso'?'selected':'' }}>En Progreso</option>
                            <option value="Cerrado" {{ request('status')=='Cerrado'?'selected':'' }}>Cerrado</option>
                        </select>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">Filtrar</button>
                        <a href="{{ route('soporte') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg shadow">Limpiar</a>
                    </div>
                </form>

                {{-- Tabla de tickets --}}
                <div class="overflow-x-auto">
                    <table id="tickets-table" class="min-w-full table-auto dark:text-gray-200">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <th>ID</th>
                                <th>Asunto</th>
                                <th>Solicitante</th>
                                <th>Prioridad</th>
                                <th>Estado</th>
                                <th>Creado</th>
                                <th>Actualizado</th>
                                <th>Asignado a</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $t)
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td class="px-4 py-2 border-b dark:border-gray-600">{{ $t->id }}</td>
                                    <td class="px-4 py-2 border-b dark:border-gray-600">{{ $t->asunto }}</td>
                                    <td class="px-4 py-2 border-b dark:border-gray-600">{{ $t->creador->name }}</td>
                                    <td class="px-4 py-2 border-b dark:border-gray-600">{{ $t->prioridad }}</td>
                                    <td class="px-4 py-2 border-b dark:border-gray-600">
                                        <form method="POST" action="{{ route('soporte.tickets.update', $t) }}">
                                            @csrf @method('PUT')
                                            <select name="estado" onchange="this.form.submit()" class="px-2 py-1 rounded-lg">
                                                @foreach(['Abierto','En Progreso','Cerrado'] as $e)
                                                    <option value="{{ $e }}"{{ $t->estado==$e?' selected':'' }}>{{ $e }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-4 py-2 border-b dark:border-gray-600">{{ $t->created_at->format('Y-m-d') }}</td>
                                    <td class="px-4 py-2 border-b dark:border-gray-600">{{ $t->updated_at->format('Y-m-d') }}</td>
                                    <td class="px-4 py-2 border-b dark:border-gray-600">
                                        <form method="POST" action="{{ route('soporte.tickets.update', $t) }}">
                                            @csrf @method('PUT')
                                            <select name="asignado_a" onchange="this.form.submit()" class="px-2 py-1 rounded-lg">
                                                <option value="">—</option>
                                                @foreach($responsables as $r)
                                                    <option value="{{ $r }}"{{ $t->asignado_a==$r?' selected':'' }}>{{ $r }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-4 py-2 border-b dark:border-gray-600 text-center">
                                        <button type="button" class="text-blue-600 hover:underline btn-view" data-ticket='@json($t)'>Ver</button>
                                    </td>
                                </tr>
                            @endforeach
                            @if($tickets->isEmpty())
                                <tr><td colspan="9" class="text-center py-4">No hay tickets.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- Modal Ver Ticket --}}
                <div id="modal-view" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50" style="display:none;">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-11/12 max-w-lg p-6 relative">
                        <button data-close class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 text-2xl">&times;</button>
                        <h3 class="text-xl font-semibold mb-4">Ticket #<span id="view-id"></span></h3>
                        <div class="space-y-4">
                            <div><h4>Asunto</h4><p id="view-asunto"></p></div>
                            <div><h4>Solicitante</h4><p id="view-solicitante"></p></div>
                            <div><h4>Descripción</h4><p id="view-descripcion"></p></div>
                            <div class="flex justify-between"><div><h4>Prioridad</h4><p id="view-prioridad"></p></div>
                            <div><h4>Estado</h4><span id="view-estado" class="inline-block px-2 py-1 text-sm font-semibold rounded-full"></span></div></div>
                            <div class="flex justify-between"><div><h4>Creado</h4><p id="view-creado"></p></div>
                            <div><h4>Actualizado</h4><p id="view-actualizado"></p></div></div>
                        </div>
                    </div>
                </div>

                {{-- Script para modal Ver --}}
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const modal = document.getElementById('modal-view');
                        document.querySelectorAll('[data-close]').forEach(btn => btn.onclick = () => modal.style.display='none');
                        document.querySelectorAll('.btn-view').forEach(btn => {
                            btn.onclick = () => {
                                const t = JSON.parse(btn.dataset.ticket);
                                document.getElementById('view-id').textContent = t.id;
                                document.getElementById('view-asunto').textContent = t.asunto;
                                document.getElementById('view-solicitante').textContent = t.creador.name;
                                document.getElementById('view-descripcion').textContent = t.descripcion;
                                document.getElementById('view-prioridad').textContent = t.prioridad;
                                document.getElementById('view-estado').textContent = t.estado;
                                document.getElementById('view-estado').className =
                                  t.estado==='Abierto' ? 'inline-block px-2 py-1 rounded-full bg-green-200 text-green-800' :
                                  t.estado==='En Progreso' ? 'inline-block px-2 py-1 rounded-full bg-yellow-200 text-yellow-800' :
                                  'inline-block px-2 py-1 rounded-full bg-gray-200 text-gray-800';
                                document.getElementById('view-creado').textContent = t.created_at;
                                document.getElementById('view-actualizado').textContent = t.updated_at;
                                modal.style.display = 'flex';
                            };
                        });
                    });
                </script>

            </div>
        </div>
    </div>
</x-app-layout>