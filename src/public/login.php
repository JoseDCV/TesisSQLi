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
        
        // VULNERABILIDAD INTENCIONAL PARA TESIS: Concatenación directa sin preparar sentencias
        // Bypass clásico de autenticación: admin' OR '1'='1
        $query = "SELECT id, username FROM users WHERE username = '$username' AND password = '$password'";
        
        // Ejecutamos la consulta directamente
        $result = $conn->query($query);
        
        if (!$result) {
            $response['message'] = 'Error en la sintaxis SQL (Posible inyección): ' . $conn->error;
            http_response_code(500);
        } else {
            
            // Verificar si el usuario y contraseña coinciden (o si la inyección forzó un resultado True)
            if ($result->num_rows > 0) {
                
                // Obtener el primer resultado devuelto
                $user = $result->fetch_assoc();
                
                // Iniciar sesión confiando ciegamente en el resultado de la consulta SQL
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                $response['success'] = true;
                $response['message'] = 'Login exitoso (Consulta SQL verdadera). Redirigiendo...';
                
            } else {
                // Credenciales incorrectas y no hubo inyección exitosa
                $response['message'] = 'Username o contraseña incorrectos.';
            }
        }
    }
    
    // Cerrar conexión
    $conn->close();
    
    // Renderear HTML en lugar de JSON para inyectar el modal de forma directa
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Autenticando...</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>body { background-color: #0f172a; color: #f8fafc; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }</style>
    </head>
    <body>
        <script>
            <?php if ($response['success']): ?>
            Swal.fire({
                title: '¡Vulnerabilidad Explotada!',
                html: '<p style="margin-bottom: 15px;">Has logrado un Bypass de Autenticación.</p><p style="font-family: monospace; font-size: 1.2rem; background: #1e293b; color: #10b981; padding: 15px; border-radius: 8px;">FLAG{sqli_bypass_auth}</p>',
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#475569',
                confirmButtonText: 'Ir a Validar Flag <i class="fas fa-arrow-right"></i>',
                cancelButtonText: 'Ir al Dashboard'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'validator.php';
                } else {
                    window.location.href = 'dashboard.php';
                }
            });
            <?php else: ?>
            Swal.fire({
                title: 'Error de Autenticación',
                text: '<?php echo addslashes($response['message']); ?>',
                icon: 'error',
                confirmButtonColor: '#ef4444'
            }).then(() => {
                window.location.href = 'auth.html';
            });
            <?php endif; ?>
        </script>
    </body>
    </html>
    <?php
    exit;
} else {
    header('Location: auth.html');
    exit;
}
