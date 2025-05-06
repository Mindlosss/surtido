<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Editar Rol') }}: {{ $role->name }}
            </h2>
            <a href="{{ route('admin.roles.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition-colors"
               onclick="return confirmNavigation(event)">
               <i class="fas fa-arrow-left mr-2"></i> Volver a Roles
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
                <form action="{{ route('admin.roles.update', $role) }}" method="POST" id="roleForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Contenedor de dos columnas -->
                    <div class="flex flex-col md:flex-row">
                        <!-- Columna izquierda - Información básica -->
                        <div class="w-full md:w-1/2 p-6 border-r border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6 pb-2 border-b border-gray-200 dark:border-gray-700 flex items-center">
                                <i class="fas fa-id-card mr-2"></i> Información Básica
                            </h3>

                            <!-- Nombre del Rol -->
                            <div class="mb-5">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Nombre del Rol
                                    </label>
                                    <span class="text-xs text-red-500 dark:text-red-400">(Obligatorio)</span>
                                </div>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user-tag text-gray-400"></i>
                                    </div>
                                    <input
                                        type="text"
                                        name="name"
                                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-gray-900 dark:text-gray-100"
                                        value="{{ old('name', $role->name) }}"
                                        required
                                    />
                                </div>
                                @error('name')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Columna derecha - Permisos -->
                        <div class="w-full md:w-1/2 p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-6 pb-2 border-b border-gray-200 dark:border-gray-700 flex items-center">
                                <i class="fas fa-key mr-2"></i> Asignación de Permisos
                            </h3>

                            <!-- Permisos disponibles -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                    Permisos Asignados
                                </label>
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600 max-h-[300px] overflow-y-auto">
                                    <div class="space-y-3">
                                        @foreach($permissions as $permission)
                                            <div class="flex items-start">
                                                <div class="flex items-center h-5">
                                                    <input
                                                        id="permission-{{ $permission->id }}"
                                                        type="checkbox"
                                                        name="permissions[]"
                                                        value="{{ $permission->name }}"
                                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                                                        {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                                    >
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="permission-{{ $permission->id }}" class="font-medium text-gray-700 dark:text-gray-300">
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
                                    <i class="fas fa-info-circle mr-1"></i> Selecciona los permisos para este rol.
                                </p>
                                @error('permissions')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex items-center justify-end space-x-4 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                        <a href="{{ route('admin.roles.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition-colors"
                           onclick="return confirmNavigation(event)">
                           <i class="fas fa-times mr-2"></i> Cancelar
                        </a>
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800"
                            onclick="return confirmSubmit(event)"
                        >
                            <i class="fas fa-save mr-2"></i> Actualizar Rol
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Scrollbars para la lista de permisos */
        .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }
        .overflow-y-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }
        .dark .overflow-y-auto::-webkit-scrollbar-track {
            background: #2d3748;
        }
        .dark .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #4a5568;
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

        // Función para comparar si hay cambios reales
        function hasRealChanges(form, initialData) {
            const formData = new FormData(form);
            
            // Comparar cada campo excepto tokens y método
            for (let [key, value] of formData.entries()) {
                if (key === '_token' || key === '_method') continue;
                
                // Caso especial para permisos (array)
                if (key === 'permissions[]') {
                    const currentPerms = Array.from(formData.getAll('permissions[]'));
                    const initialPerms = initialData.permissions;
                    
                    if (currentPerms.length !== initialPerms.length) return true;
                    
                    for (let perm of currentPerms) {
                        if (!initialPerms.includes(perm)) return true;
                    }
                    continue;
                }
                
                // Comparación normal para otros campos
                if (String(value) !== String(initialData[key])) {
                    return true;
                }
            }
            
            return false;
        }

        // Función para confirmar navegación fuera del formulario
        function confirmNavigation(event) {
            const form = document.getElementById('roleForm');
            
            // Obtener datos iniciales del formulario
            const initialData = {
                name: '{{ old('name', $role->name) }}',
                permissions: {!! json_encode($role->permissions->pluck('name')->toArray()) !!}
            };
            
            // Solo mostrar alerta si hay cambios reales
            if (hasRealChanges(form, initialData)) {
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
            const form = document.getElementById('roleForm');
            
            // Validar que se haya ingresado un nombre
            const roleName = form.querySelector('input[name="name"]').value;
            if (!roleName) {
                swalWithDarkMode.fire({
                    title: 'Nombre requerido',
                    text: 'Debes ingresar un nombre para el rol',
                    icon: 'error',
                    confirmButtonText: 'Entendido'
                });
                return false;
            }
            
            swalWithDarkMode.fire({
                title: '¿Actualizar rol?',
                text: 'Por favor revisa que los datos sean correctos antes de continuar',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, actualizar',
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
    </script>
    
</x-app-layout>