<?php

$host = '10.10.10.100'; 
$port = 1433;
$dbname = 'GCINTER';
$username = 'infiwin';
$password = 'infiwin';
$dsn = "sqlsrv:Server=$host,$port;Database=$dbname";

try {
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if(isset($_POST['productCodes'])) {
        $productCodes = explode(',', $_POST['productCodes']); 
        foreach ($productCodes as $code) {
           
            $stmt = $conn->prepare("UPDATE REG00501 SET B_SURTIDO = 1 WHERE NUMERO = :numero");
            $stmt->bindParam(':numero', $code);
            $stmt->execute();
        }
        echo "Actualización completada para los productos: " . implode(', ', $productCodes);
    }
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>
