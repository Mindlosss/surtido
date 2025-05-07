<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 leading-tight">
                Mis Tickets
            </h2>
        </div>
    </x-slot>

    <div class="py-7">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <!-- Mensaje de usuario sin permisos avanzados -->
                <p class="mb-4 text-gray-600 dark:text-gray-400">
                    Aquí puedes consultar y gestionar únicamente los tickets que tú has creado.
                </p>

                <!-- Tabla simplificada de tickets -->
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto dark:text-gray-200">
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
                            <!-- Ticket de ejemplo -->
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">101</td>
                                <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">Fallo al cargar perfil</td>
                                <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-yellow-200 text-yellow-800 dark:bg-yellow-600 dark:text-yellow-100">
                                        En Progreso
                                    </span>
                                </td>
                                <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">2025-05-05</td>
                                <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-center">
                                    <a href="#" class="text-blue-600 hover:underline">Ver</a>
                                </td>
                            </tr>
                            <!-- Otro ticket de ejemplo -->
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">102</td>
                                <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">Solicitud de cambio de contraseña</td>
                                <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-green-200 text-green-800 dark:bg-green-600 dark:text-green-100">
                                        Abierto
                                    </span>
                                </td>
                                <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">2025-05-06</td>
                                <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-center">
                                    <a href="#" class="text-blue-600 hover:underline">Ver</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="text-center text-gray-500 dark:text-gray-400 py-4">No hay más tickets.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
