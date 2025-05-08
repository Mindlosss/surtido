<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 leading-tight">
                Mis Tickets
            </h2>
            <button id="btn-create" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
                Crear Ticket
            </button>
        </div>
    </x-slot>

    <div class="py-7">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
                    <input id="search-input" type="text" placeholder="Buscar mis tickets..."
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
                        <tbody></tbody>
                    </table>
                    <p id="no-tickets-msg" class="text-center text-gray-500 dark:text-gray-400 py-4" style="display:none;">No tienes tickets.</p>
                </div>
            </div>
        </div>
        <!-- Modal Crear Ticket -->
        <div id="modal-create" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50" style="display:none;">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-11/12 max-w-md p-6 relative">
                <button data-close class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 text-2xl leading-none">&times;</button>
                <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Crear Nuevo Ticket</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 dark:text-gray-200">Área</label>
                        <select id="new-area" class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" disabled selected hidden>Selecciona un área</option>
                            <optgroup label="Area administrativa">
                                <option value="Diseño">Diseño</option>
                                <option value="Recursos Humanos">Recursos Humanos</option>
                                <option value="Ecommerce">Ecommerce</option>
                                <option value="Contabilidad">Contabilidad</option>
                                <option value="Administración">Administración</option>
                                <option value="Credito y cobranza">Credito y cobranza</option>
                                <option value="Compras">Compras</option>
                            </optgroup>

                            <optgroup label="Sucursales">
                                <option value="Makita">Makita</option>
                                <option value="Milwaukee">Milwaukee</option>
                                <option value="California">California</option>
                                <option value="Matriz">Matriz</option>
                                <option value="Multimarca">Multimarca</option>    
                                <option value="Taller California">Taller California</option>
                            </optgroup>
                            
                            <optgroup label="Almacenes">
                                <option value="Almacen">Almacen</option>
                                <option value="Bodega">Bodega</option>
                            </optgroup>
                            
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-200">Asunto</label>
                        <select id="new-asunto" class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" disabled selected hidden>Selecciona un asunto</option></option>
                            <option label="INNOVASMART">INNOVASMART</option>
                            <option label="INNOVAD-IA">INNOVAD-IA</option>
                            <option value="Internet y telefonía">Internet y telefonía</option>
                            <option value="Reportes">Reportes</option>
                            <option value="Hardware">Hardware</option>
                            <option value="Software">Software</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-200">Descripción</label>
                        <textarea placeholder="Detalles del ticket..." id="new-descripcion" rows="3" class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-200">Prioridad</label>
                        <select id="new-prioridad" class="w-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="Alta">Alta</option>
                            <option value="Media" selected>Media</option>
                            <option value="Baja">Baja</option>
                        </select>
                    </div>
                    <div class="mt-6 flex justify-end space-x-2">
                        <button id="btn-save" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Guardar</button>
                        <button data-close class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg shadow">Cancelar</button>
                    </div>
                </div>
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
            const tickets = [
                { id: '101', area: 'Soporte Técnico', asunto: 'Problema Técnico', descripcion: 'No carga la información de mi perfil.', estado: 'En Progreso', creado: '2025-05-05' },
                { id: '102', area: 'Desarrollo', asunto: 'Solicitud de Acceso', descripcion: 'Necesito restablecer mi contraseña.', estado: 'Abierto', creado: '2025-05-06' }
            ];
            const tableBody = document.querySelector('#tickets-table tbody');
            const noMsg = document.getElementById('no-tickets-msg');
            const searchInput = document.getElementById('search-input');
            const statusFilter = document.getElementById('status-filter');
            const btnClear = document.getElementById('btn-clear');
            const modalCreate = document.getElementById('modal-create');
            const modalView = document.getElementById('modal-view');
            const btnCreate = document.getElementById('btn-create');
            const btnSave = document.getElementById('btn-save');
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
                            <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">
                                <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full ${
                                    t.estado==='Abierto'? 'bg-green-200 text-green-800 dark:bg-green-600 dark:text-green-100'
                                    : t.estado==='En Progreso'? 'bg-yellow-200 text-yellow-800 dark:bg-yellow-600 dark:text-yellow-100'
                                    : 'bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-100'}">
                                    ${t.estado}
                                </span>
                            </td>
                            <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">${t.creado}</td>
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
                    return (!s || t.estado === s) && (!q || t.asunto.toLowerCase().includes(q));
                });
                renderTable(filtered);
            }
            function clearFilters() {
                searchInput.value = '';
                statusFilter.value = '';
                renderTable(tickets);
            }
            function openCreateModal() { modalCreate.style.display = 'flex'; }
            function closeCreateModal() {
                modalCreate.style.display = 'none';
                document.getElementById('new-asunto').value = 'Problema Técnico';
                document.getElementById('new-area').value = 'Soporte Técnico';
                document.getElementById('new-descripcion').value = '';
                document.getElementById('new-prioridad').value = 'Media';
            }
            function openViewModal(ticket) {
                document.getElementById('view-id').textContent = ticket.id;
                document.getElementById('view-area').textContent = ticket.area;
                document.getElementById('view-asunto').textContent = ticket.asunto;
                document.getElementById('view-descripcion').textContent = ticket.descripcion;
                document.getElementById('view-estado').textContent = ticket.estado;
                document.getElementById('view-estado').className = ticket.estado==='Abierto'? 'inline-block px-2 py-1 text-sm font-semibold rounded-full bg-green-200 text-green-800 dark:bg-green-600 dark:text-green-100'
                    : ticket.estado==='En Progreso'? 'inline-block px-2 py-1 text-sm font-semibold rounded-full bg-yellow-200 text-yellow-800 dark:bg-yellow-600 dark:text-yellow-100'
                    : 'inline-block px-2 py-1 text-sm font-semibold rounded-full bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-100';
                document.getElementById('view-creado').textContent = ticket.creado;
                modalView.style.display = 'flex';
            }
            function closeViewModal() { modalView.style.display = 'none'; }
            function initViewButtons() {
                document.querySelectorAll('.btn-view').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const id = btn.getAttribute('data-id');
                        const ticket = tickets.find(t => t.id === id);
                        openViewModal(ticket);
                    });
                });
            }
            function createTicket() {
                const area = document.getElementById('new-area').value;
                const asunto = document.getElementById('new-asunto').value;
                const descripcion = document.getElementById('new-descripcion').value;
                const prioridad = document.getElementById('new-prioridad').value;
                if (!asunto || !descripcion) { alert('Completa todos los campos'); return; }
                const id = String(100 + tickets.length + 1);
                const creado = new Date().toISOString().split('T')[0];
                const ticket = { id, area, asunto, descripcion, estado: 'Abierto', creado };
                tickets.push(ticket);
                clearFilters();
                closeCreateModal();
            }
            searchInput.addEventListener('input', filterTickets);
            statusFilter.addEventListener('change', filterTickets);
            btnClear.addEventListener('click', clearFilters);
            btnCreate.addEventListener('click', openCreateModal);
            btnSave.addEventListener('click', createTicket);
            closeButtons.forEach(btn => btn.addEventListener('click', () => { closeCreateModal(); closeViewModal(); }));
            renderTable(tickets);
        });
    </script>
</x-app-layout>
