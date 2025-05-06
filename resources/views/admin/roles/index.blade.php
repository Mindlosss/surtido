<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Gestión de Roles') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition-colors">
                   <i class="fas fa-users mr-2"></i> Usuarios
                </a>
                <a href="{{ route('admin.roles.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                   <i class="fas fa-plus-circle mr-2"></i> Nuevo Rol
                </a>
            </div>
        </div>
    </x-slot>

    <style>
        /* Scrollbars */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #2d3748;
        }
        ::-webkit-scrollbar-thumb {
            background: #4a5568;
            border-radius: 8px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #718096;
        }
    </style>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Notificaciones -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-100 rounded-lg shadow">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-100 rounded-lg shadow">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Panel principal -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Barra de búsqueda -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input 
                                    id="searchInput"
                                    type="text" 
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-gray-900 dark:text-gray-100"
                                    placeholder="Buscar roles..."
                                >
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Total: {{ $roles->count() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Contenedor de tabla con scroll -->
                <div class="overflow-hidden">
                    <div class="overflow-x-auto max-h-[calc(100vh-300px)]">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0 z-10">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-20">
                                        ID
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider min-w-[180px]">
                                        Rol
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Permisos
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-28">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700" id="roleTableBody">
                                @forelse($roles as $role)
                                    <tr class="role-row hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <!-- ID -->
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            #{{ $role->id }}
                                        </td>
                                        
                                        <!-- Nombre -->
                                        <td class="px-4 py-4">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                                                    <i class="fas fa-user-tag text-purple-600 dark:text-purple-300"></i>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100 whitespace-normal role-name">
                                                        {{ $role->name }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $role->created_at->format('d/m/Y') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <!-- Permisos -->
                                        <td class="px-4 py-4 whitespace-normal role-permissions">
                                            @if($role->permissions->count())
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($role->permissions->take(5) as $permission)
                                                        <span class="px-2 py-1 text-xs rounded bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                            {{ $permission->name }}
                                                        </span>
                                                    @endforeach
                                                    @if($role->permissions->count() > 5)
                                                        <span class="px-2 py-1 text-xs rounded bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                                            +{{ $role->permissions->count() - 5 }} más
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-xs italic text-gray-500 dark:text-gray-400">Sin permisos</span>
                                            @endif
                                        </td>
                                        
                                        <!-- Acciones -->
                                        <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-3">
                                                <!-- Editar -->
                                                <a href="{{ route('admin.roles.edit', $role) }}"
                                                   class="text-gray-400 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-300 transition-colors"
                                                   title="Editar rol">
                                                   <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <!-- Eliminar -->
                                                <button onclick="confirmDelete('{{ route('admin.roles.destroy', $role) }}')"
                                                        class="text-gray-400 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-300 transition-colors"
                                                        title="Eliminar rol">
                                                        <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            <div class="flex flex-col items-center justify-center py-8">
                                                <i class="fas fa-user-tag text-4xl text-gray-400 mb-4"></i>
                                                <p class="text-lg">No hay roles registrados</p>
                                                <a href="{{ route('admin.roles.create') }}" class="mt-4 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                    Crear primer rol
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Función para confirmar eliminación
        function confirmDelete(deleteUrl) {
            Swal.fire({
                title: '¿Confirmar eliminación?',
                text: "¡Esta acción no se puede deshacer!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                background: '#1e1e2f',
                color: '#fff',
                iconColor: '#e53e3e'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = deleteUrl;
                    
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';
                    
                    const method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'DELETE';
                    
                    form.appendChild(csrf);
                    form.appendChild(method);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Función de búsqueda
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const roleRows = document.querySelectorAll('.role-row');
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                roleRows.forEach(row => {
                    const name = row.querySelector('.role-name').textContent.toLowerCase();
                    const permissions = row.querySelector('.role-permissions').textContent.toLowerCase();
                    
                    if (name.includes(searchTerm) || permissions.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>
</x-app-layout>