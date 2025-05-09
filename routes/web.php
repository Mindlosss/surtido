<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ConteoControlador;
use App\Http\Controllers\GestorControlador;
use App\Http\Controllers\MercadoController;
use App\Http\Controllers\SurtidoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\CotizadorController;
use App\Http\Controllers\ComparadorController;
use App\Http\Controllers\SoporteController;

// RUTA PRINCIPAL
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/check-bloqueo-p', [DashboardController::class, 'checkBloqueoP'])->name('check.bloqueo.p');
});

// RUTAS PARA MERCADO (Solo usuarios con permiso 'ver-mercado')
Route::middleware(['auth', 'can:ver-mercado'])->group(function () {
    Route::get('/mercado', [MercadoController::class, 'index'])->name('mercado');
    Route::get('/mercado/fetch', [MercadoController::class, 'fetchPedidos'])->name('mercado.fetch');
    Route::post('/marcar-como-surtido', [MercadoController::class, 'marcarComoSurtido'])->name('mercado.marcarComoSurtido');
});

// RUTAS PARA SURTIDO (Solo usuarios con permiso 'ver-surtido')
Route::middleware(['auth', 'can:ver-surtido'])->group(function () {
    Route::get('/surtido', [SurtidoController::class, 'index'])->name('surtido');
    Route::get('/surtido/fetch', [SurtidoController::class, 'fetchPedidos'])->name('surtido.fetch');
    Route::post('/surtido/marcar-como-surtido', [SurtidoController::class, 'marcarComoSurtido'])->name('surtido.marcarComoSurtido');
    Route::get('surtido/buscar', [SurtidoController::class, 'buscar'])->name('surtido.buscar');
});

// RUTAS PARA EL GESTOR (Solo usuarios con permiso 'ver-gestor')
Route::middleware(['auth', 'can:ver-gestor'])->group(function () {
    Route::get('/gestor', [GestorControlador::class, 'index'])->name('gestor');
    Route::get('/gestor/ubicaciones/{conteo_id}', [GestorControlador::class, 'getUbicaciones'])->name('gestor.ubicaciones');
    Route::get('/gestor/anaqueles/{ubicacion_id}', [GestorControlador::class, 'getAnaqueles'])->name('gestor.anaqueles');
    Route::post('/gestor/productos', [GestorControlador::class, 'getProductos'])->name('gestor.productos');
    Route::post('/gestor/actualizar-segundo-conteo', [GestorControlador::class, 'actualizarSegundoConteo'])->name('gestor.actualizar-segundo-conteo');
    Route::post('/exportar', [GestorControlador::class, 'exportar'])->name('exportar');
    Route::post('/gestor/exportar-resumen', [GestorControlador::class, 'exportarResumen'])->name('gestor.exportar-resumen'); 
});

// RUTAS PARA INVENTARIO (Solo usuarios con permiso 'ver-inventario')
Route::middleware(['auth', 'can:ver-inventario'])->group(function () {
    Route::get('/inv', [ConteoControlador::class, 'index'])->name('inv');
    Route::resource('conteos', ConteoControlador::class)->except(['show']);
    Route::get('/conteos/{id}/ubicacion', [ConteoControlador::class, 'SeleccionarUbicacion'])->name('conteos.ubicacion');
    Route::get('/conteos/{id}/anaqueles/{ubicacion_id}', [ConteoControlador::class, 'showAnaqueles'])->name('conteos.showAnaqueles');
    Route::post('/conteos/{conteo_id}/storeAnaquel', [ConteoControlador::class, 'storeAnaquel'])->name('conteos.storeAnaquel');
    Route::post('/conteos/obtenerProducto', [ConteoControlador::class, 'obtenerProducto'])->name('conteos.obtenerProducto');
    Route::post('/productos/storeBarcode', [ConteoControlador::class, 'storeBarcode'])->name('productos.storeBarcode');
    Route::post('/conteos/validarSku', [ConteoControlador::class, 'validarSku'])->name('conteos.validarSku');
    Route::get('/conteos/{id}/segundo_conteo', [ConteoControlador::class, 'showSegundoConteo'])->name('conteos.segundo_conteo');
    Route::post('/guardar-cantidad/{id}', [ConteoControlador::class, 'storeCantidad'])->name('conteos.storeCantidad');
});

// RUTAS DE ADMINISTRACIÓN (Solo para 'admin')
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/users', [AdminController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [AdminController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [AdminController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{user}', [AdminController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');

    Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('admin.roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('admin.roles.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('admin.roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('admin.roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');

    Route::get('/permissions', [PermissionController::class, 'index'])->name('admin.permissions.index');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('admin.permissions.create');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('admin.permissions.store');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('admin.permissions.destroy');
});

// RUTAS DE COTIZADOR
Route::middleware(['auth', 'can:ver-cotizador'])->group(function () {
    Route::get('/cotizador', [CotizadorController::class, 'index'])->name('cotizador');
    Route::get('/cotizador/search', [CotizadorController::class, 'search'])->name('cotizador.search');
    Route::get('/cotizador/filtros', [CotizadorController::class, 'getFiltros']);
});

// RUTAS DE COMPARADOR
Route::middleware(['auth', 'can:ver-comparador'])->group(function () {
    Route::get('/comparador', [ComparadorController::class, 'index'])->name('comparador');
    Route::post('/comparador/comparar', [ComparadorController::class, 'comparar'])->name('comparador.comparar');
});

// RUTAS DE SOPORTE
Route::middleware(['auth','can:ver-soporte'])->group(function(){
    Route::get('/soporte', [SoporteController::class,'index'])->name('soporte');
    Route::post('/soporte/tickets', [SoporteController::class,'store'])->name('soporte.tickets.store');
    Route::put('/soporte/tickets/{ticket}', [SoporteController::class,'update'])->name('soporte.tickets.update');
    Route::get('/soporte/fetch', [SoporteController::class,'fetch'])->name('soporte.fetch');
});

// RUTAS DE PERFIL
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// RUTAS DE AUTENTICACIÓN
require __DIR__ . '/auth.php';