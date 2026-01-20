<?php

// Obtener credenciales de la base de datos desde variables de entorno o usar valores por defecto
$host = getenv('DB_HOST') ?: '127.0.0.1';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$db_name = getenv('DB_NAME') ?: 'tesis_sqli';

// Crear conexión a la base de datos
$conn = new mysqli($host, $user, $password, $db_name);

// Verificar si la conexión fue exitosa
if ($conn->connect_error) {
    die('Error de conexión a la base de datos: ' . $conn->connect_error);
}

// Configurar el charset a UTF-8
$conn->set_charset('utf8mb4');

// Retornar la conexión
return $conn;
