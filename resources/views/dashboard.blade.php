<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 leading-tight">
                Aplicaciones
            </h2>
            <!-- Alerta de precios libres (solo para usuarios con permiso) -->
            <div id="preciosAlert" class="{{ $showAlert ?? false ? 'block' : 'hidden' }}">
                <div class="bg-red-500 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    PRECIOS LIBRES
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-7">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Card principal ajustada sin scroll -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden border border-gray-200 dark:border-gray-700">
                <div class="p-5">
                    <!-- Grid de aplicaciones optimizado -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @php
                            $apps = [
                                ['name' => 'ADMIN', 'icon' => 'admin.png', 'route' => 'admin.users.index', 'role' => 'admin', 'dev' => false],
                                ['name' => 'SURTIDO', 'icon' => 'surtido.png', 'route' => 'surtido', 'perm' => 'ver-surtido', 'dev' => false],
                                ['name' => 'SURTIDO MELI', 'icon' => 'mercado.png', 'route' => 'mercado', 'perm' => 'ver-mercado', 'dev' => false],
                                ['name' => 'INVENTARIO', 'icon' => 'inventario.png', 'route' => 'inv', 'perm' => 'ver-inventario', 'dev' => false],
                                ['name' => 'GESTOR', 'icon' => 'gestor.png', 'route' => 'gestor', 'perm' => 'ver-gestor', 'dev' => false],
                                ['name' => 'COMPRA vs XML', 'icon' => 'comparador.png', 'route' => 'comparador', 'perm' => 'ver-comparador', 'dev' => false],
                                ['name' => 'PUBLICADOR', 'icon' => 'publicador.png', 'route' => '#', 'perm' => 'ver-publicador', 'dev' => true],
                                ['name' => 'ACTUALIZADOR', 'icon' => 'actualizador.png', 'route' => '#', 'perm' => 'ver-actualizador', 'dev' => true],
                                ['name' => 'COSTOS', 'icon' => 'costos.png', 'route' => '#', 'perm' => 'ver-costos', 'dev' => true],
                                ['name' => 'TALLERES', 'icon' => 'talleres.png', 'route' => '#', 'perm' => 'ver-talleres', 'dev' => true],
                                ['name' => 'EXHIBICION', 'icon' => 'exhibicion.png', 'route' => '#', 'perm' => 'ver-exhibicion', 'dev' => true],
                                ['name' => 'INVERSIONES', 'icon' => 'inversiones.png', 'route' => '#', 'perm' => 'ver-inversiones', 'dev' => true],
                                ['name' => 'COTIZADOR', 'icon' => 'cotizador.png', 'route' => 'cotizador', 'perm' => 'ver-cotizador', 'dev' => false],
                                ['name' => 'VENTAS', 'icon' => 'ventas.png', 'route' => '#', 'perm' => 'ver-ventas', 'dev' => true],
                                ['name' => 'SOPORTE', 'icon' => 'soporte.png', 'route' => 'soporte', 'perm' => 'ver-soporte', 'dev' => false],
                                ['name' => 'ENVÍOS', 'icon' => 'envios.png', 'route' => '#', 'perm' => 'ver-envios', 'dev' => true],
                            ];

                            usort($apps, function ($a, $b) {
                                $hasAccessA = isset($a['role']) ? auth()->user()->hasRole($a['role']) :
                                            (isset($a['perm']) ? auth()->user()->can($a['perm']) : true);
                                $hasAccessB = isset($b['role']) ? auth()->user()->hasRole($b['role']) :
                                            (isset($b['perm']) ? auth()->user()->can($b['perm']) : true);
                                return $hasAccessB <=> $hasAccessA;
                            });
                            
                        @endphp

                        @foreach($apps as $app)
                            @php
                                $hasAccess = isset($app['role']) ? auth()->user()->hasRole($app['role']) :
                                            (isset($app['perm']) ? auth()->user()->can($app['perm']) : true);
                            @endphp

                            <div class="flex flex-col h-full">
                                @if(!$hasAccess)
                                    <div class="flex-1 bg-gray-100 dark:bg-gray-700 rounded-lg p-3 flex flex-col items-center justify-center border border-gray-200 dark:border-gray-600 opacity-75">
                                        <div class="relative w-full flex justify-end">
                                            <i class="fas fa-lock text-gray-500 dark:text-gray-400 text-xs"></i>
                                        </div>
                                @else
                                    <a href="{{ $app['route'] != '#' ? route($app['route']) : '#' }}"
                                       onclick="{{ $app['route'] != '#' ? "showLoadingAlert('".route($app['route'])."', '".$app['name']."')" : 'showDevAlert(event)' }}"
                                       class="flex-1 bg-white dark:bg-gray-700 rounded-lg p-3 flex flex-col items-center justify-center border border-gray-200 dark:border-gray-600 hover:border-blue-400 dark:hover:border-blue-500 transition-colors">
                                @endif

                                    <div class="h-14 w-14 mb-2 flex items-center justify-center">
                                        <img src="/images/{{ $app['icon'] }}" alt="{{ $app['name'] }}" class="h-full w-full object-contain opacity-90 hover:opacity-100 transition-opacity">
                                    </div>
                                    <div class="text-center">
                                        <h3 class="text-xs font-medium text-gray-700 dark:text-gray-300 leading-tight">{{ $app['name'] }}</h3>
                                        @if($app['dev'] && $hasAccess)
                                            <span class="inline-block mt-1 px-1.5 py-0.5 text-[0.6rem] font-medium rounded-full bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                                                EN DESARROLLO
                                            </span>
                                        @endif
                                    </div>

                                @if(!$hasAccess)
                                    </div>
                                @else
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Función para apps en desarrollo
        function showDevAlert(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Aplicación en desarrollo',
                text: 'Esta funcionalidad está actualmente en construcción y estará disponible pronto.',
                icon: 'info',
                background: '#1e1e2f',
                color: '#fff',
                confirmButtonColor: '#3b82f6',
                confirmButtonText: 'Entendido'
            });
        }

        // Función para mostrar loading
        function showLoadingAlert(route, appName) {
            Swal.fire({
                title: `Abriendo ${appName}`,
                html: `<div class="flex flex-col items-center">
                         <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-blue-500 mb-3"></div>
                         <p class="text-gray-300 text-sm">Cargando aplicación...</p>
                       </div>`,
                background: '#1e1e2f',
                color: '#fff',
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    setTimeout(() => {
                        window.location.href = route;
                    }, 300);
                }
            });
        }

        // Función para verificar el estado del bloqueo de precios
        function checkBloqueoP() {
            fetch('{{ route("check.bloqueo.p") }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                const alertElement = document.getElementById('preciosAlert');
                if (data.showAlert) {
                    alertElement.classList.remove('hidden');
                    alertElement.classList.add('block');
                } else {
                    alertElement.classList.remove('block');
                    alertElement.classList.add('hidden');
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Verificar cada minuto (60000 ms)
        setInterval(checkBloqueoP, 60000);

        // Verificar al cargar la página
        document.addEventListener('DOMContentLoaded', checkBloqueoP);

        // Cierra cualquier SweetAlert2 activo al navegar con el back del navegador
        window.addEventListener('popstate', () => {
            if (Swal.isVisible()) {
                Swal.close();
            }
        });

        // También cierra la alerta si la página se restaura desde el bfcache
        window.addEventListener('pageshow', (event) => {
            if (event.persisted || window.performance.navigation.type === 2) {
                if (Swal.isVisible()) {
                    Swal.close();
                }
            }
        });
    </script>
</x-app-layout>
