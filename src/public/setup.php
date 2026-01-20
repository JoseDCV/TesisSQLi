<?php

// Configuración de conexión para instalación
$host = '127.0.0.1';
$user = 'root';
$password = '';
$db_name = 'tesis_sqli';

// Conectar a MySQL sin especificar base de datos
$conn = new mysqli($host, $user, $password);

// Verificar conexión
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

// Configurar charset
$conn->set_charset('utf8mb4');

echo "<h2>Instalación de Base de Datos</h2>";
echo "<pre>";

// Crear base de datos si no existe
$sql_create_db = "CREATE DATABASE IF NOT EXISTS $db_name";
if ($conn->query($sql_create_db) === TRUE) {
    echo "✓ Base de datos '$db_name' creada o ya existe.\n";
} else {
    die("✗ Error al crear la base de datos: " . $conn->error);
}

// Seleccionar la base de datos
if (!$conn->select_db($db_name)) {
    die("✗ Error al seleccionar la base de datos: " . $conn->error);
}

// Crear tabla users si no existe
$sql_create_table = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
";

if ($conn->query($sql_create_table) === TRUE) {
    echo "✓ Tabla 'users' creada o ya existe.\n";
} else {
    die("✗ Error al crear la tabla: " . $conn->error);
}

// Verificar que la tabla fue creada correctamente
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows > 0) {
    echo "✓ Tabla 'users' verificada exitosamente.\n";
}

// Mostrar estructura de la tabla
$result = $conn->query("DESCRIBE users");
echo "\n--- Estructura de la tabla 'users' ---\n";
while ($row = $result->fetch_assoc()) {
    echo "Campo: {$row['Field']} | Tipo: {$row['Type']} | Null: {$row['Null']} | Key: {$row['Key']}\n";
}

echo "\n✓✓✓ Base de datos instalada correctamente ✓✓✓\n";
echo "\nPuedes proceder a usar el sistema de registro y login.\n";
echo "</pre>";

// Cerrar conexión
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Completado</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background-color: #0a0a0a;
            color: #00ff00;
            padding: 20px;
        }
        h2 {
            color: #00ccff;
        }
        pre {
            background-color: #1a1a1a;
            border: 1px solid #00ff00;
            padding: 20px;
            border-radius: 5px;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: linear-gradient(90deg, #00ff00, #00ccff);
            color: #000;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        a:hover {
            box-shadow: 0 0 15px #00ff00;
        }
    </style>
</head>
<body>
    <a href="auth.html">→ Ir a Registro/Login</a>
    <a href="index.html">→ Ir al Inicio</a>
</body>
</html>