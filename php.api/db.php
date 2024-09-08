<?php
// Configuración de la base de datos
$host = "sql204.infinityfree.com";  // Cambia por tu host de MySQL
$dbname = "if0_37263671_gestion_datos";  // Nombre de tu base de datos
$username = "if0_37263671";  // Nombre de usuario MySQL
$password = "3V2B4dgzioNS";  // Contraseña de MySQL

// Crear conexión
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} else {
    echo "Conexión exitosa a la base de datos.";
}
?>