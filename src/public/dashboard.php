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
            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }
            
            body {
                background-image: url('https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?q=80&w=1920&auto=format&fit=crop');
                background-size: cover;
                background-attachment: fixed;
                color: #ffffff;
                font-family: 'Roboto', sans-serif;
                min-height: 100vh;
                position: relative;
            }
            
            .overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.7);
                z-index: 0;
            }
            
            /* Navbar */
            .navbar {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                background: rgba(0, 0, 0, 0.8);
                backdrop-filter: blur(10px);
                border-bottom: 1px solid rgba(0, 255, 0, 0.3);
                padding: 15px 30px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                z-index: 100;
            }
            
            .navbar-brand {
                font-family: 'Orbitron', sans-serif;
                font-size: 24px;
                color: #00ff00;
                text-decoration: none;
            }
            
            .navbar-menu {
                display: flex;
                gap: 30px;
                align-items: center;
            }
            
            .navbar-link {
                color: #ffffff;
                text-decoration: none;
                transition: color 0.3s;
            }
            
            .navbar-link:hover {
                color: #00ff00;
            }
            
            .logout-btn {
                background: rgba(255, 0, 0, 0.3);
                border: 1px solid #ff0000;
                color: #ffffff;
                padding: 8px 15px;
                border-radius: 5px;
                text-decoration: none;
                transition: background 0.3s, color 0.3s;
            }
            
            .logout-btn:hover {
                background: #ff0000;
                color: #000000;
            }
            
            /* Main Content */
            .main-content {
                position: relative;
                z-index: 1;
                padding-top: 80px;
            }
            
            /* Hero Section */
            .hero-section {
                text-align: center;
                padding: 60px 20px;
            }
            
            .hero-section h1 {
                font-family: 'Orbitron', sans-serif;
                font-size: 48px;
                margin-bottom: 10px;
                text-shadow: 0 0 20px rgba(0, 255, 0, 0.5);
            }
            
            .hero-section .username {
                color: #00ff00;
            }
            
            .hero-section p {
                font-size: 18px;
                color: #cccccc;
            }
            
            /* Cards Grid */
            .cards-container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 40px 20px;
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
                gap: 30px;
            }
            
            .card {
                background: rgba(255, 255, 255, 0.1);
                border-radius: 15px;
                backdrop-filter: blur(10px);
                border: 1px solid rgba(0, 255, 0, 0.3);
                padding: 30px;
                transition: transform 0.3s, box-shadow 0.3s;
            }
            
            .card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 30px rgba(0, 255, 0, 0.3);
            }
            
            .card-icon {
                font-size: 48px;
                color: #00ff00;
                margin-bottom: 20px;
            }
            
            .card h3 {
                font-family: 'Orbitron', sans-serif;
                font-size: 24px;
                margin-bottom: 15px;
                color: #00ccff;
            }
            
            .card p {
                font-size: 14px;
                line-height: 1.6;
                color: #cccccc;
                margin-bottom: 20px;
            }
            
            .card-btn {
                background: linear-gradient(90deg, #00ff00, #00ccff);
                border: none;
                border-radius: 5px;
                padding: 10px 20px;
                color: #ffffff;
                text-decoration: none;
                display: inline-block;
                cursor: pointer;
                transition: box-shadow 0.3s;
            }
            
            .card-btn:hover {
                box-shadow: 0 0 15px #00ff00;
            }
        </style>
    </head>
    <body>
        <div class="overlay"></div>
        
        <!-- Navbar -->
        <nav class="navbar">
            <a href="dashboard.php" class="navbar-brand">
                <i class="fas fa-shield-alt"></i> Ciberseguridad
            </a>
            <div class="navbar-menu">
                <a href="dashboard.php" class="navbar-link">Inicio</a>
                <a href="#" class="navbar-link">Herramientas</a>
                <a href="#" class="navbar-link">Reportes</a>
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
                    <a href="#" class="card-btn">Leer más</a>
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
                    <a href="#" class="card-btn">Leer más</a>
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
                    <a href="#" class="card-btn">Leer más</a>
                </div>
                
            </div>
            
        </div>
        
    </body>
</html>
