<?php
// Archivo: config/db.php
// Configuración para la conexión a la base de datos MySQL

// --- Parámetros de Conexión ---
// Asegúrate de reemplazar 'TU_HOST' con el host de tu base de datos
// si no es 'localhost' (por ejemplo, el que te proporcione tu proveedor de hosting).
define('DB_HOST', 'localhost'); // O el host que te haya dado tu proveedor
define('DB_NAME', 'gastos'); // Corregido según tu última información
define('DB_USER', 'root');
define('DB_PASS', ''); // ¡Ten cuidado con exponer contraseñas en repositorios públicos!
define('DB_CHARSET', 'utf8mb4');

// --- Opciones de PDO ---
// PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION: Lanza excepciones en caso de errores SQL.
// PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC: Devuelve los resultados como arrays asociativos.
// PDO::ATTR_EMULATE_PREPARES => false: Desactiva la emulación de sentencias preparadas para mayor seguridad.
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// --- Data Source Name (DSN) ---
// Define cómo PDO se conectará a la base de datos.
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

/**
 * Establece y devuelve una conexión PDO a la base de datos.
 *
 * @return PDO Una instancia de PDO en caso de éxito.
 * @throws PDOException Si la conexión falla.
 */
function getPDOConnection() {
    global $dsn, $options; // Accede a las variables globales $dsn y $options
    // La declaración 'global' debe estar dentro de la función si las variables se definieron fuera.
    // Sin embargo, para $dsn, DB_USER, DB_PASS, $options, es mejor pasarlas como parámetros
    // o acceder a ellas directamente si están en el mismo ámbito o son constantes.
    // Por simplicidad y consistencia con el código anterior, mantenemos global por ahora,
    // pero considera refactorizar esto en el futuro.

    // Re-obteniendo las variables en caso de que el 'global' no funcione como se espera en todos los contextos.
    // Esto es redundante si 'global' funciona, pero es una salvaguarda.
    $current_dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $current_user = DB_USER;
    $current_pass = DB_PASS;
    $current_options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        // Usamos las variables re-obtenidas para asegurar que tienen los valores correctos aquí.
        $pdo = new PDO($current_dsn, $current_user, $current_pass, $current_options);
        return $pdo;
    } catch (PDOException $e) {
        // En un entorno de producción, no deberías mostrar errores detallados al usuario.
        // En su lugar, registra el error y muestra un mensaje genérico.
        error_log("Error de conexión a la BD: " . $e->getMessage()); // Registra el error en el log del servidor
        // Para propósitos de depuración con test_conexion.php, relanzamos la excepción
        // para que sea capturada y mostrada por el script de prueba.
        throw new PDOException($e->getMessage(), (int)$e->getCode());
    }
}

// NO HAY CÓDIGO DE EJEMPLO DE USO AQUÍ PARA EVITAR LLAMADAS PREMATURAS.
// El archivo config/db.php solo debe definir la configuración y la función.
// La llamada a getPDOConnection() se hará desde otros scripts que incluyan este.
?>
