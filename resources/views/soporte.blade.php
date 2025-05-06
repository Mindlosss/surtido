{{-- resources/views/soporte.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 leading-tight">
                Gestión de Tickets de Soporte
            </h2>
            {{-- Botón para Crear Nuevo Ticket --}}
            <a href="#" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                Crear Nuevo Ticket
            </a>
        </div>
    </x-slot>

    <div class="py-7">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6"> 

                    {{-- Sección de Filtros y Búsqueda --}}
                    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        {{-- Búsqueda --}}
                        <div class="relative flex-grow">
                            <label for="table-search" class="sr-only">Buscar</label>
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                            </div>
                            <input type="text" id="table-search" class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-300 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Buscar ticket por ID, asunto...">
                        </div>

                        {{-- Filtros Dropdown --}}
                        <div class="flex items-center gap-4">
                            <div>
                                <label for="filter-status" class="sr-only">Filtrar por Estado</label>
                                <select id="filter-status" class="border border-gray-300 dark:border-gray-600 rounded-md py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    <option selected>Estado (Todos)</option>
                                    <option value="abierto">Abierto</option>
                                    <option value="en_progreso">En Progreso</option>
                                    <option value="resuelto">Resuelto</option>
                                    <option value="cerrado">Cerrado</option>
                                    <option value="pendiente">Pendiente</option>
                                </select>
                            </div>
                             <div>
                                <label for="filter-priority" class="sr-only">Filtrar por Prioridad</label>
                                <select id="filter-priority" class="border border-gray-300 dark:border-gray-600 rounded-md py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    <option selected>Prioridad (Todas)</option>
                                    <option value="baja">Baja</option>
                                    <option value="media">Media</option>
                                    <option value="alta">Alta</option>
                                    <option value="urgente">Urgente</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Tabla de Tickets --}}
                    <div class="overflow-x-auto relative shadow-md sm:rounded-lg border dark:border-gray-700">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3"># Ticket</th>
                                    <th scope="col" class="px-6 py-3">Asunto</th>
                                    <th scope="col" class="px-6 py-3">Estado</th>
                                    <th scope="col" class="px-6 py-3">Prioridad</th>
                                    <th scope="col" class="px-6 py-3">Asignado a</th>
                                    <th scope="col" class="px-6 py-3">Solicitante</th>
                                    <th scope="col" class="px-6 py-3">Creado</th>
                                    <th scope="col" class="px-6 py-3">Últ. Actualización</th>
                                    <th scope="col" class="px-6 py-3"><span class="sr-only">Acciones</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Fila de ejemplo 1 --}}
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        SYS-1024
                                    </th>
                                    <td class="px-6 py-4">
                                        Problema con impresora en Contabilidad
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                            Abierto
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                         <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                            Media
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        Juan Pérez
                                    </td>
                                    <td class="px-6 py-4">
                                        Ana García (Conta)
                                    </td>
                                    <td class="px-6 py-4">
                                        2025-05-01 09:15
                                    </td>
                                     <td class="px-6 py-4">
                                        2025-05-02 11:30
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline mr-3">Ver</a>
                                        <a href="#" class="font-medium text-indigo-600 dark:text-indigo-500 hover:underline">Editar</a>
                                    </td>
                                </tr>
                                {{-- Fila de ejemplo 2 --}}
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        SYS-1023
                                    </th>
                                    <td class="px-6 py-4">
                                        Instalación de software CAD
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                            En Progreso
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                            Alta
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        Maria López
                                    </td>
                                    <td class="px-6 py-4">
                                        Carlos Ruiz (Diseño)
                                    </td>
                                    <td class="px-6 py-4">
                                        2025-04-30 14:00
                                    </td>
                                     <td class="px-6 py-4">
                                        2025-05-01 16:45.
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline mr-3">Ver</a>
                                        <a href="#" class="font-medium text-indigo-600 dark:text-indigo-500 hover:underline">Editar</a>
                                    </td>
                                </tr>
                                 {{-- Fila de ejemplo 3 --}}
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        SYS-1022
                                    </th>
                                    <td class="px-6 py-4">
                                        Acceso VPN no funciona
                                    </td>
                                    <td class="px-6 py-4">
                                         <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-300">
                                            Resuelto
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                            Media
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        Juan Pérez
                                    </td>
                                    <td class="px-6 py-4">
                                        Luisa Fernández (Ventas)
                                    </td>
                                    <td class="px-6 py-4">
                                        2025-04-29 11:20
                                    </td>
                                     <td class="px-6 py-4">
                                        2025-04-30 10:05
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline mr-3">Ver</a>
                                        
                                    </td>
                                </tr>
                                

                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación (Simulada) --}}
                    <nav class="flex items-center justify-between pt-4" aria-label="Table navigation">
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                            Mostrando <span class="font-semibold text-gray-900 dark:text-white">1-10</span> de <span class="font-semibold text-gray-900 dark:text-white">100</span>
                        </span>
                        <ul class="inline-flex items-center -space-x-px">
                            <li>
                                <a href="#" class="block px-3 py-2 ml-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                    <span class="sr-only">Anterior</span>
                                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                </a>
                            </li>
                            <li>
                                <a href="#" aria-current="page" class="z-10 px-3 py-2 leading-tight text-blue-600 border border-blue-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white">1</a>
                            </li>
                            <li>
                                <a href="#" class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">2</a>
                            </li>
                            <li>
                                <a href="#" class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">...</a>
                            </li>
                             <li>
                                <a href="#" class="px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">10</a>
                            </li>
                            <li>
                                <a href="#" class="block px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                    <span class="sr-only">Siguiente</span>
                                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                </a>
                            </li>
                        </ul>
                    </nav>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>