<?php
/**
 * ============================================================================
 * SCRIPT DE VALIDACIÓN DE FLAGS (CTF) - SECURITYPRO
 * ============================================================================
 * 
 * JUSTIFICACIÓN ACADÉMICA (METODOLOGÍA DSRM - CAPÍTULO IV):
 * Este script actúa como el artefacto final de evaluación dentro de la fase 
 * de "Demostración y Evaluación" (Demonstration and Evaluation) de la 
 * metodología Design Science Research Methodology (DSRM). 
 * 
 * Propósito en la Tesis:
 * 1. Consolidación de la Explotación: Permite verificar de manera empírica,
 *    segura e inequívoca que el investigador ha logrado extraer el dato 
 *    objetivo de la base de datos (exfiltración exitosa).
 * 2. Validación Binaria (Éxito/Fallo): Demuestra la integridad técnica del
 *    reto al evitar falsos positivos mediante el uso de la función 
 *    cripotográfica `hash_equals()`, mitigando teóricos "Timing Attacks".
 * 3. Auditoría e Integridad del Entorno: Implementa un registro persistente 
 *    (log file) en un volumen montado de Docker (`src/logs/validator.log`) 
 *    para trazar los eventos de validación. Esta bitácora asegura la 
 *    reproducibilidad y proporciona evidencia material (logs de auditoría) 
 *    requerida en la Matriz de Validación del informe final de la tesis.
 * 
 * ============================================================================
 */

session_start();

// Definición Estática Institucional de Flags (Target Secrets)
$VALID_FLAGS = [
    'FLAG{sqli_bypass_auth}',
    'FLAG{union_metadata_exfil_db}',
    'FLAG{blind_boolean_inferential_db}'
];

// Ruta del archivo de auditoría (Log de persistencia en volumen Docker)
$LOG_FILE = __DIR__ . '/../logs/validator.log';

/**
 * Registra los intentos de validación en el archivo log.
 * @param string $flagAttempt La flag ingresada por el usuario.
 * @param bool $success Resultado de la validación.
 */
function logAttempt($flagAttempt, $success) {
    global $LOG_FILE;
    $date = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN_IP';
    $status = $success ? '[SUCCESS]' : '[FAILED] ';
    
    // Sanitizar el input del log básico
    $safeFlag = htmlspecialchars(substr($flagAttempt, 0, 100)); 
    
    $logEntry = "{$date} | IP: {$ip} | STATUS: {$status} | FLAG_SUBMITTED: {$safeFlag}" . PHP_EOL;
    
    // Crear el directorio si no existe (aunque ya lo inicializamos)
    if (!is_dir(dirname($LOG_FILE))) {
        mkdir(dirname($LOG_FILE), 0777, true);
    }
    
    // Anexar al log (LOCK_EX para evitar condiciones de carrera)
    file_put_contents($LOG_FILE, $logEntry, FILE_APPEND | LOCK_EX);
}

// Procesar la solicitud POST (Validación AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];
    $input_data = json_decode(file_get_contents('php://input'), true);
    $submittedFlag = isset($input_data['flag']) ? trim($input_data['flag']) : '';
    $submittedDbName = isset($input_data['db_name']) ? trim($input_data['db_name']) : '';

    if (empty($submittedFlag)) {
        $response['message'] = 'Por favor, ingresa una flag.';
        echo json_encode($response);
        exit;
    }

    $isValid = false;
    $requiresDbName = false;
    $dbNameCorrect = false;

    // Validación segura utilizando hash_equals para evitar ataques de tiempo
    foreach ($VALID_FLAGS as $correctFlag) {
        if (strlen($submittedFlag) === strlen($correctFlag)) {
            if (hash_equals($correctFlag, $submittedFlag)) {
                
                // Si es el Reto 3, aplicar regla de validación cruzada estricta
                if ($correctFlag === 'FLAG{blind_boolean_inferential_db}') {
                    $requiresDbName = true;
                    if ($submittedDbName === 'tesis_sqli') {
                        $isValid = true;
                        $dbNameCorrect = true;
                    } else {
                        $isValid = false;
                    }
                } else {
                    $isValid = true;
                }
                break;
            }
        }
    }

    // Registrar en el log de auditoría (incluyendo info extra si aplica)
    $logPayload = $submittedFlag;
    if ($requiresDbName) {
        $logPayload .= " | DB_NAME_SUBMITTED: " . htmlspecialchars(substr($submittedDbName, 0, 50));
    }
    logAttempt($logPayload, $isValid);

    if ($isValid) {
        $response['success'] = true;
        $response['message'] = '¡Validación Exitosa! La flag es correcta. (Registro guardado en logs)';
    } else {
        $response['success'] = false;
        if ($requiresDbName && !$dbNameCorrect) {
            $response['message'] = 'Fallo de Validación: La flag es correcta, pero el nombre de la base de datos es incorrecto o está vacío.';
        } else {
            $response['message'] = 'Fallo de Validación: La flag ingresada no existe o es incorrecta.';
        }
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validador de Flags CTF - SecurityPro</title>
    <!-- Fuentes y estilos base (Consistentes con el entorno corporativo/ciberseguro) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --success: #10b981;
            --danger: #ef4444;
            --border-color: #334155;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .validator-container {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 40px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: -0.025em;
        }

        .header p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .form-group { margin-bottom: 25px; }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        input[type="text"] {
            width: 100%;
            padding: 12px 16px;
            background-color: #0f172a;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-main);
            font-family: monospace;
            font-size: 1.1rem;
            transition: all 0.2s ease;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        button:hover { background-color: var(--primary-hover); }

        /* Estilos dinámicos para el DOM (Respuesta Binaria) */
        #result-container {
            margin-top: 25px;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
            display: none; /* Oculto por defecto */
            animation: fadeIn 0.3s ease-out;
        }

        .result-success {
            background-color: rgba(16, 185, 129, 0.1);
            border: 2px solid var(--success);
            color: var(--success);
        }

        .result-error {
            background-color: rgba(239, 68, 68, 0.1);
            border: 2px solid var(--danger);
            color: var(--danger);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Estilos del botón de retorno */
        .btn-return {
            display: inline-block;
            width: 100%;
            margin-top: 15px;
            padding: 12px;
            background-color: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            transition: all 0.2s;
            box-sizing: border-box;
        }

        .btn-return:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--text-main);
            border-color: var(--text-muted);
        }
        
        .footer-note {
            margin-top: 30px;
            text-align: center;
            font-size: 0.8rem;
            color: var(--text-muted);
        }
    </style>
