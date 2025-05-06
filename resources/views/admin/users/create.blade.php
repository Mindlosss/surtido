<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Crear nuevo usuario') }}
            </h2>
            <a href="{{ route('admin.users.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition-colors"
               onclick="return confirmNavigation(event)">
               <i class="fas fa-arrow-left mr-2"></i> Volver a Usuarios
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Notificaciones de error -->
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-100 rounded-lg shadow">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <div>
                            <h4 class="font-medium">Por favor corrige los siguientes errores:</h4>
                            <ul class="list-disc list-inside mt-1 text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

                
            <!-- Panel principal -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('admin.users.store') }}" method="POST" id="userForm">
                    @csrf
                    
                    <!-- Contenedor de dos columnas -->
                    <div class="flex flex-col md:flex-row">
                        <!-- Columna izquierda - Información básica -->
                        <div class="w-full md:w-1/2 p-6 border-r border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6 pb-2 border-b border-gray-200 dark:border-gray-700 flex items-center">
                                <i class="fas fa-id-card mr-2"></i> Información Básica
                            </h3>

                            <!-- Nombre -->
                            <div class="mb-5">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Nombre Completo
                                    </label>
                                    <span class="text-xs text-red-500 dark:text-red-400">(Obligatorio)</span>
                                </div>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    <input
                                        type="text"
                                        name="name"
                                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-gray-900 dark:text-gray-100"
                                        placeholder="Ej: Juan Pérez"
                                        value="{{ old('name') }}"
                                        required
                                    />
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="mb-5">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Correo Electrónico
                                    </label>
                                    <span class="text-xs text-red-500 dark:text-red-400">(Obligatorio)</span>
                                </div>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400"></i>
                                    </div>
                                    <input
                                        type="email"
                                        name="email"
                                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-gray-900 dark:text-gray-100"
                                        placeholder="Ej: usuario@dominio.com"
                                        value="{{ old('email') }}"
                                        required
                                    />
                                </div>
                            </div>

                            <!-- Contraseña -->
                            <div class="mb-5">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Contraseña
                                    </label>
                                    <span class="text-xs text-red-500 dark:text-red-400">(Obligatorio)</span>
                                </div>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-400"></i>
                                    </div>
                                    <input
                                        type="password"
                                        name="password"
                                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-gray-900 dark:text-gray-100"
                                        placeholder="Mínimo 4 caracteres"
                                        required
                                        minlength="4"
                                    />
                                </div>
                                
                            </div>

                            <!-- Confirmar Contraseña -->
                            <div class="mb-5">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Confirmar Contraseña
                                    </label>
                                    <span class="text-xs text-red-500 dark:text-red-400">(Obligatorio)</span>
                                </div>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-400"></i>
                                    </div>
                                    <input
                                        type="password"
                                        name="password_confirmation"
                                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-gray-900 dark:text-gray-100"
                                        placeholder="Repite la contraseña"
                                        required
                                        minlength="4"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Columna derecha - Roles y permisos -->
                        <div class="w-full md:w-1/2 p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6 pb-2 border-b border-gray-200 dark:border-gray-700 flex items-center">
                                <i class="fas fa-user-shield mr-2"></i> Roles y Permisos
                            </h3>

                            <!-- Rol principal -->
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Rol Principal
                                    </label>
                                    <span class="text-xs text-red-500 dark:text-red-400">(Obligatorio)</span>
                                </div>
                                <select
                                    id="roleSelect"
                                    name="role"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                    required
                                >
                                    <option value="">-- Seleccione un rol --</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-info-circle mr-1"></i> El rol determina los permisos básicos del usuario.
                                </p>
                            </div>

                            <!-- Permisos adicionales -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                    Permisos Adicionales (Opcional)
                                </label>
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 max-h-[300px] overflow-y-auto">
                                    <div class="space-y-3" id="permissionsContainer">
                                        @foreach($permissions as $permission)
                                            <div class="flex items-start permission-item" data-permission="{{ $permission->name }}">
                                                <div class="flex items-center h-5">
                                                    <input
                                                        id="permission-{{ $permission->id }}"
                                                        type="checkbox"
                                                        name="permissions[]"
                                                        value="{{ $permission->name }}"
                                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 permission-checkbox"
                                                        {{ is_array(old('permissions')) && in_array($permission->name, old('permissions')) ? 'checked' : '' }}
                                                    >
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="permission-{{ $permission->id }}" class="font-medium text-gray-700 dark:text-gray-300 permission-label">
                                                        {{ $permission->name }}
                                                    </label>
                                                    @if($permission->description)
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                            {{ $permission->description }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-info-circle mr-1"></i> Los permisos tachados ya están incluidos en el rol seleccionado.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex items-center justify-end space-x-4 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                        <a href="{{ route('admin.users.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition-colors"
                           onclick="return confirmNavigation(event)">
                           <i class="fas fa-times mr-2"></i> Cancelar
                        </a>
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800"
                            onclick="return confirmSubmit(event)"
                        >
                            <i class="fas fa-save mr-2"></i> Crear Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Datos de permisos por rol (ocultos) -->
    <div id="rolePermissionsData" class="hidden">
        @foreach($roles as $role)
            <div data-role="{{ $role->name }}" data-permissions="{{ $role->permissions->pluck('name')->toJson() }}"></div>
        @endforeach
    </div>

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
        
        /* Estilo para permisos incluidos en el rol */
        .permission-included .permission-label {
            text-decoration: line-through;
            color: #9CA3AF !important;
        }
        .dark .permission-included .permission-label {
            color: #6B7280 !important;
        }
        .permission-included .permission-checkbox {
            opacity: 0.6;
        }
    </style>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Configuración global de SweetAlert2 para modo oscuro
        const swalWithDarkMode = Swal.mixin({
            background: '#1e293b',
            color: '#f8fafc',
            iconColor: '#6366f1',
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#ef4444',
        });

        // Variable para almacenar la URL de destino
        let targetUrl = '';

        // Función para confirmar navegación fuera del formulario
        function confirmNavigation(event) {
            const form = document.getElementById('userForm');
            const formData = new FormData(form);
            let hasData = false;
            
            // Verificar si hay datos en el formulario
            for (let [key, value] of formData.entries()) {
                if (key !== '_token' && value) {
                    hasData = true;
                    break;
                }
            }
            
            if (hasData) {
                event.preventDefault();
                targetUrl = event.currentTarget.href;
                
                swalWithDarkMode.fire({
                    title: '¿Estás seguro?',
                    text: 'Tienes cambios sin guardar. ¿Quieres salir de todas formas?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, salir',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = targetUrl;
                    }
                });
                
                return false;
            }
            return true;
        }

        // Función para confirmar envío del formulario
        function confirmSubmit(event) {
            event.preventDefault();
            const form = document.getElementById('userForm');
            
            // Validación de contraseña mínima
            const password = form.querySelector('input[name="password"]').value;
            if (password.length < 4) {
                swalWithDarkMode.fire({
                    title: 'Contraseña muy corta',
                    text: 'La contraseña debe tener al menos 4 caracteres',
                    icon: 'error',
                    confirmButtonText: 'Entendido'
                });
                return false;
            }
            
            // Validar que las contraseñas coincidan
            onst passwordConfirmation = form.querySelector('input[name="password_confirmation"]').value;
            if (password !== passwordConfirmation) {
                swalWithDarkMode.fire({
                    title: 'Contraseñas no coinciden',
                    text: 'Las contraseñas ingresadas no son iguales',
                    icon: 'error',
                    confirmButtonText: 'Entendido'
                });
                return false;
            }
            
            // Validar que se haya seleccionado un rol
            const role = form.querySelector('select[name="role"]').value;
            if (!role) {
                swalWithDarkMode.fire({
                    title: 'Rol no seleccionado',
                    text: 'Debes seleccionar un rol para el usuario',
                    icon: 'error',
                    confirmButtonText: 'Entendido'
                });
                return false;
            }
            
            swalWithDarkMode.fire({
                title: '¿Crear nuevo usuario?',
                text: 'Por favor revisa que los datos sean correctos antes de continuar',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, crear',
                cancelButtonText: 'Cancelar',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return new Promise((resolve) => {
                        form.submit();
                        resolve();
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            });
        }

        // Función para mostrar alerta de éxito (si hay mensaje de éxito en la sesión)
        @if(session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                swalWithDarkMode.fire({
                    title: '¡Éxito!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                });
            });
        @endif

        // Función para mostrar alerta de error (si hay mensaje de error en la sesión)
        @if(session('error'))
            document.addEventListener('DOMContentLoaded', function() {
                swalWithDarkMode.fire({
                    title: '¡Error!',
                    text: '{{ session('error') }}',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            });
        @endif

        // Función para manejar la visualización de permisos
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('roleSelect');
            const permissionsContainer = document.getElementById('permissionsContainer');
            
            // Crear mapa de permisos por rol
            const rolePermissions = {};
            document.querySelectorAll('#rolePermissionsData div[data-role]').forEach(el => {
                const roleName = el.getAttribute('data-role');
                const permissions = JSON.parse(el.getAttribute('data-permissions'));
                rolePermissions[roleName] = permissions;
            });
            
            // Función para actualizar la visualización de permisos
            function updatePermissionsDisplay() {
                const selectedRole = roleSelect.value;
                const rolePerms = selectedRole ? rolePermissions[selectedRole] : [];
                
                // Resetear todos los permisos
                document.querySelectorAll('.permission-item').forEach(item => {
                    item.classList.remove('permission-included');
                    const checkbox = item.querySelector('.permission-checkbox');
                    checkbox.disabled = false;
                    checkbox.checked = false;
                });
                
                // Marcar los permisos incluidos en el rol
                if (selectedRole) {
                    rolePerms.forEach(permName => {
                        const permItem = document.querySelector(`.permission-item[data-permission="${permName}"]`);
                        if (permItem) {
                            permItem.classList.add('permission-included');
                            const checkbox = permItem.querySelector('.permission-checkbox');
                            checkbox.disabled = true;
                        }
                    });
                }
                
                // Mantener los checks que ya estaban seleccionados (en caso de error de validación)
                @if(is_array(old('permissions')))
                    @foreach(old('permissions') as $perm)
                        const oldPermItem = document.querySelector(`.permission-item[data-permission="{{ $perm }}"]`);
                        if (oldPermItem && !oldPermItem.classList.contains('permission-included')) {
                            const checkbox = oldPermItem.querySelector('.permission-checkbox');
                            checkbox.checked = true;
                        }
                    @endforeach
                @endif
            }
            
            // Escuchar cambios en el select de rol
            roleSelect.addEventListener('change', updatePermissionsDisplay);
            
            // Ejecutar al cargar la página
            updatePermissionsDisplay();
        });
    </script>

</x-app-layout>