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
    <title>Herramientas de Auditoría - SecurityPro</title>
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
        
        .main-content { padding: 120px 40px 60px; max-width: 1000px; margin: 0 auto; }
        .header-title { text-align: center; margin-bottom: 50px; }
        .header-title h1 { font-size: 32px; font-weight: 700; color: #f8fafc; margin-bottom: 10px; }
        .header-title p { color: #94a3b8; font-size: 16px; max-width: 600px; margin: 0 auto; }
        
        .tools-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 25px; }
        .tool-card { background: #1e293b; border: 1px solid #334155; border-radius: 10px; padding: 25px; display: flex; gap: 20px; align-items: flex-start; transition: transform 0.2s, box-shadow 0.2s; }
        .tool-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3); border-color: #475569; }
        
        .tool-icon { flex-shrink: 0; width: 60px; height: 60px; border-radius: 12px; display: flex; justify-content: center; align-items: center; font-size: 28px; }
        .tool-content h3 { font-size: 18px; color: #f1f5f9; margin-bottom: 8px; font-weight: 600; }
        .tool-content p { color: #94a3b8; font-size: 14px; line-height: 1.5; }
        
        /* Tool Colors */
        .nmap { background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2); }
        .burp { background: rgba(249, 115, 22, 0.1); color: #f97316; border: 1px solid rgba(249, 115, 22, 0.2); }
        .sqlmap { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }
        .metasploit { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; border: 1px solid rgba(139, 92, 246, 0.2); }
        .wireshark { background: rgba(6, 182, 212, 0.1); color: #06b6d4; border: 1px solid rgba(6, 182, 212, 0.2); }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="dashboard.php" class="navbar-brand"><i class="fas fa-shield-alt"></i> Ciberseguridad</a>
        <div class="navbar-menu">
            <a href="dashboard.php" class="navbar-link">Inicio</a>
            <a href="tools.php" class="navbar-link active">Herramientas</a>
            <a href="reports.php" class="navbar-link">Reportes</a>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>
    </nav>
    
    <div class="main-content">
        <div class="header-title">
            <h1>Arsenal de Herramientas de Auditoría</h1>
            <p>Directorio de las herramientas más utilizadas en la industria para el descubrimiento, explotación y mitigación de vulnerabilidades técnicas en aplicaciones web y redes.</p>
        </div>
        
        <div class="tools-grid">
            <div class="tool-card">
                <div class="tool-icon nmap"><i class="fas fa-network-wired"></i></div>
                <div class="tool-content">
                    <h3>Nmap</h3>
                    <p>Herramienta esencial para exploración de redes y auditoría de seguridad. Utilizada para descubrir hosts y servicios activos en una red informática (Port Scanning y Fingerprinting).</p>
                </div>
            </div>
            
            <div class="tool-card">
                <div class="tool-icon burp"><i class="fas fa-spider"></i></div>
                <div class="tool-content">
                    <h3>Burp Suite</h3>
                    <p>Plataforma integral para realizar pruebas de seguridad en aplicaciones web. Su proxy interceptor permite a los auditores modificar peticiones HTTP sobre la marcha para buscar vulnerabilidades complejas.</p>
                </div>
            </div>
            
            <div class="tool-card">
                <div class="tool-icon sqlmap"><i class="fas fa-database"></i></div>
                <div class="tool-content">
                    <h3>SQLmap</h3>
                    <p>Herramienta automatizada open-source que detecta y explota fallas de Inyección SQL. Es capaz de hacer fingerprinting de bases de datos, exfiltrar datos e incluso acceder al sistema de archivos subyacente.</p>
                </div>
            </div>
            
            <div class="tool-card">
                <div class="tool-icon metasploit"><i class="fas fa-bomb"></i></div>
                <div class="tool-content">
                    <h3>Metasploit Framework</h3>
                    <p>El estándar de oro para pruebas de penetración. Proporciona información sobre vulnerabilidades, facilita el desarrollo de firmas de IDS y automatiza el despliegue de exploits contra sistemas objetivos.</p>
                </div>
            </div>
            
            <div class="tool-card">
                <div class="tool-icon wireshark"><i class="fas fa-wifi"></i></div>
                <div class="tool-content">
                    <h3>Wireshark</h3>
                    <p>El analizador de protocolos de red más utilizado a nivel mundial. Permite capturar y visualizar interactivamente el tráfico de red en un nivel micro (Packet Sniffing) para identificar anomalías o fugas de datos.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
