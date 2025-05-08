<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 leading-tight">
            Sistema de Tickets
        </h2>
    </x-slot>

    <div class="py-7">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm dark:border-gray-700 p-6">
                <form method="GET" action="{{ route('soporte') }}" class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
                    <input
                        name="search"
                        value="{{ request('search') }}"
                        type="text"
                        placeholder="Buscar tickets..."
                        class="w-full sm:w-64 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 placeholder-gray-500 dark:placeholder-gray-400 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4 sm:mb-0"/>
                    <div class="flex space-x-2">
                        <select
                            name="status"
                            class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos</option>
                            <option value="Abierto" {{ request('status')=='Abierto'?'selected':'' }}>Abierto</option>
                            <option value="En Progreso" {{ request('status')=='En Progreso'?'selected':'' }}>En Progreso</option>
                            <option value="Cerrado" {{ request('status')=='Cerrado'?'selected':'' }}>Cerrado</option>
                        </select>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">
                            Filtrar
                        </button>
                        <a href="{{ route('soporte') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg shadow">
                            Limpiar
                        </a>
                    </div>
                </form>

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
                                <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-left">Actualizado</th>
                                <th class="px-4 py-2 border-b border-gray-200 dark:border-gray-600 text-left">Asignado a</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $t)
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">{{ $t->id }}</td>
                                    <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">{{ $t->asunto }}</td>
                                    <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">{{ $t->creador->name }}</td>
                                    <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">{{ $t->prioridad }}</td>
                                    <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">
                                        <form method="POST" action="{{ route('soporte.tickets.update', $t) }}">
                                            @csrf @method('PUT')
                                            <select name="estado" onchange="this.form.submit()"
                                                    class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-2 py-1 rounded-lg">
                                                @foreach(['Abierto','En Progreso','Cerrado'] as $e)
                                                    <option value="{{ $e }}" {{ $t->estado==$e?'selected':'' }}>{{ $e }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">{{ $t->created_at->format('Y-m-d') }}</td>
                                    <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">{{ $t->updated_at->format('Y-m-d') }}</td>
                                    <td class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">
                                        <form method="POST" action="{{ route('soporte.tickets.update', $t) }}">
                                            @csrf @method('PUT')
                                            <select name="asignado_a" onchange="this.form.submit()"
                                                    class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-2 py-1 rounded-lg">
                                                <option value="">—</option>
                                                @foreach($responsables as $r)
                                                    <option value="{{ $r }}" {{ $t->asignado_a==$r?'selected':'' }}>{{ $r }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            @if($tickets->isEmpty())
                                <tr><td colspan="8" class="text-center py-4">No hay tickets.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>