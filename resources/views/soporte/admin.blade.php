<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 leading-tight">
                Sistema de Tickets
            </h2>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
                Nuevo Ticket
            </button>
        </div>
    </x-slot>

    <!-- Alpine.js para interactividad -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <div class="py-7" x-data="ticketSystem()" x-init="init()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm dark:border-gray-700 p-6">
                <!-- Búsqueda y filtros -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
                    <input type="text" placeholder="Buscar tickets..."
                           class="w-full sm:w-64 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 placeholder-gray-500 dark:placeholder-gray-400 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4 sm:mb-0"
                           x-model="query"
                           @input.debounce.300ms="filterTickets()" />
                    <div class="flex space-x-2">
                        <select x-model="statusFilter" @change="filterTickets()"
                                class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos</option>
                            <option value="Abierto">Abierto</option>
                            <option value="En Progreso">En Progreso</option>
                            <option value="Cerrado">Cerrado</option>
                        </select>
                        <button @click="resetFilters()"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">
                            Limpiar
                        </button>
                    </div>
                </div>

                <!-- Tabla de tickets full width -->
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto dark:text-gray-200">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-left">ID</th>
                                <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-left">Asunto</th>
                                <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-left">Solicitante</th>
                                <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-left">Prioridad</th>
                                <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-left">Estado</th>
                                <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-left">Creado</th>
                                <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="ticket in filtered" :key="ticket.id">
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600" x-text="ticket.id"></td>
                                    <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600" x-text="ticket.asunto"></td>
                                    <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600" x-text="ticket.solicitante"></td>
                                    <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600" x-text="ticket.prioridad"></td>
                                    <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">
                                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full"
                                              :class="{
                                                  'bg-green-200 text-green-800 dark:bg-green-600 dark:text-green-100': ticket.estado==='Abierto',
                                                  'bg-yellow-200 text-yellow-800 dark:bg-yellow-600 dark:text-yellow-100': ticket.estado==='En Progreso',
                                                  'bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-100': ticket.estado==='Cerrado'
                                              }"
                                              x-text="ticket.estado"></span>
                                    </td>
                                    <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600" x-text="ticket.creado"></td>
                                    <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-center">
                                        <button @click="openModal(ticket)"
                                                class="text-blue-600 hover:underline focus:outline-none">
                                            Ver
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <p x-show="filtered.length===0" class="text-center text-gray-500 py-4">No hay tickets.</p>
                </div>
            </div>
        </div>

        <!-- Modal de detalle -->
        <div x-show="isModalOpen" x-cloak
             class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-11/12 max-w-lg p-6 relative">
                <button @click="closeModal()"
                        class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 text-2xl leading-none">
                    &times;
                </button>
                <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">
                    Ticket #<span x-text="selected.id"></span>
                </h3>
                <div class="space-y-4">
                    <div>
                        <h4 class="font-medium text-gray-700 dark:text-gray-200">Asunto</h4>
                        <p class="text-gray-800 dark:text-gray-200" x-text="selected.asunto"></p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700 dark:text-gray-200">Solicitante</h4>
                        <p class="text-gray-800 dark:text-gray-200" x-text="selected.solicitante"></p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700 dark:text-gray-200">Descripción</h4>
                        <p class="text-gray-800 dark:text-gray-200" x-text="selected.descripcion"></p>
                    </div>
                    <div class="flex justify-between">
                        <div>
                            <h4 class="font-medium text-gray-700 dark:text-gray-200">Prioridad</h4>
                            <p class="text-gray-800 dark:text-gray-200" x-text="selected.prioridad"></p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-700 dark:text-gray-200">Estado</h4>
                            <span class="inline-block px-2 py-1 text-sm font-semibold rounded-full"
                                  :class="{
                                      'bg-green-200 text-green-800 dark:bg-green-600 dark:text-green-100': selected.estado==='Abierto',
                                      'bg-yellow-200 text-yellow-800 dark:bg-yellow-600 dark:text-yellow-100': selected.estado==='En Progreso',
                                      'bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-100': selected.estado==='Cerrado'
                                  }"
                                  x-text="selected.estado"></span>
                        </div>
                    </div>
                    <div class="flex justify-between">
                        <div>
                            <h4 class="font-medium text-gray-700 dark:text-gray-200">Creado</h4>
                            <p class="text-gray-800 dark:text-gray-200" x-text="selected.creado"></p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-700 dark:text-gray-200">Actualizado</h4>
                            <p class="text-gray-800 dark:text-gray-200" x-text="selected.actualizado"></p>
                        </div>
                    </div>
                    <div class="mt-6 flex space-x-2">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
                            Marcar como Resuelto
                        </button>
                        <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow">
                            Eliminar Ticket
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function ticketSystem() {
            return {
                query: '',
                statusFilter: '',
                tickets: [
                    { id: '001', asunto: 'Error de login', solicitante: 'Juan Pérez', prioridad: 'Alta', estado: 'En Progreso', creado: '2025-05-01', actualizado: '2025-05-03', descripcion: 'El usuario no puede iniciar sesión desde el portal web. Aparece mensaje de "credenciales inválidas" aún siendo correctas.' },
                    { id: '002', asunto: 'Solicitud de acceso VPN', solicitante: 'María García', prioridad: 'Media', estado: 'Abierto', creado: '2025-04-28', actualizado: '2025-04-28', descripcion: 'Nueva solicitud de acceso VPN para recursos internos.' },
                    { id: '003', asunto: 'Actualización de servidor', solicitante: 'Carlos López', prioridad: 'Baja', estado: 'Cerrado', creado: '2025-04-20', actualizado: '2025-04-22', descripcion: 'Se completó la actualización de sistema operativo y parches de seguridad.' }
                ],
                filtered: [],
                selected: null,
                isModalOpen: false,
                init() {
                    this.filtered = this.tickets;
                },
                filterTickets() {
                    this.filtered = this.tickets.filter(t => {
                        return (!this.statusFilter || t.estado === this.statusFilter)
                            && (!this.query || t.asunto.toLowerCase().includes(this.query.toLowerCase()) || t.solicitante.toLowerCase().includes(this.query.toLowerCase()));
                    });
                },
                resetFilters() {
                    this.query = '';
                    this.statusFilter = '';
                    this.filterTickets();
                },
                openModal(ticket) {
                    this.selected = ticket;
                    this.isModalOpen = true;
                },
                closeModal() {
                    this.isModalOpen = false;
                    this.selected = null;
                }
            }
        }
    </script>
</x-app-layout>
