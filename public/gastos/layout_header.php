<?php
if (!isset($page_title)) {
    $page_title = "Sistema de Gestión de Gastos"; 
}
$active_page = basename($_SERVER['PHP_SELF']); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Gestión de Gastos GC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .sidebar {
            transition: transform 0.3s ease-in-out;
        }
        .content-area { 
            transition: margin-left 0.3s ease-in-out;
        }
        .nav-link-active {
            background-color: #4A5568; /* Tailwind: bg-gray-700 */
            color: #ffffff;
            font-weight: 600; 
        }
        .nav-link-active i {
            color: #ffffff; 
        }
        main {
             padding-top: 5rem; 
        }
        @media (min-width: 768px) { 
            main {
                padding-top: 1.5rem; 
            }
        }
        .filtros-dashboard, .filtros-container, .form-container-gastos, .control-section { /* Añadida clase para control */
            display: flex;
            flex-wrap: wrap;
            gap: 1rem; 
            padding: 1.5rem; 
            background-color: #f9fafb; 
            border-radius: 0.5rem; 
            margin-bottom: 1.5rem; 
            align-items: flex-end; 
            border: 1px solid #e5e7eb; 
        }
        .filtro-grupo, .form-grupo-gastos, .control-grupo { /* Añadida clase para control */
            display: flex;
            flex-direction: column;
            min-width: 160px; 
            flex-grow: 1; 
        }
        .filtro-grupo label, .form-grupo-gastos label, .control-grupo label {
            margin-bottom: 0.5rem; 
            font-weight: 500; 
            font-size: 0.875rem; 
            color: #374151; 
        }
        .filtro-grupo select,
        .filtro-grupo input[type="date"],
        .filtro-grupo input[type="number"],
        .form-grupo-gastos select,
        .form-grupo-gastos input[type="date"],
        .form-grupo-gastos input[type="number"],
        .form-grupo-gastos input[type="text"],
        .form-grupo-gastos input[type="file"],
        .form-grupo-gastos textarea,
        .control-grupo input[type="text"], /* Para formularios de control */
        .control-grupo button {
            padding: 0.625rem 0.75rem; 
            border: 1px solid #d1d5db; 
            border-radius: 0.375rem; 
            font-size: 0.9rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); 
            background-color: #fff; 
        }
        .filtro-grupo button, 
        .form-grupo-gastos button[type="submit"],
        .control-grupo button { /* Estilo para botones de control */
            padding: 0.625rem 1rem;
            background-color: #4f46e5; 
            color: white;
            border: none;
            border-radius: 0.375rem; 
            cursor: pointer;
            font-weight: 500; 
            transition: background-color 0.2s;
            height: 2.75rem; 
            line-height: 1.5rem; 
        }
        .filtro-grupo button:hover, 
        .form-grupo-gastos button[type="submit"]:hover,
        .control-grupo button:hover {
            background-color: #4338ca; 
        }
        .control-section table { width: 100%; border-collapse: collapse; margin-top:1rem;}
        .control-section th, .control-section td { border: 1px solid #e5e7eb; padding: 0.75rem; text-align:left;}
        .control-section th { background-color: #f9fafb; }
        .control-section .action-btn { margin-right: 0.5rem; }

        .mensaje { padding: 1rem; margin-bottom: 1.5rem; border-width: 1px; border-radius: 0.375rem; text-align: center; }
        .mensaje-info { color: #0c5460; background-color: #d1ecf1; border-color: #bee5eb; }
        .mensaje-error { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }
        .mensaje-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }

    </style>
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="flex h-screen overflow-hidden">
        <aside class="sidebar bg-slate-800 text-slate-100 w-64 space-y-6 py-7 px-2 absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 z-30 shadow-xl">
            <a href="dashboard_vista.php" class="text-white flex items-center space-x-3 px-4 mb-8">
                <i class="fas fa-dollar-sign text-3xl text-emerald-400"></i>
                <span class="text-2xl font-bold">Gestión Gastos</span>
            </a>

            <nav class="mt-6">
                <a href="dashboard_vista.php" class="flex items-center py-3 px-4 rounded-lg transition duration-200 hover:bg-slate-700 hover:text-white <?php echo ($active_page == 'dashboard_vista.php') ? 'nav-link-active' : ''; ?>">
                    <i class="fas fa-tachometer-alt w-6 text-center mr-3"></i>Dashboard
                </a>
                <a href="captura_gasto.php" class="flex items-center py-3 px-4 rounded-lg transition duration-200 hover:bg-slate-700 hover:text-white <?php echo ($active_page == 'captura_gasto.php') ? 'nav-link-active' : ''; ?>">
                    <i class="fas fa-file-invoice-dollar w-6 text-center mr-3"></i>Registrar Gasto
                </a>
                <a href="listar_gastos.php" class="flex items-center py-3 px-4 rounded-lg transition duration-200 hover:bg-slate-700 hover:text-white <?php echo ($active_page == 'listar_gastos.php') ? 'nav-link-active' : ''; ?>">
                    <i class="fas fa-list-alt w-6 text-center mr-3"></i>Listar Gastos
                </a>
                <a href="resumen_gastos_vista.php" class="flex items-center py-3 px-4 rounded-lg transition duration-200 hover:bg-slate-700 hover:text-white <?php echo ($active_page == 'resumen_gastos_vista.php') ? 'nav-link-active' : ''; ?>">
                    <i class="fas fa-chart-pie w-6 text-center mr-3"></i>Resúmenes
                </a>
                <a href="control_catalogos.php" class="flex items-center py-3 px-4 rounded-lg transition duration-200 hover:bg-slate-700 hover:text-white <?php echo ($active_page == 'control_catalogos.php' || $active_page == 'gestionar_areas.php' || $active_page == 'gestionar_tarjetas.php' || $active_page == 'gestionar_plataformas.php') ? 'nav-link-active' : ''; ?>">
                    <i class="fas fa-sliders-h w-6 text-center mr-3"></i>Control Catálogos
                </a>
            </nav>
        </aside>

        <div class="content-area flex-1 flex flex-col overflow-y-auto">
            <header class="bg-white shadow-lg p-4 sticky top-0 md:relative z-20">
                <div class="flex justify-between items-center">
                    <button id="mobileMenuButton" class="text-gray-600 hover:text-gray-800 focus:outline-none md:hidden">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                    <h1 id="pageTitleHeader" class="text-xl md:text-2xl font-semibold text-slate-700 truncate">
                        <?php echo htmlspecialchars($page_title); ?>
                    </h1>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <button class="flex items-center space-x-2 focus:outline-none">
                                <img src="https://placehold.co/40x40/E2E8F0/4A5568?text=U" alt="[Avatar de Usuario]" class="w-9 h-9 rounded-full border-2 border-slate-300 shadow-sm">
                                <span class="text-slate-700 hidden sm:block font-medium">Usuario</span>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-4 md:p-6 bg-slate-50">
