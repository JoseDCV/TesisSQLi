<?php
session_start();
$conn = require '../config/db.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: auth.html');
    exit();
}

$username = $_SESSION['username'];

// Verificar privilegios de administrador (Role-Based Access Control)
if ($username !== 'admin' && $username !== 'sysadmin') {
    header('HTTP/1.1 403 Forbidden');
    die("
    <html><body style='background:#0a0a0a; color:#00ff00; font-family:Courier New, monospace; text-align:center; padding-top:100px;'>
    <h1 style='color:#ff0000; font-size:48px;'>403 - ACCESO DENEGADO</h1>
    <p style='font-size:18px;'>[ ERROR DE AUTORIZACIÓN ]<br><br>Esta zona está restringida estrictamente para el personal de administración del sistema.</p>
    <br><br>
    <a href='dashboard.php' style='color:#00ccff; text-decoration:none; border:1px solid #00ccff; padding:10px 20px;'>&lt;&lt; RETURN TO DASHBOARD</a>
    </body></html>
    ");
}

// RETO 2 CTF: Buscador Vulnerable a SQLi (Union-based y Error-based)
/*
 * ==============================================================================
 * COMENTARIO ACADÉMICO PARA TESIS:
 * Este código es vulnerable por diseño debido a la falta de sentencias preparadas 
 * (Prepared Statements). Al concatenar directamente la entrada del usuario 
 * ($_GET['search']) en la instrucción SQL, un atacante puede modificar la 
 * semántica de la consulta original.
 * 
 * Esto permite la exfiltración de metadatos críticos de la base de datos, 
 * como la versión (@@version), el usuario actual (user()), o el nombre de 
 * la base de datos (database()), así como volcar tablas enteras.
 * ==============================================================================
 */
$search = isset($_GET['search']) ? $_GET['search'] : '';

if (!empty($search)) {
    // VULNERABILIDAD AQUI: Concatenación directa de variable sin escape.
    // 
    // ESTRUCTURA DE TABLA FIJA: Seleccionamos exactamente 4 columnas (id, username, email, created_at).
    // Esto es crucial para que el estudiante pueda realizar la enumeración con ORDER BY (ej: ORDER BY 4) 
    // y hacer un UNION SELECT 1,2,3,4 consistente.
    //
    // LÓGICA DE RESULTADOS (UNION-BASED): 
    // Limpieza Automática con UNION: Si detectamos la palabra UNION, forzamos internamente 
    // una condición falsa en la consulta original (1=0 AND). Esto garantiza que la tabla 
    // solo muestre la fila inyectada por el estudiante sin "pistas falsas" de usuarios reales.
    $force_false = (stripos($search, 'UNION') !== false) ? "1=0 AND " : "";
    $query = "SELECT id, username, email, created_at FROM users WHERE {$force_false}username LIKE '%$search%' ORDER BY id ASC";
} else {
    // Consulta segura si no hay búsqueda (4 columnas exactas)
    $query = "SELECT id, username, email, created_at FROM users ORDER BY id ASC";
}

// Ejecutamos la consulta usando un bloque try-catch para no silenciar las excepciones
// y manejar explícitamente los errores para facilitar SQLi Error-based
try {
    $result = $conn->query($query);
    if (!$result) {
        // En caso de que MySQLi no lance excepción por defecto, forzamos una
        throw new Exception($conn->error);
    }
} catch (Exception $e) {
    // Comentario para Tesis: Exponer detalles de errores internos de la base de datos 
    // en la interfaz de usuario es una vulnerabilidad crítica de "Information Disclosure" 
    // que facilita directamente los ataques Error-based SQLi.
    $db_error = $e->getMessage();
    $error_message = "Error SQL [MariaDB]: " . $db_error . " <br>Query Ejecutada: " . htmlspecialchars($query);
    $result = false;
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Administración - Usuarios del Sistema</title>
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
            max-width: 1100px;
            margin: 0 auto;
        }

        h1 {
            font-weight: 700;
            color: #f8fafc;
            margin-bottom: 10px;
            font-size: 28px;
            letter-spacing: -0.5px;
        }

        .subtitle {
            color: #94a3b8;
            margin-bottom: 40px;
            font-size: 15px;
        }

        .users-table-container {
            overflow-x: auto;
            background: #1e293b;
            border-radius: 12px;
            border: 1px solid #334155;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
        }

        .users-table th,
        .users-table td {
            padding: 16px 24px;
            text-align: left;
            border-bottom: 1px solid #334155;
        }

        .users-table th {
            background: #0f172a;
            color: #94a3b8;
            font-size: 13px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .users-table tr:last-child td {
            border-bottom: none;
        }

        .users-table tr:hover {
            background: #334155;
        }

        .users-table td {
            color: #e2e8f0;
            font-size: 14px;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-admin {
            background: rgba(139, 92, 246, 0.1);
            color: #c4b5fd;
            border: 1px solid rgba(139, 92, 246, 0.3);
        }

        .badge-user {
            background: rgba(59, 130, 246, 0.1);
            color: #93c5fd;
            border: 1px solid rgba(59, 130, 246, 0.3);
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

        /* Estilos del buscador */
        .search-container {
            margin-bottom: 25px;
            display: flex;
            gap: 10px;
        }

        .search-input {
            flex: 1;
            padding: 12px 20px;
            border-radius: 8px;
            border: 1px solid #334155;
            background: #0f172a;
            color: #f8fafc;
            font-size: 15px;
            outline: none;
            transition: border-color 0.2s;
        }

        .search-input:focus {
            border-color: #3b82f6;
        }

        .search-btn {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 15px;
            transition: background 0.2s;
        }

        .search-btn:hover {
            background: #2563eb;
        }

        .sql-error-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-left: 4px solid #ef4444;
            color: #f87171;
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 25px;
            font-family: monospace;
            font-size: 14px;
            word-break: break-all;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <a href="dashboard.php" class="navbar-brand"><i class="fas fa-server"></i> ADMIN CONSOLE</a>
        <div>
            <span style="margin-right:20px; color:#aaa;">Operador: <strong
                    style="color:#fff;"><?php echo htmlspecialchars($username); ?></strong></span>
            <a href="logout.php" class="logout-btn"><i class="fas fa-power-off"></i> Disconnect</a>
        </div>
    </nav>

    <div class="main-content">
        <a href="dashboard.php" class="btn-back"><i class="fas fa-chevron-left"></i> Retornar al Dashboard</a>

        <h1>Directorio de Usuarios Globales</h1>
        <p class="subtitle">Visualización completa de las identidades registradas en el clúster de base de datos
            MariaDB.</p>

        <!-- Buscador Vulnerable -->
        <div class="search-container">
            <form action="admin_users.php" method="GET" style="display:flex; width:100%; gap:10px;">
                <input type="text" name="search" class="search-input" placeholder="Buscar usuario por nombre..."
                    value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="search-btn"><i class="fas fa-search"></i> Buscar</button>
            </form>
        </div>

        <?php if (isset($error_message)): ?>
            <!-- Caja de Error SQL (Facilita ataques Error-based) -->
            <div class="sql-error-box" style="display:flex; flex-direction:column; gap:12px;">
                <div style="font-size: 16px; font-weight: bold; color: #fca5a5;">
                    <i class="fas fa-exclamation-triangle"></i> Excepción de Base de Datos
                </div>
                <!-- Mensaje de MariaDB como protagonista visual -->
                <div
                    style="font-size: 20px; color: #ef4444; background: #450a0a; padding: 15px; border-radius: 6px; border: 1px solid #7f1d1d;">
                    <strong><?php echo htmlspecialchars($db_error); ?></strong>
                </div>
                <div style="color: #f87171; font-size: 13px; margin-top: 5px;">
                    Query Ejecutada: <br><span
                        style="color: #cbd5e1; font-family: monospace;"><?php echo htmlspecialchars($query); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <div class="users-table-container">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>UID</th>
                        <th>Nombre de Usuario</th>
                        <th>Correo Corporativo / Red</th>
                        <th>Privilegios</th>
                        <th>Fecha de Alta</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && $result->num_rows > 0) {
                        // Renderizado Condicional Estricto para el Reto CTF
                        $is_sqli_attempt = preg_match('/ORDER\s+BY|UNION/i', $search);
                        $is_valid_sqli = stripos($search, 'ORDER BY 4') !== false || stripos($search, 'UNION') !== false;

                        if ($is_sqli_attempt && !$is_valid_sqli) {
                            // Mostrar mensaje de error en lugar de resultados parciales (ej. ORDER BY 1, 2, 3)
                            echo "<tr><td colspan='5' style='text-align:center; padding: 40px; color:#ef4444; font-size:15px; font-weight:600; background:rgba(239,68,68,0.05); border:1px dashed #ef4444;'><i class='fas fa-times-circle'></i> Error de sincronización de columnas: No se pueden mostrar registros parciales</td></tr>";
                        } else {
                            while ($row = $result->fetch_assoc()) {
                                // Identificar badges
                                $is_admin = ($row['username'] === 'admin' || $row['username'] === 'sysadmin');
                                $badge = $is_admin ? '<span class="badge badge-admin"><i class="fas fa-shield-alt"></i> ADMIN</span>' : '<span class="badge badge-user"><i class="fas fa-user"></i> USER</span>';

                                echo "<tr>";
                                echo "<td style='color:#777;'>#" . htmlspecialchars($row['id']) . "</td>";
                                echo "<td><strong style='color:#fff;'>" . htmlspecialchars($row['username']) . "</strong></td>";
                                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                echo "<td>" . $badge . "</td>";
                                echo "<td style='color:#999;'>" . htmlspecialchars($row['created_at']) . "</td>";
                                echo "</tr>";
                            }
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center; padding: 40px;'>No se encontraron registros activos en la base de datos.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if (isset($is_valid_sqli) && $is_valid_sqli && stripos($search, 'UNION') !== false): ?>
    <script>
        Swal.fire({
            title: '¡Exfiltración UNION Exitosa!',
            html: '<p style="margin-bottom: 15px;">Has logrado extraer datos mediante UNION SELECT.</p><p style="font-family: monospace; font-size: 1.2rem; background: #1e293b; color: #10b981; padding: 15px; border-radius: 8px;">FLAG{union_metadata_exfil_db}</p>',
            icon: 'success',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#475569',
            confirmButtonText: 'Ir a Validar Flag <i class="fas fa-arrow-right"></i>',
            cancelButtonText: 'Continuar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'validator.php';
            }
        });
    </script>
    <?php endif; ?>
</body>

</html>