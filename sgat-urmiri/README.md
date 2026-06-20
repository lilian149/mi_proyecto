# 🌿 SGAT-Urmiri — Sistema de Gestión y Automatización Turística
## Guía de Instalación para XAMPP

---

## 📋 Requisitos Previos
- XAMPP instalado (PHP 8.0+, MySQL 5.7+, Apache)
- Navegador moderno (Chrome, Firefox, Edge)

---

## 🚀 Instalación Paso a Paso

### 1. Copiar el proyecto
Copia la carpeta `sgat-urmiri` dentro de:
```
C:\xampp\htdocs\sgat-urmiri\
```

### 2. Iniciar XAMPP
Abre XAMPP Control Panel y pulsa **Start** en:
- Apache
- MySQL

### 3. Crear la base de datos
Abre tu navegador y ve a:
```
http://localhost/phpmyadmin
```
- Haz clic en **Nueva** (o "New")
- Crea una base de datos llamada `sgat_urmiri`
- Selecciona charset: `utf8mb4_unicode_ci`
- Ve a la pestaña **SQL**
- Abre el archivo `database/sgat_urmiri.sql` de este proyecto
- Copia todo el contenido y pégalo en phpMyAdmin
- Haz clic en **Continuar / Go**

### 4. Configurar conexión (si es necesario)
Abre `config/database.php` y verifica:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          // dejar vacío en XAMPP por defecto
define('DB_NAME', 'sgat_urmiri');
define('BASE_URL', 'http://localhost/sgat-urmiri/');
```

### 5. Crear carpeta de uploads
La carpeta `uploads/habitaciones/` ya existe en el proyecto.
Asegúrate de que tenga permisos de escritura (en Windows no hace falta configurar nada extra).

### 6. Acceder al sistema
Abre tu navegador y ve a:
```
http://localhost/sgat-urmiri/
```

---

## 👤 Credenciales de Acceso

| Rol           | Correo              | Contraseña |
|---------------|---------------------|------------|
| Administrador | admin@urmiri.bo     | password   |
| Operador      | operador@urmiri.bo  | password   |

> **Nota:** El hash de contraseña en la base de datos corresponde a `password`.
> Puedes generar tu propia contraseña con:
> ```php
> echo password_hash('tu_nueva_contraseña', PASSWORD_BCRYPT);
> ```

---

## 📁 Estructura del Proyecto

```
sgat-urmiri/
├── config/
│   ├── database.php        ← Conexión PDO a MySQL
│   └── helpers.php         ← Funciones auxiliares y sesión
├── controllers/
│   ├── auth_controller.php      ← Login, registro, logout
│   ├── reserva_controller.php   ← CRUD reservas + AJAX disponibilidad
│   └── habitacion_controller.php ← CRUD habitaciones con subida de imágenes
├── views/
│   ├── partials/
│   │   ├── navbar.php      ← Barra de navegación
│   │   ├── footer.php      ← Pie de página
│   │   └── flash.php       ← Mensajes de éxito/error
│   ├── auth/
│   │   ├── login.php       ← Formulario de inicio de sesión
│   │   └── registro.php    ← Formulario de registro
│   ├── habitaciones/
│   │   └── habitaciones.php ← Galería pública de habitaciones
│   ├── reservas/
│   │   ├── nueva_reserva.php   ← Formulario de reserva con AJAX
│   │   └── mis_reservas.php    ← Historial del turista
│   └── admin/
│       ├── sidebar.php     ← Menú lateral admin
│       ├── dashboard.php   ← Panel con estadísticas
│       ├── reservas.php    ← Gestión de reservas
│       ├── habitaciones.php ← CRUD habitaciones
│       ├── usuarios.php    ← Gestión de usuarios
│       └── servicios.php   ← CRUD servicios
├── assets/
│   ├── css/style.css       ← Estilos personalizados Bootstrap
│   ├── js/main.js          ← JavaScript + AJAX disponibilidad
│   └── images/             ← Imágenes estáticas del sitio
├── uploads/
│   └── habitaciones/       ← Imágenes subidas por el admin
├── database/
│   └── sgat_urmiri.sql     ← Script SQL completo
└── index.php               ← Página principal
```

---

## ✅ Funcionalidades Implementadas

### Módulo de Autenticación
- [x] Registro con validación y bcrypt
- [x] Login con verificación de hash
- [x] Logout seguro
- [x] Roles: administrador, operador, turista
- [x] Protección de rutas por rol

### Módulo de Habitaciones
- [x] Galería con cards Bootstrap
- [x] Subida de imágenes
- [x] Estado: DISPONIBLE (verde) / OCUPADA (rojo)
- [x] Precio en bolivianos (Bs.)
- [x] Filtros por estado

### Módulo de Reservas
- [x] Verificación de disponibilidad en tiempo real (AJAX)
- [x] Cálculo automático de noches y total en Bs.
- [x] Código único de reserva (RES-XXXXXX-YYYY)
- [x] Estados: Pendiente / Confirmada / Cancelada
- [x] Historial personal del turista

### Panel Administrativo
- [x] Dashboard con estadísticas
- [x] CRUD completo de habitaciones
- [x] CRUD completo de servicios
- [x] Gestión de reservas (confirmar/cancelar)
- [x] Gestión de usuarios (activar/desactivar)

### Seguridad
- [x] Consultas preparadas con PDO (previene SQL Injection)
- [x] Contraseñas con bcrypt
- [x] Sanitización de entradas con htmlspecialchars
- [x] Verificación de roles antes de cada acción

---

## 🔧 Personalización

### Cambiar el precio de una habitación
Ve a **Panel Admin → Habitaciones → Editar**

### Agregar nuevas habitaciones
Ve a **Panel Admin → Habitaciones → Nueva Habitación**
Sube una imagen JPG/PNG/WebP

### Cambiar la URL base
Si el proyecto está en otra carpeta, edita `config/database.php`:
```php
define('BASE_URL', 'http://localhost/TU_CARPETA/');
```

---

## 📞 Soporte
Sistema desarrollado como proyecto universitario para el
Complejo Turístico de Aguas Termales Urmiri, La Paz — Bolivia.
