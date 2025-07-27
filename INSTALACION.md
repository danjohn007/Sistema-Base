# Manual de Instalación - Sistema Base

## Instalador para Servidor Apache/cPanel

Este sistema incluye un instalador web (`install.php`) diseñado específicamente para facilitar la instalación en servidores Apache a través de cPanel.

### Características del Instalador

- ✅ Interfaz web amigable para configuración de base de datos
- ✅ Validación de conexión a base de datos en tiempo real  
- ✅ Creación automática de archivo `.env` con configuración
- ✅ Opción para crear base de datos y tablas automáticamente
- ✅ Auto-eliminación por seguridad después de la instalación
- ✅ Totalmente compatible con hosting compartido/cPanel

### Instrucciones de Instalación

#### 1. Subir archivos al servidor

1. Descarga o clona el repositorio
2. Sube todos los archivos a tu servidor web via cPanel File Manager o FTP
3. Asegúrate de que el directorio `public` sea el DocumentRoot de tu dominio

#### 2. Ejecutar el instalador

1. Navega a: `http://tu-dominio.com/install.php`
2. Completa el formulario con los datos de tu base de datos:

   **Parámetros requeridos:**
   - `$db_name`: Nombre de la base de datos
   - `$db_user`: Usuario de la base de datos  
   - `$db_pass`: Contraseña de la base de datos

   **Parámetros opcionales:**
   - `db_host`: Servidor de base de datos (por defecto: localhost)
   - `db_port`: Puerto de conexión (por defecto: 3306)

3. Marca "Crear base de datos y tablas automáticamente" si:
   - La base de datos no existe
   - Necesitas crear las tablas del sistema
   - Quieres insertar datos iniciales (usuario admin)

#### 3. Finalizar instalación

1. El instalador validará la conexión a la base de datos
2. Creará el archivo `.env` con la configuración proporcionada
3. Si seleccionaste la opción, creará la estructura de la base de datos
4. Una vez completado, elimina el archivo `install.php` por seguridad

### Configuración de cPanel

#### Configurar Base de Datos MySQL

1. **Accede a cPanel → MySQL Databases**
2. **Crear base de datos:**
   - Nombre: `tu_usuario_sistema_base` (ejemplo)
   - Clic en "Create Database"

3. **Crear usuario de base de datos:**
   - Nombre: `tu_usuario_db` (ejemplo)
   - Contraseña: (genera una segura)
   - Clic en "Create User"

4. **Asignar usuario a base de datos:**
   - Selecciona usuario y base de datos creados
   - Marca "ALL PRIVILEGES"
   - Clic en "Make Changes"

#### Configurar Subdirectorio (opcional)

Si instalas en un subdirectorio (ej: `/sistema`):

1. Crear subdirectorio en File Manager
2. Subir archivos del sistema
3. Acceder via: `http://tu-dominio.com/sistema/install.php`

### Credenciales por Defecto

Después de la instalación exitosa, puedes acceder con:

- **Email:** admin@sistema.com
- **Contraseña:** password123
- **Rol:** Administrador

⚠️ **IMPORTANTE:** Cambia estas credenciales inmediatamente después del primer acceso.

### Solución de Problemas

#### Error de conexión a base de datos
- Verifica que los datos de conexión sean correctos
- Asegúrate de que el usuario tenga permisos en la base de datos
- Comprueba que el servidor MySQL esté funcionando

#### Error de permisos de archivos
- El directorio debe tener permisos de escritura para crear `.env`
- En cPanel: File Manager → Permissions → 755 para directorios, 644 para archivos

#### Error "Sistema ya instalado"
- El archivo `.env` ya existe
- Si necesitas reinstalar, elimina el archivo `.env` primero

### Funciones de Seguridad

- **Auto-eliminación:** El instalador puede eliminarse automáticamente
- **Validación de entrada:** Todos los datos son validados y sanitizados
- **Configuración de producción:** Se configura automáticamente para producción
- **Protección de archivos:** Configuración Apache incluida para proteger archivos sensibles

### Estructura después de la instalación

```
/public_html/ (o tu directorio web)
├── .env                     # ✅ Configuración generada
├── .env.example            # Archivo de ejemplo
├── app/                    # Aplicación MVC
├── config/                 # Configuración del sistema
├── database/               # Migraciones y esquemas
└── public/                 # Archivos públicos
    ├── index.php          # Punto de entrada
    ├── install.php        # ❌ Eliminar después de instalación
    ├── css/               # Recursos CSS
    ├── js/                # Recursos JavaScript
    └── .htaccess         # Configuración Apache
```

### Soporte

Si encuentras problemas durante la instalación:

1. Verifica los requisitos del sistema (PHP 7.4+, MySQL 5.7+)
2. Comprueba los logs de error de cPanel
3. Asegúrate de que mod_rewrite esté habilitado
4. Contacta al soporte de tu hosting si persisten los problemas

---

**Nota:** Este instalador está diseñado específicamente para entornos de hosting compartido y cPanel, facilitando la instalación sin necesidad de acceso SSH o línea de comandos.