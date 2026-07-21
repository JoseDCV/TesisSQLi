# SecurityPro - Entorno CTF para el Entrenamiento Progresivo en Inyección SQL

Un entorno de laboratorio técnico (CTF) desacoplado, diseñado para el entrenamiento progresivo y la investigación en vulnerabilidades de Inyección SQL (SQLi). El laboratorio está enfocado en tres niveles de complejidad creciente: Bypass de Autenticación, Union-Based SQLi y Blind SQLi. Todo el entorno está containerizado para garantizar un despliegue rápido, seguro y aislado.

---

## Arquitectura del Sistema (Stack Tecnológico)

El laboratorio utiliza una arquitectura de microservicios basada en contenedores Docker sobre una red aislada (`ctf_network`):

*   **Nginx (Proxy/Web Server):** Alpine Nginx configurado para servir la aplicación y hacer proxy de las peticiones.
*   **PHP 8.2 FPM (Backend):** Contenedor encargado de procesar la lógica vulnerable y comunicarse con la base de datos.
*   **MariaDB 10.5 (Base de Datos):** Servidor de base de datos que aloja la información objetivo del laboratorio (tablas de usuarios y datos ficticios).

---

## Prerrequisitos

Para desplegar este laboratorio, necesitas tener instalado:

*   [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows/macOS) o **Docker Engine** (Linux).
*   **Docker Compose** (V2 incluido con Docker Desktop).
*   Git (Opcional, para clonar el repositorio).

---

## Guía de Despliegue Paso a Paso

Sigue estos pasos para levantar el entorno de manera segura en tu máquina local.

### 1. Clonar el repositorio y configurar variables de entorno

Clona el repositorio en tu máquina:
```bash
git clone <URL_DEL_REPOSITORIO>
cd TesisSQLi
```

A continuación, configura las credenciales copiando la plantilla:
```bash
cp .env.example .env
```
*(Puedes editar el archivo `.env` si deseas cambiar las credenciales por defecto, aunque los valores iniciales sirven perfectamente para un entorno local/aislado).*

### 2. Despliegue según tu Sistema Operativo

#### Windows (PowerShell / CMD y Docker Desktop)
Abre PowerShell o CMD y ejecuta:
```powershell
docker-compose up -d --build
```
Una vez levantado, la aplicación estará disponible en `http://localhost`.

#### macOS (Terminal y Docker Desktop Mac)
Abre la Terminal en el directorio del proyecto y ejecuta:
```bash
docker-compose up -d --build
```
Accede a la aplicación en `http://localhost`.

#### Linux (Terminal, Docker Engine y Docker Compose V2)
Abre tu Terminal y ejecuta (puede requerir `sudo` si tu usuario no pertenece al grupo `docker`):
```bash
docker compose up -d --build
```
*(Nota: En versiones modernas de Docker, el comando es `docker compose` sin el guion).*
Accede a la aplicación en `http://localhost`.

### 3. Instalación de la Base de Datos
La primera vez que levantes el entorno, deberás poblar la base de datos. Para ello, visita la siguiente URL en tu navegador:
`http://localhost/setup.php`

---

## Niveles del Laboratorio

El entorno está diseñado con 3 retos de Inyección SQL que aumentan en complejidad:

1.  **Nivel Inicial (Bypass de Autenticación):**
    Enfocado en vulnerar un formulario de login (Authentication Bypass) mediante inyección en campos de entrada, permitiendo acceso como administrador sin conocer la contraseña.
2.  **Nivel Intermedio (Buscador / Exfiltración - Union-Based):**
    Consiste en la explotación de un motor de búsqueda vulnerable para extraer información estructurada y oculta desde la base de datos utilizando operadores UNION.
3.  **Nivel Avanzado (Verificador Ciego - Blind SQLi):**
    Enfocado en vulnerabilidades donde no hay feedback directo (errores u outputs en pantalla). El atacante debe inferir la estructura y el contenido de la base de datos basándose en diferencias lógicas (Boolean-Based) o tiempos de respuesta (Time-Based).

---

## Detención, Limpieza e Higiene de Seguridad

Es fundamental apagar los contenedores y limpiar los volúmenes una vez concluido el entrenamiento.

**Para detener los contenedores (conservando los datos de la base de datos):**
```bash
docker-compose down
```

**Para detener los contenedores y ELIMINAR la base de datos (Reset completo):**
```bash
docker-compose down -v
```

> **Nota sobre Seguridad:** Las credenciales y variables críticas se manejan exclusivamente a través del archivo `.env`. Este archivo está excluido del control de versiones mediante `.gitignore`. **Nunca subas tu archivo `.env` a un repositorio público.**

---

## Licencia y Consideraciones Éticas

⚠️ **ADVERTENCIA E IMPORTANTE:**
Este proyecto se ha desarrollado **exclusivamente con fines educativos y de investigación**. Las vulnerabilidades expuestas aquí son intencionales y su objetivo es la formación técnica y la mejora de habilidades en Ciberseguridad.

*   No despliegues este entorno en servidores expuestos públicamente a Internet.
*   Utilízalo únicamente en redes locales o máquinas virtuales bajo tu control absoluto.
*   Cualquier uso indebido de los conocimientos adquiridos a partir de este laboratorio en sistemas ajenos sin consentimiento expreso es ilegal y contrario a la ética profesional.
