<?php

// Incluir configuración de la base de datos
$conn = require '../config/db.php';

// Inicializar respuesta
$response = [
    'success' => false,
    'message' => ''
];

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Obtener datos del formulario
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    
    // Validar que los campos no estén vacíos
    if (empty($username) || empty($email) || empty($password)) {
        $response['message'] = 'Todos los campos son obligatorios.';
        http_response_code(400);
    } 
    // Validar formato de email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'El formato del email no es válido.';
        http_response_code(400);
    }
    // Validar longitud de contraseña
    elseif (strlen($password) < 6) {
        $response['message'] = 'La contraseña debe tener al menos 6 caracteres.';
        http_response_code(400);
    }
    else {
        // Encriptar la contraseña
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Preparar consulta SQL usando Prepared Statement
        $stmt = $conn->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
        
        if (!$stmt) {
            $response['message'] = 'Error al preparar la consulta: ' . $conn->error;
            http_response_code(500);
        } else {
            // Vincular parámetros (s = string)
            $stmt->bind_param('sss', $username, $email, $hashed_password);
            
            // Ejecutar la consulta
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Registro exitoso. Redirigiendo...';
                http_response_code(200);
                
                // Redirigir a index.html después de 2 segundos
                header('Refresh: 2; url=index.html');
                
            } else {
                // Manejar errores específicos
                if ($conn->errno === 1062) {
                    $response['message'] = 'El usuario o email ya está registrado.';
                } else {
                    $response['message'] = 'Error al registrar el usuario: ' . $stmt->error;
                }
                http_response_code(400);
            }
            
            // Cerrar statement
            $stmt->close();
        }
    }
    
    // Cerrar conexión
    $conn->close();
    
    // Retornar respuesta JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    
} else {
    $response['message'] = 'Método de solicitud no permitido.';
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode($response);
}