</head>
<body>

    <div class="validator-container">
        <div class="header">
            <h1>🛡️ Validador de Capturas</h1>
            <p>Ingresa la flag exfiltrada para verificar la integridad de la prueba (CTF).</p>
        </div>

        <div class="form-group">
            <label for="flag-input">Formato institucional: FLAG{...}</label>
            <input type="text" id="flag-input" placeholder="Ej: FLAG{xxxxxxxxxxxxxx}" autocomplete="off" spellcheck="false">
        </div>

        <div class="form-group" id="db-name-group" style="display: none; animation: fadeIn 0.3s ease-out;">
            <label for="db-name-input" style="color: #60a5fa;">Para verificar la exfiltración completa, ingresa el nombre de la base de datos descubierta:</label>
            <input type="text" id="db-name-input" placeholder="Nombre de la base de datos..." autocomplete="off" spellcheck="false">
        </div>

        <button id="validate-btn">Validar Flag</button>

        <!-- Contenedor dinámico para retroalimentación binaria en el DOM -->
        <div id="result-container"></div>
        
        <!-- Botón de Retorno Seguro -->
        <a href="dashboard.php" class="btn-return">Regresar al Panel de Retos</a>

        <div class="footer-note">
            Entorno de Laboratorio Aislado | Registro de auditoría activo (DSRM)
        </div>
    </div>

    <script>
        // Escuchar cambios en el input principal para desplegar el input dinámico
        document.getElementById('flag-input').addEventListener('input', function() {
            const flagValue = this.value.trim();
            const dbNameGroup = document.getElementById('db-name-group');
            if (flagValue === 'FLAG{blind_boolean_inferential_db}') {
                dbNameGroup.style.display = 'block';
            } else {
                dbNameGroup.style.display = 'none';
            }
        });

        document.getElementById('validate-btn').addEventListener('click', async () => {
            const flagValue = document.getElementById('flag-input').value.trim();
            const dbNameValue = document.getElementById('db-name-input').value.trim();
            const resultContainer = document.getElementById('result-container');
            
            // Ocultar resultados previos
            resultContainer.style.display = 'none';
            resultContainer.className = '';
            
            if (!flagValue) {
                showResult(false, '⚠️ Por favor ingresa una flag para validar.');
                return;
            }

            // Cambiar estado del botón
            const btn = document.getElementById('validate-btn');
            const originalText = btn.innerText;
            btn.innerText = 'Validando...';
            btn.disabled = true;

            try {
                const payload = { flag: flagValue };
                if (document.getElementById('db-name-group').style.display !== 'none') {
                    payload.db_name = dbNameValue;
                }

                const response = await fetch('validator.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();
                showResult(data.success, data.success ? '✅ ' + data.message : '❌ ' + data.message);
                
            } catch (error) {
                showResult(false, '❌ Error de comunicación con el servidor.');
            } finally {
                btn.innerText = originalText;
                btn.disabled = false;
            }
        });

        // Función para renderizar el recuadro dinámico en el DOM
        function showResult(isSuccess, message) {
            const resultContainer = document.getElementById('result-container');
            resultContainer.innerText = message;
            resultContainer.className = isSuccess ? 'result-success' : 'result-error';
            resultContainer.style.display = 'block';
        }
    </script>
</body>
</html>
