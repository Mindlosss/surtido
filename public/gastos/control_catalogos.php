<?php
// Archivo: control_catalogos.php
// Propósito: Página principal para la sección de administración de catálogos.

$page_title = "Control de Catálogos"; // Título para el encabezado del layout
require_once 'layout_header.php'; // Incluye la barra lateral, encabezado y estilos base
// No se necesita conexión a la base de datos para esta página principal,
// ya que solo contiene enlaces a otras páginas que sí la necesitarán.
?>

<div class="container mx-auto px-0 md:px-4 py-0">
    <p class="text-gray-600 mb-6 mt-2">Seleccione el catálogo que desea administrar:</p>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <a href="gestionar_areas.php" class="block p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 ease-in-out border border-gray-200 hover:border-indigo-500">
            <div class="flex items-center mb-3">
                <i class="fas fa-map-signs text-3xl text-indigo-500 mr-4"></i>
                <h2 class="text-xl font-semibold text-gray-700">Áreas de Uso</h2>
            </div>
            <p class="text-sm text-gray-500">Administrar las diferentes áreas donde se utilizan los recursos o se aplican los gastos.</p>
        </a>

        <a href="gestionar_tarjetas.php" class="block p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 ease-in-out border border-gray-200 hover:border-emerald-500">
            <div class="flex items-center mb-3">
                <i class="fas fa-credit-card text-3xl text-emerald-500 mr-4"></i>
                <h2 class="text-xl font-semibold text-gray-700">Tarjetas</h2>
            </div>
            <p class="text-sm text-gray-500">Administrar las tarjetas de pago utilizadas para las compras.</p>
        </a>

        <a href="gestionar_plataformas.php" class="block p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 ease-in-out border border-gray-200 hover:border-sky-500">
            <div class="flex items-center mb-3">
                <i class="fas fa-store text-3xl text-sky-500 mr-4"></i>
                <h2 class="text-xl font-semibold text-gray-700">Plataformas de Compra</h2>
            </div>
            <p class="text-sm text-gray-500">Administrar las plataformas o tiendas donde se realizan las adquisiciones.</p>
        </a>
        
        <a href="gestionar_tipos_gasto.php" class="block p-6 bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 ease-in-out border border-gray-200 hover:border-amber-500">
            <div class="flex items-center mb-3">
                <i class="fas fa-tags text-3xl text-amber-500 mr-4"></i>
                <h2 class="text-xl font-semibold text-gray-700">Tipos de Gasto</h2>
            </div>
            <p class="text-sm text-gray-500">Administrar las categorías o tipos de gastos.</p>
        </a>

    </div>
</div>

<?php
require_once 'layout_footer.php'; // Incluye el pie de página del layout
?>
