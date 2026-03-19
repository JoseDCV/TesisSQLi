# Proyecto de Tesis: Análisis de Vulnerabilidades de Inyección SQL

[![PHP 8.1+](https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=flat-square&logo=php)](https://www.php.net/)
[![MariaDB 10.5+](https://img.shields.io/badge/MariaDB-10.5%2B-003B6F?style=flat-square&logo=mariadb)](https://mariadb.org/)
[![License MIT](https://img.shields.io/badge/License-MIT-green.svg?style=flat-square)](LICENSE)
[![Código en Español](https://img.shields.io/badge/Documentaci%C3%B3n-Español-red?style=flat-square)]()

## 📋 Tabla de Contenidos

- [Descripción General](#descripción-general)
- [Arquitectura del Proyecto](#arquitectura-del-proyecto)
- [Stack Tecnológico](#stack-tecnológico)
- [Instalación](#instalación)
- [Estructura de Base de Datos](#estructura-de-base-de-datos)
- [Uso](#uso)
- [Roadmap](#roadmap)
- [Licencia](#licencia)

---

## 📘 Descripción General

Este proyecto es una **aplicación web académica desarrollada como trabajo de tesis** que tiene como objetivo demostrar y analizar las vulnerabilidades de **Inyección SQL (SQLi)** en aplicaciones web PHP. La plataforma proporciona un entorno controlado donde es posible estudiar:

- Métodos de ataque por inyección SQL
- Impacto de estas vulnerabilidades en sistemas de bases de datos
- Técnicas defensivas y validación de entrada
- Prácticas recomendadas en seguridad de aplicaciones

**Nota Académica:** Este proyecto es exclusivamente para fines educativos y de investigación. Su uso en sistemas en producción sin las debidas medidas de seguridad es **completamente desaconsejado**.

---

## 🏗️ Arquitectura del Proyecto

### Estructura de Directorios

```
TesisSQLi/
├── src/
│   ├── config/               # Configuración de la aplicación
│   │   └── db.php           # Conexión a base de datos MariaDB
│   └── public/              # Archivos públicamente accesibles
│       ├── index.html       # Página de inicio
│       ├── login.php        # Módulo de autenticación
│       ├── register.php     # Módulo de registro de usuarios
│       ├── dashboard.php    # Panel de control
│       ├── auth.html        # Página de autenticación
│       ├── setup.php        # Script de configuración inicial
│       ├── assets/          # Recursos estáticos
│       │   └── img/         # Imágenes del proyecto
│       └── css/
│           ├── styles.css          # Estilos principales
│           └── custom-styles.css   # Estilos personalizados
├── docker/                  # Configuración de contenedores
│   ├── mariadb/
│   │   └── init.sql         # Script de inicialización de BD
│   ├── nginx/
│   │   └── nginx.conf       # Configuración del servidor web
│   └── php/
│       └── Dockerfile       # Imagen de PHP personalizada
├── docker-compose.yml       # Orquestación de servicios
└── README.md               # Este archivo
```

### Componentes Principales

| Componente | Descripción |
|-----------|------------|
| **src/config** | Módulo de configuración centralizado que maneja la conexión a la base de datos MariaDB con validación de credenciales. |
| **src/public** | Interfaz web que contiene las páginas de autenticación, registro y dashboard. Punto de entrada para el análisis de vulnerabilidades SQLi. |
| **docker/** | Infraestructura containerizada que permite replicabilidad y aislamiento del entorno de desarrollo. |

---

## 💻 Stack Tecnológico

### Backend
- **PHP 8.1+** - Lenguaje de programación del lado del servidor
- **MySQLi (Procedural)** - Interfaz de conexión a base de datos

### Base de Datos
- **MariaDB 10.5+** - Sistema gestor de base de datos relacional (fork de MySQL)
- **SQL** - Lenguaje de consultas estructurado

### Frontend
- **HTML5** - Estructura semántica del contenido
- **CSS3** - Estilos y diseño responsivo
- **JavaScript (Vanilla)** - Lógica de interacción del cliente

### Infraestructura
- **Docker & Docker Compose** - Containerización y orquestación
- **Nginx** - Servidor web de alto rendimiento
- **Linux** - Sistema operativo base

---

## 🔧 Instalación

### Requisitos Previos

#### Instalación Local (Linux)

- **PHP 8.1 o superior** con extensiones `mysqli` y `curl`
- **MariaDB Server 10.5 o superior**
- **Composer** (opcional, para gestión de dependencias futuras)
- **Git** (para control de versiones)

#### Con Docker

- **Docker** 20.10+
- **Docker Compose** 1.29+

---

### Opción 1: Instalación Local en Linux

#### 1. Clonar el Repositorio

```bash
git clone https://github.com/JoseDCV/TesisSQLi.git
cd TesisSQLi
```

#### 2. Instalar Dependencias del Sistema

**Debian/Ubuntu:**
```bash
sudo apt update
sudo apt install php8.1 php8.1-cli php8.1-mysql php8.1-curl -y
sudo apt install mariadb-server mariadb-client -y
```

**Fedora/RHEL:**
```bash
sudo dnf install php php-mysqlnd php-curl -y
sudo dnf install mariadb-server mariadb -y
```

#### 3. Configurar MariaDB

```bash
# Iniciar el servicio MariaDB
sudo systemctl start mariadb
sudo systemctl enable mariadb

# Ejecutar configuración segura (recomendado)
sudo mysql_secure_installation

# Acceder a MariaDB y crear usuario para la tesis
sudo mysql -u root -p
```

En la consola de MariaDB:
```sql
-- Crear usuario para la aplicación
CREATE USER 'tesis_user'@'localhost' IDENTIFIED BY 'tesis_password';

-- Crear base de datos
CREATE DATABASE tesis_sqli CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Otorgar permisos
GRANT ALL PRIVILEGES ON tesis_sqli.* TO 'tesis_user'@'localhost';
FLUSH PRIVILEGES;

-- Salir
EXIT;
```

#### 4. Importar Estructura de Base de Datos

```bash
mysql -u tesis_user -p tesis_sqli < docker/mariadb/init.sql
# Ingresar contraseña: tesis_password
```

#### 5. Configurar Variables de Entorno

Crear archivo `.env` en la raíz del proyecto:

```bash
# Configuración de Base de Datos
DB_HOST=127.0.0.1
DB_USER=tesis_user
DB_PASSWORD=tesis_password
DB_NAME=tesis_sqli

# Configuración de Aplicación
APP_ENV=development
APP_DEBUG=true
APP_PORT=8000
```

#### 6. Iniciar Servidor PHP Integrado

```bash
cd src/public
php -S localhost:8000
```

La aplicación estará disponible en: **http://localhost:8000**

---

### Opción 2: Instalación con Docker

#### 1. Clonar el Repositorio

```bash
git clone https://github.com/JoseDCV/TesisSQLi.git
cd TesisSQLi
```

#### 2. Crear Archivo .env

```bash
cat > .env << EOF
DB_HOST=mariadb
DB_USER=tesis_user
DB_PASSWORD=tesis_password
DB_NAME=tesis_sqli
APP_ENV=development
EOF
```

#### 3. Construir e Iniciar Contenedores

```bash
docker-compose up -d --build
```

#### 4. Verificar Estado

```bash
docker-compose ps

# Salida esperada:
# CONTAINER ID   IMAGE               STATUS      PORTS
# xxxxxxxx       tesisSQLi-php       Up 2 mins   0.0.0.0:8000->8000/tcp
# xxxxxxxx       mariadb:latest      Up 2 mins   3306/tcp
```

La aplicación estará disponible en: **http://localhost:8000**

---

### Script de Configuración Automática

Se proporciona el script `setup.php` para automatizar la inicialización:

```bash
# Acceder a la URL de configuración
curl http://localhost:8000/setup.php

# O navegar en navegador
# http://localhost:8000/setup.php
```

---

## 📊 Estructura de Base de Datos

### Tabla: `users`

Tabla principal para almacenamiento de credenciales de usuario.

| Campo | Tipo | Atributos | Descripción |
|-------|------|-----------|------------|
| `id` | INT | PRIMARY KEY, AUTO_INCREMENT | Identificador único del usuario |
| `username` | VARCHAR(255) | NOT NULL, UNIQUE | Nombre de usuario único |
| `email` | VARCHAR(255) | NOT NULL, UNIQUE | Correo electrónico único |
| `password` | VARCHAR(255) | NOT NULL | Hash de contraseña |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Fecha y hora de creación |

#### Índices Implementados

```sql
CREATE INDEX idx_username ON users(username);
CREATE INDEX idx_email ON users(email);
```

**Justificación:** Los índices mejoran el rendimiento de búsquedas frecuentes por `username` y `email`.

#### Charset y Collation

- **Charset:** `utf8mb4` - Soporte completo de caracteres Unicode (incluyendo emojis)
- **Collation:** `utf8mb4_unicode_ci` - Comparación insensible a mayúsculas/minúsculas

---

## 🚀 Uso

### Flujo de Uso Típico

#### 1. Registro de Usuario

```
http://localhost:8000/register.php
```

- Completar formulario con `username`, `email` y `password`
- La aplicación insertará los datos en la tabla `users`

#### 2. Autenticación

```
http://localhost:8000/login.php
```

- Ingresar credenciales
- Sistema verificará contra base de datos

#### 3. Panel de Control

```
http://localhost:8000/dashboard.php
```

- Acceso a funcionalidades posteriores a autenticación

---

## 🔬 Vulnerabilidades de Inyección SQL

La aplicación **contiene vulnerabilidades deliberadas de SQLi** para fines académicos:

### Puntos Vulnerables Identificados

1. **Módulo de Autenticación** (`login.php`)
   - Entrada de usuario concatenada directamente en queries SQL
   - Parámetros GET/POST sin sanitización

2. **Módulo de Registro** (`register.php`)
   - Inserción directa de datos sin validación
   - Sin uso de prepared statements

### Ejemplos de Ataques Posibles

**SQLi de Autenticación (Bypass):**
```
Username: admin' OR '1'='1
Password: anything
```

**SQLi de Extracción de Datos:**
```
Username: ' UNION SELECT id, username, password, email, created_at FROM users--
```

---

## 📅 Roadmap

### Fase 1: Estructura Base ✅
- [x] Inicialización de proyecto
- [x] Configuración de base de datos
- [x] Interfaces HTML/CSS
- [x] Módulos de login y registro

### Fase 2: Dockerización (En Progreso 🔄)
- [ ] Dockerfile optimizado para PHP
- [ ] Configuración de nginx
- [ ] Docker Compose funcional
- [ ] Scripts de inicialización automática

### Fase 3: Implementación de Vulnerabilidades (En Progreso 🔄)
- [ ] Validación de puntos vulnerables
- [ ] Documentación de técnicas de ataque
- [ ] Casos de uso SQLi avanzados
- [ ] Pruebas de penetración

### Fase 4: Contramedidas y Seguridad (Planificado 📋)
- [ ] Implementación de prepared statements
- [ ] Validación y sanitización de entrada
- [ ] Protección CSRF y XSS
- [ ] Rate limiting y WAF
- [ ] Encriptación de contraseñas (bcrypt/argon2)

### Fase 5: Documentación Académica (Planificado 📋)
- [ ] Guía completa de vulnerabilidades
- [ ] Análisis de impacto
- [ ] Benchmarking de ataques
- [ ] Mejores prácticas defensivas

---

## 📚 Referencias Académicas

- OWASP Top 10 - Inyección SQL: https://owasp.org/www-community/attacks/SQL_Injection
- CWE-89: Improper Neutralization of Special Elements used in an SQL Command: https://cwe.mitre.org/data/definitions/89.html
- PHP MySQLi Documentation: https://www.php.net/manual/en/book.mysqli.php
- MariaDB Security: https://mariadb.com/kb/en/security/

---

## 📄 Licencia

Este proyecto está licenciado bajo la Licencia MIT. Consulta el archivo [LICENSE](LICENSE) para más detalles.

```
MIT License

Copyright (c) 2026 JoseDCV

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.
```

---

## ⚠️ Aviso Legal

**ESTE PROYECTO ES SOLO PARA FINES EDUCATIVOS Y DE INVESTIGACIÓN**

No se debe utilizar para atacar sistemas sin autorización. El autor no es responsable del mal uso de este código. Cumpla con las leyes aplicables en su jurisdicción.

---

## 👤 Autor

**José David Castillo Vargas** (JoseDCV)

Trabajo de Tesis - Análisis de Vulnerabilidades de Inyección SQL en Aplicaciones Web PHP

---

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Para cambios mayores, por favor abre un issue primero para discutir los cambios propuestos.

```bash
git checkout -b feature/nueva-funcionalidad
git commit -am 'Añadir nueva funcionalidad'
git push origin feature/nueva-funcionalidad
```

---

## 📧 Contacto

Para preguntas o sugerencias académicas, por favor abre un [GitHub Issue](https://github.com/JoseDCV/TesisSQLi/issues).

---

**Última actualización:** 20 de enero de 2026  
**Versión:** 1.0.0
