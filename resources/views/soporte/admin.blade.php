<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 leading-tight">
                Sistema de Tickets
            </h2>
        </div>
    </x-slot>

    <div class="py-7">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm dark:border-gray-700 p-6">
                <!-- Búsqueda y filtros -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
                    <input id="search-input" type="text" placeholder="Buscar tickets..."
                           class="w-full sm:w-64 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 placeholder-gray-500 dark:placeholder-gray-400 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4 sm:mb-0" />
                    <div class="flex space-x-2">
                        <select id="status-filter"
                                class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos</option>
                            <option value="Abierto">Abierto</option>
                            <option value="En Progreso">En Progreso</option>
                            <option value="Cerrado">Cerrado</option>
                        </select>
                        <button id="btn-clear" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">
                            Limpiar
                        </button>
                    </div>
                </div>

                <!-- Tabla de tickets full width -->
                <div class="overflow-x-auto">
                    <table id="tickets-table" class="min-w-full table-auto dark:text-gray-200">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-left">ID</th>
                                <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-left">Asunto</th>
                                <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-left">Solicitante</th>
                                <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-left">Prioridad</th>
                                <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-left">Estado</th>
                                <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-left">Creado</th>
                                <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-left">Actualizado</th>
                                <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <p id="no-tickets-msg" class="text-center text-gray-500 dark:text-gray-400 py-4" style="display:none;">No hay tickets.</p>
                </div>
            </div>
        </div>

        <!-- Modal de detalle -->
        <div id="modal-view" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50" style="display:none;">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-11/12 max-w-lg p-6 relative">
                <button data-close class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 text-2xl leading-none">&times;</button>
                <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Ticket #<span id="view-id"></span></h3>
                <div class="space-y-4">
                    <div>
                        <h4 class="font-medium text-gray-700 dark:text-gray-200">Asunto</h4>
                        <p id="view-asunto" class="text-gray-800 dark:text-gray-200"></p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700 dark:text-gray-200">Solicitante</h4>
                        <p id="view-solicitante" class="text-gray-800 dark:text-gray-200"></p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-700 dark:text-gray-200">Descripción</h4>
                        <p id="view-descripcion" class="text-gray-800 dark:text-gray-200"></p>
                    </div>
                    <div class="flex justify-between">
                        <div>
                            <h4 class="font-medium text-gray-700 dark:text-gray-200">Prioridad</h4>
                            <p id="view-prioridad" class="text-gray-800 dark:text-gray-200"></p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-700 dark:text-gray-200">Estado</h4>
                            <span id="view-estado" class="inline-block px-2 py-1 text-sm font-semibold rounded-full"></span>
                        </div>
                    </div>
                    <div class="flex justify-between">
                        <div>
                            <h4 class="font-medium text-gray-700 dark:text-gray-200">Creado</h4>
                            <p id="view-creado" class="text-gray-800 dark:text-gray-200"></p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-700 dark:text-gray-200">Actualizado</h4>
                            <p id="view-actualizado" class="text-gray-800 dark:text-gray-200"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tickets = [
                { id: '001', asunto: 'Error de login', solicitante: 'Juan Pérez', prioridad: 'Alta', estado: 'En Progreso', creado: '2025-05-01', actualizado: '2025-05-03', descripcion: 'El usuario no puede iniciar sesión desde el portal web. Aparece mensaje de "credenciales inválidas" aun siendo correctas.' },
                { id: '002', asunto: 'Solicitud de acceso VPN', solicitante: 'María García', prioridad: 'Media', estado: 'Abierto', creado: '2025-04-28', actualizado: '2025-04-28', descripcion: 'Nueva solicitud de acceso VPN para recursos internos.' },
                { id: '003', asunto: 'Actualización de servidor', solicitante: 'Carlos López', prioridad: 'Baja', estado: 'Cerrado', creado: '2025-04-20', actualizado: '2025-04-22', descripcion: 'Se completó la actualización de sistema operativo y parches de seguridad.' }
            ];
            const tableBody = document.querySelector('#tickets-table tbody');
            const noMsg = document.getElementById('no-tickets-msg');
            const searchInput = document.getElementById('search-input');
            const statusFilter = document.getElementById('status-filter');
            const btnClear = document.getElementById('btn-clear');
            const modalView = document.getElementById('modal-view');
            const closeButtons = document.querySelectorAll('[data-close]');

            function renderTable(list) {
                tableBody.innerHTML = '';
                if (list.length === 0) {
                    noMsg.style.display = 'block';
                } else {
                    noMsg.style.display = 'none';
                    list.forEach(t => {
                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-gray-100 dark:hover:bg-gray-700';
                        tr.innerHTML = `
                            <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">${t.id}</td>
                            <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">${t.asunto}</td>
                            <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">${t.solicitante}</td>
                            <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">${t.prioridad}</td>
                            <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">
                                <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full ${
                                    t.estado==='Abierto'? 'bg-green-200 text-green-800 dark:bg-green-600 dark:text-green-100'
                                    : t.estado==='En Progreso'? 'bg-yellow-200 text-yellow-800 dark:bg-yellow-600 dark:text-yellow-100'
                                    : 'bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-100'}">
                                    ${t.estado}
                                </span>
                            </td>
                            <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">${t.creado}</td>
                            <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">${t.actualizado}</td>
                            <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-center">
                                <button class="text-blue-600 hover:underline btn-view" data-id="${t.id}">Ver</button>
                            </td>
                        `;
                        tableBody.appendChild(tr);
                    });
                    initViewButtons();
                }
            }

            function filterTickets() {
                const q = searchInput.value.toLowerCase();
                const s = statusFilter.value;
                const filtered = tickets.filter(t => {
                    return (!s || t.estado === s)
                        && (!q || t.asunto.toLowerCase().includes(q) || t.solicitante.toLowerCase().includes(q));
                });
                renderTable(filtered);
            }

            function clearFilters() {
                searchInput.value = '';
                statusFilter.value = '';
                renderTable(tickets);
            }

            function openViewModal(ticket) {
                document.getElementById('view-id').textContent = ticket.id;
                document.getElementById('view-asunto').textContent = ticket.asunto;
                document.getElementById('view-solicitante').textContent = ticket.solicitante;
                document.getElementById('view-descripcion').textContent = ticket.descripcion;
                document.getElementById('view-prioridad').textContent = ticket.prioridad;
                document.getElementById('view-estado').textContent = ticket.estado;
                document.getElementById('view-estado').className = ticket.estado==='Abierto'
                    ? 'inline-block px-2 py-1 text-sm font-semibold rounded-full bg-green-200 text-green-800 dark:bg-green-600 dark:text-green-100'
                    : ticket.estado==='En Progreso'
                        ? 'inline-block px-2 py-1 text-sm font-semibold rounded-full bg-yellow-200 text-yellow-800 dark:bg-yellow-600 dark:text-yellow-100'
                        : 'inline-block px-2 py-1 text-sm font-semibold rounded-full bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-100';
                document.getElementById('view-creado').textContent = ticket.creado;
                document.getElementById('view-actualizado').textContent = ticket.actualizado;
                modalView.style.display = 'flex';
            }

            function closeViewModal() {
                modalView.style.display = 'none';
            }

            function initViewButtons() {
                document.querySelectorAll('.btn-view').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const id = btn.getAttribute('data-id');
                        const ticket = tickets.find(t => t.id === id);
                        openViewModal(ticket);
                    });
                });
            }

            // Event listeners
            searchInput.addEventListener('input', filterTickets);
            statusFilter.addEventListener('change', filterTickets);
            btnClear.addEventListener('click', clearFilters);
            closeButtons.forEach(btn => btn.addEventListener('click', closeViewModal));

            // Initial render
            renderTable(tickets);
        });
    </script>
</x-app-layout>
