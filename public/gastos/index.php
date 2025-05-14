<?php
// Archivo: index.php
// Este será el punto de entrada principal de la aplicación.
// Mostrará el dashboard por defecto.



// Aquí podríamos tener lógica para cargar diferentes "vistas" o "módulos"
// basados en parámetros GET, pero para empezar, cargaremos directamente el contenido del dashboard.

// Para cargar el contenido del dashboard, en lugar de hacer un require_once de todo el archivo
// dashboard_vista.php (que a su vez haría require_once del header y footer de nuevo),
// lo ideal sería tener el contenido específico del dashboard en un archivo separado
// o refactorizar dashboard_vista.php para que pueda ser incluido sin duplicar layouts.

// Solución simple por ahora: Redirigir a dashboard_vista.php
// Esto es más limpio que tratar de incluir contenido que ya tiene su propio layout.
// El usuario verá dashboard_vista.php en la URL.
if (basename($_SERVER['PHP_SELF']) == 'index.php') {
    header('Location: dashboard_vista.php');
    exit;
}

$page_title = "Dashboard"; // Definimos el título para el header
require_once 'layout_header.php'; // Incluimos la cabecera y la barra lateral

// Si en el futuro quieres un enfoque de Single Page Application (SPA) más real con PHP,
// necesitarías cargar el contenido de las vistas (sin su propio header/footer) aquí
// usando AJAX o una estructura de plantillas más compleja.

// Como estamos redirigiendo, el footer no se llegará a incluir desde aquí.
// require_once 'layout_footer.php'; 
?>
