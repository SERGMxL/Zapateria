<?php
// Configuración de la base de datos
$host = 'localhost';
$dbname = 'tienda_zapatos';
$username = 'root';
$password = '';

// Configuración de conexión PDO
try {
    // Crear la conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Configurar el manejo de errores para PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Establecer el modo de caracteres a UTF-8
    
    $pdo->exec("SET NAMES 'utf8'");

} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Modo de desarrollo o producción
$modoDesarrollo = true;

if ($modoDesarrollo) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    ini_set('log_errors', 1);
    ini_set('error_log', '../logs/errores.log'); // Registro de errores en el directorio logs
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Incluir FPDF para la generación de PDFs (si es necesario en tu sistema)
require_once(__DIR__ . '/../libs/fpdf.php');
?>
