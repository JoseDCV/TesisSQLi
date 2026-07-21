<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header('Location: auth.html');
    exit();
}

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Reportes Técnicos - SecurityPro</title>
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css?family=Roboto|Inter|Orbitron" rel="stylesheet" />
    <style>
        body { background-color: #0f172a; color: #f8fafc; font-family: 'Inter', sans-serif; margin: 0; padding: 0; min-height: 100vh; }
        .navbar { position: fixed; top: 0; left: 0; right: 0; background: #1e293b; border-bottom: 1px solid #334155; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; z-index: 100; }
        .navbar-brand { font-size: 22px; font-weight: 700; color: #3b82f6; text-decoration: none; display:flex; align-items:center; gap:10px; }
        .navbar-menu { display: flex; gap: 30px; align-items: center; }
        .navbar-link { color: #94a3b8; text-decoration: none; font-weight: 500; font-size: 15px; transition: color 0.2s; }
        .navbar-link.active, .navbar-link:hover { color: #f8fafc; }
        .logout-btn { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 8px 18px; border-radius: 6px; text-decoration: none; font-weight: 500; font-size: 14px; transition: all 0.2s; }
        .logout-btn:hover { background: #ef4444; color: #fff; border-color: #ef4444;}
        
        .main-content { padding: 120px 40px 60px; max-width: 800px; margin: 0 auto; }
        
        .report-card { background: #1e293b; border: 1px solid #334155; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .report-header { background: rgba(59, 130, 246, 0.1); border-bottom: 1px solid #334155; padding: 30px; text-align: center; }
        .report-header i { font-size: 40px; color: #3b82f6; margin-bottom: 15px; }
        .report-header h2 { font-size: 24px; color: #f8fafc; margin-bottom: 5px; }
        .report-header p { color: #94a3b8; font-size: 14px; }
        
        .report-body { padding: 40px 30px; }
        .info-box { background: rgba(245, 158, 11, 0.1); border-left: 4px solid #f59e0b; padding: 20px; border-radius: 4px; margin-bottom: 30px; }
        .info-box p { color: #cbd5e1; font-size: 15px; line-height: 1.6; margin: 0; }
        .info-box strong { color: #fcd34d; font-family: monospace; font-size: 16px; display: inline-block; padding: 2px 6px; background: rgba(0,0,0,0.2); border-radius: 4px; }
        
        .status-list { list-style: none; padding: 0; margin: 0; }
        .status-item { display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px dashed #334155; }
        .status-item:last-child { border-bottom: none; }
        .status-name { color: #e2e8f0; font-weight: 500; display:flex; align-items:center; gap:10px; }
        .status-name i { color: #94a3b8; }
        .status-badge { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
        
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="dashboard.php" class="navbar-brand"><i class="fas fa-shield-alt"></i> Ciberseguridad</a>
        <div class="navbar-menu">
            <a href="dashboard.php" class="navbar-link">Inicio</a>
            <a href="tools.php" class="navbar-link">Herramientas</a>
            <a href="reports.php" class="navbar-link active">Reportes</a>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>
    </nav>
    
    <div class="main-content">
        <div class="report-card">
            <div class="report-header">
                <i class="fas fa-file-contract"></i>
                <h2>Estado Operativo de los Servicios</h2>
                <p>Reporte Técnico Oficial - Generado automáticamente</p>
            </div>
            
            <div class="report-body">
                <!-- OSINT VECTOR CLUE -->
                <div class="info-box">
                    <p><i class="fas fa-info-circle" style="color: #f59e0b; margin-right: 8px;"></i> <strong>Aviso del Departamento de TI:</strong><br><br>Cualquier problema, incidencia o duda con la estabilidad del servicio, favor enviar un correo de soporte directamente al usuario administrador a la dirección: <strong>admin@securityproproyect.org</strong>.</p>
                </div>
                
                <h3 style="color: #f8fafc; font-size: 18px; margin-bottom: 20px;">Monitor de Disponibilidad (Nodos Activos)</h3>
                <ul class="status-list">
                    <li class="status-item">
                        <span class="status-name"><i class="fas fa-server"></i> Nginx Web Server (Frontend)</span>
                        <span class="status-badge">Operativo</span>
                    </li>
                    <li class="status-item">
                        <span class="status-name"><i class="fas fa-code"></i> PHP-FPM Engine (Backend)</span>
                        <span class="status-badge">Operativo</span>
                    </li>
                    <li class="status-item">
                        <span class="status-name"><i class="fas fa-database"></i> MariaDB Cluster (Base de Datos)</span>
                        <span class="status-badge">Operativo</span>
                    </li>
                    <li class="status-item">
                        <span class="status-name"><i class="fas fa-shield-virus"></i> Firewall de Aplicaciones Web (WAF)</span>
                        <span class="status-badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border-color: rgba(239, 68, 68, 0.3);">Deshabilitado Temporalmente</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
