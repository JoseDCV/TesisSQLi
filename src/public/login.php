<?php

// Iniciar sesión
session_start();

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
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    
    // Validar que los campos no estén vacíos
    if (empty($username) || empty($password)) {
        $response['message'] = 'Username y contraseña son obligatorios.';
        http_response_code(400);
    } else {
        
        // Preparar consulta SQL para buscar el usuario
        $stmt = $conn->prepare('SELECT id, username, password FROM users WHERE username = ?');
        
        if (!$stmt) {
            $response['message'] = 'Error al preparar la consulta: ' . $conn->error;
            http_response_code(500);
        } else {
            
            // Vincular parámetro
            $stmt->bind_param('s', $username);
            
            // Ejecutar la consulta
            if (!$stmt->execute()) {
                $response['message'] = 'Error al ejecutar la consulta: ' . $stmt->error;
                http_response_code(500);
            } else {
                
                // Obtener resultado
                $result = $stmt->get_result();
                
                // Verificar si el usuario existe
                if ($result->num_rows === 1) {
                    
                    // Obtener datos del usuario
                    $user = $result->fetch_assoc();
                    
                    // Verificar la contraseña
                    if (password_verify($password, $user['password'])) {
                        
                        // Contraseña correcta - Iniciar sesión
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        
                        $response['success'] = true;
                        $response['message'] = 'Login exitoso. Redirigiendo...';
                        http_response_code(200);
                        
                        // Redirigir al dashboard después de 1.5 segundos
                        header('Refresh: 1.5; url=dashboard.php');
                        
                    } else {
                        // Contraseña incorrecta
                        $response['message'] = 'Username o contraseña incorrectos.';
                        http_response_code(401);
                    }
                    
                } else {
                    // Usuario no encontrado
                    $response['message'] = 'Username o contraseña incorrectos.';
                    http_response_code(401);
                }
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
