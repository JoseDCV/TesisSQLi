<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    // Redirigir al login si no está autenticado
    header('Location: auth.html');
    exit();
}

// Obtener nombre de usuario
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Dashboard - Ciberseguridad</title>
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Font Awesome icons -->
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <!-- Google fonts -->
        <link href="https://fonts.googleapis.com/css?family=Roboto|Inter|Orbitron" rel="stylesheet" />
        <style>
            * { box-sizing: border-box; margin: 0; padding: 0; }
            body {
                background-color: #0f172a;
                color: #f8fafc;
                font-family: 'Inter', -apple-system, sans-serif;
                min-height: 100vh;
            }
            .navbar {
                position: fixed; top: 0; left: 0; right: 0;
                background: #1e293b;
                border-bottom: 1px solid #334155;
                padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; z-index: 100;
            }
            .navbar-brand { font-size: 22px; font-weight: 700; color: #3b82f6; text-decoration: none; display:flex; align-items:center; gap:10px; letter-spacing: -0.5px;}
            .navbar-brand i { font-size: 24px; }
            .navbar-menu { display: flex; gap: 30px; align-items: center; }
            .navbar-link { color: #94a3b8; text-decoration: none; font-weight: 500; font-size: 15px; transition: color 0.2s; }
            .navbar-link:hover { color: #f8fafc; }
            .logout-btn {
                background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444;
                padding: 8px 18px; border-radius: 6px; text-decoration: none; font-weight: 500; font-size: 14px; transition: all 0.2s;
            }
            .logout-btn:hover { background: #ef4444; color: #fff; border-color: #ef4444;}
            
            .main-content { padding-top: 70px; }
            .hero-section { text-align: center; padding: 80px 20px 60px; border-bottom: 1px solid #1e293b; background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%); }
            .hero-section h1 { font-size: 36px; font-weight: 700; margin-bottom: 15px; color: #f8fafc; letter-spacing: -1px;}
            .hero-section .username { color: #3b82f6; }
            .hero-section p { font-size: 17px; color: #94a3b8; max-width: 600px; margin: 0 auto; line-height: 1.6;}
            
            .cards-container { max-width: 1100px; margin: 0 auto; padding: 60px 20px; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; }
            .card {
                background: #1e293b; border-radius: 12px; border: 1px solid #334155; padding: 35px 30px;
                transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }
            .card:hover { transform: translateY(-4px); box-shadow: 0 12px 20px -3px rgba(0, 0, 0, 0.3); border-color: #475569; }
            .card-icon { font-size: 36px; color: #3b82f6; margin-bottom: 20px; }
            .card h3 { font-size: 20px; font-weight: 600; margin-bottom: 12px; color: #f1f5f9; }
            .card p { font-size: 14px; line-height: 1.6; color: #94a3b8; margin-bottom: 25px; }
            .card-btn {
                background: #3b82f6; border: none; border-radius: 6px; padding: 10px 20px; color: #ffffff;
                text-decoration: none; display: inline-block; font-weight: 500; font-size: 14px; transition: background 0.2s;
            }
            .card-btn:hover { background: #2563eb; }
        </style>
    </head>
    <body>
        
        <!-- Navbar -->
        <nav class="navbar">
            <a href="dashboard.php" class="navbar-brand">
                <i class="fas fa-shield-alt"></i> Ciberseguridad
            </a>
            <div class="navbar-menu">
                <a href="dashboard.php" class="navbar-link">Inicio</a>
                <a href="tools.php" class="navbar-link">Herramientas</a>
                <a href="reports.php" class="navbar-link">Reportes</a>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </div>
        </nav>
        
        <!-- Main Content -->
        <div class="main-content">
            
            <!-- Hero Section -->
            <section class="hero-section">
                <h1>Bienvenido al Sistema, <span class="username"><?php echo htmlspecialchars($username); ?></span></h1>
                <p>Tu portal de información sobre ciberseguridad y mejores prácticas</p>
            </section>
            
            <!-- Cards Section -->
            <div class="cards-container">
                
                <?php if ($username === 'admin' || $username === 'sysadmin'): ?>
                <!-- Card Admin: Lista de Usuarios -->
                <div class="card" style="border-color: #8b5cf6;">
                    <div class="card-icon" style="color: #8b5cf6;">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <h3 style="color: #f1f5f9;">Panel de Administración</h3>
                    <p>
                        Acceso exclusivo. Visualiza todos los usuarios registrados en el sistema, correos electrónicos y fechas de registro de la base de datos central.
                    </p>
                    <a href="admin_users.php" class="card-btn" style="background: #8b5cf6;">Ver Todos los Usuarios</a>
                </div>
                
                <!-- Card Admin: Verificación de Usuarios (Blind SQLi) -->
                <div class="card" style="border-color: #f59e0b;">
                    <div class="card-icon" style="color: #f59e0b;">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <h3 style="color: #f1f5f9;">Módulo de Verificación</h3>
                    <p>
                        Herramienta de nivel avanzado para validar la existencia de identidades corporativas en el sistema de manera segura y confidencial.
                    </p>
                    <a href="verify_user.php" class="card-btn" style="background: #f59e0b;">Ir a Verificación</a>
                </div>
                <?php endif; ?>
                
                <!-- Card 1: Top 10 OWASP -->
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-list-ol"></i>
                    </div>
                    <h3>Top 10 OWASP 2025</h3>
                    <p>
                        Conoce las 10 vulnerabilidades de seguridad más críticas en aplicaciones web 
                        según el Open Web Application Security Project. Mantente actualizado con las 
                        amenazas más comunes y aprende a proteger tus sistemas.
                    </p>
                    <button class="card-btn btn-modal" data-target="owasp">Leer más</button>
                </div>
                
                <!-- Card 2: SQL Injection -->
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <h3>¿Qué es SQL Injection?</h3>
                    <p>
                        La inyección SQL es una de las vulnerabilidades más peligrosas en aplicaciones web. 
                        Aprende cómo funciona este tipo de ataque, sus consecuencias y las mejores 
                        prácticas para prevenir este tipo de vulnerabilidades.
                    </p>
                    <button class="card-btn btn-modal" data-target="sqli">Leer más</button>
                </div>
                
                <!-- Card 3: XSS Prevention -->
                <div class="card">
                    <div class="card-icon">
                        <i class="fas fa-code"></i>
                    </div>
                    <h3>Prevención de Ataques XSS</h3>
                    <p>
                        El Cross-Site Scripting (XSS) permite a los atacantes inyectar scripts maliciosos 
                        en páginas web. Descubre cómo validar inputs, sanitizar datos y proteger a tus 
                        usuarios de este tipo de ataques.
                    </p>
                    <button class="card-btn btn-modal" data-target="xss">Leer más</button>
                </div>
                
            </div>
            
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.querySelectorAll('.btn-modal').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = this.getAttribute('data-target');
                    
                    let title = '';
                    let htmlContent = '';
                    
                    if (target === 'owasp') {
                        title = 'Top 10 OWASP';
                        htmlContent = `
                            <div style="text-align: left; font-size: 0.95rem; line-height: 1.6; color: #cbd5e1;">
                                <p style="margin-bottom: 15px;">El <strong>OWASP Top 10</strong> es un documento de concientización estándar para desarrolladores y seguridad de aplicaciones web. Representa un consenso amplio sobre los riesgos de seguridad más críticos.</p>
                                <ul style="margin-left: 20px; margin-bottom: 15px;">
                                    <li><strong>A01:</strong> Control de Acceso Roto</li>
                                    <li><strong>A02:</strong> Fallos Criptográficos</li>
                                    <li><strong>A03:</strong> Inyección (SQLi, NoSQLi, OS)</li>
                                    <li><strong>A04:</strong> Diseño Inseguro</li>
                                    <li><strong>A05:</strong> Configuración de Seguridad Defectuosa</li>
                                </ul>
                                <p>Mantenerse alineado con este estándar mitiga la gran mayoría de los vectores de ataque conocidos a nivel global.</p>
                            </div>
                        `;
                    } else if (target === 'sqli') {
                        title = 'Inyección SQL (SQLi)';
                        htmlContent = `
                            <div style="text-align: left; font-size: 0.95rem; line-height: 1.6; color: #cbd5e1;">
                                <p style="margin-bottom: 15px;">La <strong>Inyección SQL</strong> ocurre cuando datos no confiables se envían a un intérprete como parte de un comando o consulta. Los datos hostiles del atacante pueden engañar al intérprete para que ejecute comandos imprevistos.</p>
                                <p style="margin-bottom: 10px; color: #f87171; font-weight: 600;">Mitigaciones Principales:</p>
                                <ul style="margin-left: 20px;">
                                    <li>Uso estricto de <strong>Sentencias Preparadas (Prepared Statements)</strong> o Consultas Parametrizadas.</li>
                                    <li>Implementación de Procedimientos Almacenados (Stored Procedures).</li>
                                    <li>Validación de entrada de listas blancas (White-listing).</li>
                                    <li>Escape de todos los datos suministrados por el usuario.</li>
                                </ul>
                            </div>
                        `;
                    } else if (target === 'xss') {
                        title = 'Cross-Site Scripting (XSS)';
                        htmlContent = `
                            <div style="text-align: left; font-size: 0.95rem; line-height: 1.6; color: #cbd5e1;">
                                <p style="margin-bottom: 15px;">El <strong>XSS</strong> ocurre cuando una aplicación incluye datos no confiables en una página web sin la validación o escape adecuados, o actualiza una página existente con datos provistos por el usuario mediante una API del navegador.</p>
                                <p style="margin-bottom: 10px; color: #f87171; font-weight: 600;">Mitigaciones Principales:</p>
                                <ul style="margin-left: 20px;">
                                    <li>Codificación sensible al contexto al insertar datos no confiables en un documento HTML.</li>
                                    <li>Implementación estricta de políticas <strong>CSP (Content Security Policy)</strong>.</li>
                                    <li>Uso de frameworks modernos (React, Vue, Angular) que escapan datos por defecto.</li>
                                </ul>
                            </div>
                        `;
                    }
                    
                    Swal.fire({
                        title: title,
                        html: htmlContent,
                        icon: 'info',
                        background: '#1e293b',
                        color: '#f8fafc',
                        confirmButtonColor: '#3b82f6',
                        confirmButtonText: 'Entendido'
                    });
                });
            });
        </script>
    </body>
</html>
