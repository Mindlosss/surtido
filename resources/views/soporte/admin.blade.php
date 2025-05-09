<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 leading-tight">
            Sistema de Tickets
        </h2>
    </x-slot>

    <div class="py-4 sm:py-7">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm dark:border-gray-700 p-4 sm:p-6">

                {{-- Filtros de búsqueda --}}
                <form method="GET" action="{{ route('soporte') }}" class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center mb-6">
                    <input
                        name="search"
                        value="{{ request('search') }}"
                        type="text"
                        placeholder="Buscar tickets..."
                        class="w-full sm:w-64 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 placeholder-gray-500 dark:placeholder-gray-400 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                        <select name="status" class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos los estados</option>
                            <option value="Abierto" {{ request('status')=='Abierto'?'selected':'' }}>Abierto</option>
                            <option value="En Progreso" {{ request('status')=='En Progreso'?'selected':'' }}>En Progreso</option>
                            <option value="Cerrado" {{ request('status')=='Cerrado'?'selected':'' }}>Cerrado</option>
                        </select>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow w-full sm:w-auto">
                                Filtrar
                            </button>
                            <a href="{{ route('soporte') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg shadow w-full sm:w-auto text-center">
                                Limpiar
                            </a>
                        </div>
                    </div>
                </form>

                {{-- Tabla de tickets --}}
                <div class="overflow-x-auto rounded-lg border dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-3 sm:px-4 py-3 text-left text-xs sm:text-sm font-medium text-gray-800 dark:text-gray-200 uppercase">ID</th>
                                <th class="px-3 sm:px-4 py-3 text-left text-xs sm:text-sm font-medium text-gray-800 dark:text-gray-200 uppercase">Asunto</th>
                                <th class="hidden sm:table-cell px-4 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 uppercase">Solicitante</th>
                                <th class="px-3 sm:px-4 py-3 text-left text-xs sm:text-sm font-medium text-gray-800 dark:text-gray-200 uppercase">Prioridad</th>
                                <th class="hidden sm:table-cell px-4 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 uppercase">Estado</th>
                                <th class="hidden md:table-cell px-4 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 uppercase">Creado</th>
                                <th class="hidden lg:table-cell px-4 py-3 text-left text-sm font-medium text-gray-800 dark:text-gray-200 uppercase">Actualizado</th>
                                <th class="px-3 sm:px-4 py-3 text-left text-xs sm:text-sm font-medium text-gray-800 dark:text-gray-200 uppercase">Asignado</th>
                                <th class="px-3 sm:px-4 py-3 text-right text-xs sm:text-sm font-medium text-gray-800 dark:text-gray-200 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @foreach($tickets as $t)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">{{ $t->id }}</td>
                                    <td class="px-3 sm:px-4 py-3 max-w-[200px] truncate text-sm text-gray-800 dark:text-gray-200">{{ $t->asunto }}</td>
                                    <td class="hidden sm:table-cell px-4 py-3 text-sm text-gray-800 dark:text-gray-200">{{ $t->creador->name }}</td>
                                    <td class="px-3 sm:px-4 py-3 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                            {{ $t->prioridad === 'Alta' ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' : 
                                               ($t->prioridad === 'Media' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : 
                                               'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100') }}">
                                            {{ $t->prioridad }}
                                        </span>
                                    </td>
                                    <td class="hidden sm:table-cell px-4 py-3 text-sm">
                                        <form method="POST" action="{{ route('soporte.tickets.update', $t) }}">
                                            @csrf @method('PUT')
                                            <select name="estado" onchange="this.form.submit()" class="text-xs px-2 py-1 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                @foreach(['Abierto','En Progreso','Cerrado'] as $e)
                                                    <option value="{{ $e }}"{{ $t->estado==$e?' selected':'' }}>{{ $e }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                    <td class="hidden md:table-cell px-4 py-3 text-sm text-gray-800 dark:text-gray-200">{{ $t->created_at->format('d/m/Y') }}</td>
                                    <td class="hidden lg:table-cell px-4 py-3 text-sm text-gray-800 dark:text-gray-200">{{ $t->updated_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-3 sm:px-4 py-3 text-sm">
                                        <form method="POST" action="{{ route('soporte.tickets.update', $t) }}">
                                            @csrf @method('PUT')
                                            <select name="asignado_a" onchange="this.form.submit()" class="text-xs px-2 py-1 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                <option value="">—</option>
                                                @foreach($responsables as $r)
                                                    <option value="{{ $r }}"{{ $t->asignado_a==$r?' selected':'' }}>{{ $r }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-3 sm:px-4 py-3 text-right text-sm">
                                        <button type="button" class="text-blue-600 hover:text-blue-800 dark:hover:text-blue-400 transition-colors btn-view" data-ticket='@json($t)'>
                                            <span class="hidden sm:inline">Ver</span>
                                            <svg class="inline sm:hidden w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($tickets->isEmpty())
                        <div class="text-center py-4 text-gray-800 dark:text-gray-200">No se encontraron tickets.</div>
                    @endif
                </div>

                {{-- Modal Detalle Ticket --}}
                <div id="modal-view" class="fixed inset-0 z-50 hidden" aria-hidden="true">
                    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
                    
                    <div class="relative mx-auto p-4 w-full max-w-2xl top-1/2 -translate-y-1/2">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 sm:p-8">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                    Ticket #<span id="view-id"></span>
                                </h3>
                                <button type="button" data-close class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-4 text-gray-600 dark:text-gray-300">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 border-b pb-4 dark:border-gray-700">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Solicitante</dt>
                                        <dd id="view-solicitante" class="mt-1 text-gray-900 dark:text-gray-100"></dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Prioridad</dt>
                                        <dd id="view-prioridad" class="mt-1"></dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado</dt>
                                        <dd id="view-estado" class="mt-1"></dd>
                                    </div>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Asunto</dt>
                                    <dd id="view-asunto" class="mt-1 text-lg font-medium text-gray-900 dark:text-white"></dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Descripción</dt>
                                    <dd id="view-descripcion" class="mt-1 text-gray-900 dark:text-gray-100 prose dark:prose-invert max-h-64 overflow-y-auto"></dd>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t dark:border-gray-700">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Creado</dt>
                                        <dd id="view-creado" class="mt-1 text-gray-900 dark:text-gray-100"></dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Actualizado</dt>
                                        <dd id="view-actualizado" class="mt-1 text-gray-900 dark:text-gray-100"></dd>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const modal = document.getElementById('modal-view');
                        const closeModal = () => modal.classList.add('hidden');
                        
                        // Modal handlers
                        document.querySelectorAll('[data-close]').forEach(btn => btn.addEventListener('click', closeModal));
                        modal.addEventListener('click', (e) => e.target === modal && closeModal());

                        // View ticket details
                        document.querySelectorAll('.btn-view').forEach(btn => {
                            btn.addEventListener('click', () => {
                                const ticket = JSON.parse(btn.dataset.ticket);
                                
                                document.getElementById('view-id').textContent = ticket.id;
                                document.getElementById('view-asunto').textContent = ticket.asunto;
                                document.getElementById('view-solicitante').textContent = ticket.creador.name;
                                document.getElementById('view-descripcion').textContent = ticket.descripcion;
                                
                                // Prioridad
                                const prioridad = document.getElementById('view-prioridad');
                                prioridad.innerHTML = `
                                    <span class="px-3 py-1 text-sm rounded-full 
                                        ${ticket.prioridad === 'Alta' ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' :
                                           ticket.prioridad === 'Media' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' :
                                           'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'}">
                                        ${ticket.prioridad}
                                    </span>
                                `;
                                
                                // Estado
                                const estado = document.getElementById('view-estado');
                                estado.innerHTML = `
                                    <span class="px-3 py-1 text-sm rounded-full 
                                        ${ticket.estado === 'Abierto' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' :
                                           ticket.estado === 'En Progreso' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' :
                                           'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100'}">
                                        ${ticket.estado}
                                    </span>
                                `;
                                
                                // Fechas
                                const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
                                document.getElementById('view-creado').textContent = 
                                    new Date(ticket.created_at).toLocaleDateString('es-ES', options);
                                document.getElementById('view-actualizado').textContent = 
                                    new Date(ticket.updated_at).toLocaleDateString('es-ES', options);
                                
                                modal.classList.remove('hidden');
                            });
                        });
                    });
                </script>

            </div>
        </div>
    </div>
</x-app-layout>