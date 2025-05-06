<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $conteo->nombre }}
            <link href="{{ asset('css/estilos.css') }}" rel="stylesheet">
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center">
                        <h1>Seleccionar Ubicación</h1>
                        <a href="{{ route('inv') }}" class="btn-comun">Regresar</a>
                    </div>
                    <div class="grid_ubicaciones">
                        @foreach ($ubicaciones as $ubicacion)
                        <a href="{{ route('conteos.showAnaqueles', ['id' => $conteo->id, 'ubicacion_id' => $ubicacion->id]) }}" class="conteo-card">
                                <div class="flex flex-col justify-between items-center">
                                    <span class="text-lg font-medium">{{ $ubicacion->nombre }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
