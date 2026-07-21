<?php
session_start();
// RETO 3 CTF: Blind SQLi (Boolean-based y Time-based)
/*
 * ==============================================================================
 * COMENTARIO ACADÉMICO PARA TESIS:
 * Este nivel presenta el escenario más complejo de Inyección SQL: Blind SQLi.
 * A diferencia de los retos anteriores, aquí no existe un "canal de salida" directo.
 * La aplicación NUNCA muestra los datos de la base de datos en la pantalla,
 * y los errores de SQL son suprimidos deliberadamente.
 * 
 * El atacante se ve obligado a utilizar técnicas de inferencia:
 * 1. Boolean-based: Realizar preguntas de "Verdadero o Falso" (ej. AND 1=1) y observar 
 *    si la aplicación responde con el mensaje de "éxito" o "fallo".
 * 2. Time-based: Inyectar funciones como SLEEP() (ej. AND SLEEP(5)) y medir 
 *    el tiempo de respuesta del servidor web para confirmar la ejecución.
 * ==============================================================================
 */

// Deshabilitar el límite de tiempo de ejecución de PHP para permitir ataques Time-based con SLEEP()
set_time_limit(0);

$conn = require '../config/db.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: auth.html');
    exit();
}

$username = $_SESSION['username'];
$verification_result = null;
$search_query = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search_query = isset($_POST['employee_id']) ? $_POST['employee_id'] : '';

    if (!empty($search_query)) {
        // VULNERABILIDAD AQUI: Concatenación directa sin preparación ni escape.
        $query = "SELECT id FROM users WHERE username = '$search_query'";
        
        try {
            $result = $conn->query($query);
            
            if (!$result) {
                // Lanzamos excepción si la consulta falla sintácticamente
                throw new Exception($conn->error);
            }
            
            // Lógica de respuesta binaria
            if ($result->num_rows > 0) {
                $verification_result = "success";
            } else {
                $verification_result = "fail";
            }
        } catch (Exception $e) {
            // SUPRESIÓN DE ERRORES INTENCIONAL:
            // Al ocultar los errores sintácticos de SQL, bloqueamos el ataque Error-based.
            // Si la consulta falla (ej. por comilla desbalanceada), mostramos el 
            // mismo mensaje genérico de "fallo" que mostraríamos si el usuario no existiera.
            $verification_result = "fail";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>SecurityPro - Verificación de Identidad</title>
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css?family=Roboto|Inter|Orbitron" rel="stylesheet" />
    <style>
        body {
            background-color: #0f172a;
            color: #f8fafc;
            font-family: 'Inter', -apple-system, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #1e293b;
            border-bottom: 1px solid #334155;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 100;
        }

        .navbar-brand {
            font-size: 22px;
            font-weight: 700;
            color: #3b82f6;
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .main-content {
            position: relative;
            z-index: 1;
            padding: 100px 40px 50px;
            max-width: 600px;
            margin: 0 auto;
        }

        h1 {
            font-weight: 700;
            color: #f8fafc;
            margin-bottom: 10px;
            font-size: 28px;
            letter-spacing: -0.5px;
            text-align: center;
        }

        .subtitle {
            color: #94a3b8;
            margin-bottom: 40px;
            font-size: 15px;
            text-align: center;
        }

        .verification-card {
            background: #1e293b;
            border-radius: 12px;
            border: 1px solid #334155;
            padding: 40px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #cbd5e1;
            font-size: 14px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #334155;
            background: #0f172a;
            color: #f8fafc;
            font-size: 15px;
            outline: none;
            transition: border-color 0.2s;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: #3b82f6;
        }

        .btn-verify {
            width: 100%;
            background: #3b82f6;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: background 0.2s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .btn-verify:hover {
            background: #2563eb;
        }

        .result-box {
            margin-top: 30px;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .result-success {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #4ade80;
        }

        .result-fail {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
        }

        .result-icon {
            font-size: 32px;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: transparent;
            color: #94a3b8;
            padding: 8px 0;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 25px;
            transition: color 0.2s;
        }

        .btn-back:hover {
            color: #f8fafc;
        }
        
        .logout-btn {
            color: #ef4444;
            font-weight: 500;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
            margin-left: 15px;
        }

        .logout-btn:hover {
            color: #f81111;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <a href="dashboard.php" class="navbar-brand"><i class="fas fa-shield-alt"></i> SecurityPro</a>
        <div>
            <span style="margin-right:20px; color:#aaa;">Operador: <strong style="color:#fff;"><?php echo htmlspecialchars($username); ?></strong></span>
            <a href="logout.php" class="logout-btn"><i class="fas fa-power-off"></i> Disconnect</a>
        </div>
    </nav>

    <div class="main-content">
        <a href="dashboard.php" class="btn-back"><i class="fas fa-chevron-left"></i> Retornar al Dashboard</a>

        <h1>Módulo de Verificación</h1>
        <p class="subtitle">Herramienta interna para validar la existencia de identidades corporativas.</p>

        <div class="verification-card">
            <form action="verify_user.php" method="POST">
                <div class="form-group">
                    <label class="form-label" for="employee_id"><i class="fas fa-id-badge"></i> Nombre de Usuario / ID de Empleado</label>
                    <input type="text" id="employee_id" name="employee_id" class="form-control" placeholder="Ingrese el identificador..." value="<?php echo htmlspecialchars($search_query); ?>" required autocomplete="off">
                </div>
                
                <button type="submit" class="btn-verify">
                    <i class="fas fa-check-circle"></i> Verificar Identidad
                </button>
            </form>

            <?php if ($verification_result === 'success'): ?>
                <div class="result-box result-success">
                    <i class="fas fa-check-circle result-icon"></i>
                    <div>
                        <strong>VERIFICACIÓN POSITIVA</strong><br>
                        El usuario existe actualmente en los registros del sistema.
                    </div>
                </div>
            <?php elseif ($verification_result === 'fail'): ?>
                <div class="result-box result-fail">
                    <i class="fas fa-times-circle result-icon"></i>
                    <div>
                        <strong>VERIFICACIÓN NEGATIVA</strong><br>
                        No se ha encontrado ningún usuario con ese identificador.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php 
    // Detectar si hubo un ataque Blind exitoso dirigido a exfiltrar el esquema de la base de datos
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($search_query)) {
        
        // El reto solo se considera superado si:
        // 1. La consulta no falla y la condición inyectada evalúa a TRUE ($verification_result === 'success')
        // 2. El payload utiliza la función SUBSTRING (o equivalente) apuntando a DATABASE()
        $is_exfiltrating_db = preg_match('/(SUBSTRING|SUBSTR|MID)\s*\(\s*(DATABASE|SCHEMA)\s*\(\)/i', $search_query);
        
        if ($verification_result === 'success' && $is_exfiltrating_db) {
            echo "<script>
            Swal.fire({
                title: '¡Inferencia Blind Exitosa!',
                html: '<p style=\"margin-bottom: 15px;\">Has logrado inferir de forma correcta caracteres del nombre de la base de datos mediante comprobaciones lógicas (Blind SQLi).</p><p style=\"font-family: monospace; font-size: 1.2rem; background: #1e293b; color: #10b981; padding: 15px; border-radius: 8px;\">FLAG{blind_boolean_inferential_db}</p>',
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#475569',
                confirmButtonText: 'Ir a Validar Flag <i class=\"fas fa-arrow-right\"></i>',
                cancelButtonText: 'Continuar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'validator.php';
                }
            });
            </script>";
        }
    }
    ?>
</body>
</html>
