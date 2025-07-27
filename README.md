# Sistema Base - MVC PHP

Sistema base desarrollado en PHP puro siguiendo el patrón MVC (Model-View-Controller) para la gestión de usuarios con roles y permisos.

## Características

- ✅ Arquitectura MVC pura (sin frameworks)
- ✅ Registro de usuarios con validación
- ✅ Sistema de roles (admin, editor, viewer)
- ✅ Validaciones del lado cliente y servidor
- ✅ Seguridad: hash de contraseñas, protección CSRF
- ✅ Interfaz responsive con Bootstrap 5
- ✅ Prevención de SQL injection
- ✅ Sanitización de entradas

## Requisitos del Sistema

- PHP 7.4 o superior
- MySQL 5.7+ / MariaDB 10.3+
- Servidor web con soporte para URL rewriting (Apache/Nginx)

## Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/danjohn007/Sistema-Base.git
   cd Sistema-Base
   ```

2. **Configurar base de datos**
   ```bash
   cp .env.example .env
   # Editar .env con tus credenciales de base de datos
   ```

3. **Crear base de datos y tablas**
   ```bash
   mysql -u root -p < database/migrations/create_users_table.sql
   ```

4. **Configurar servidor web**
   - Apuntar el DocumentRoot a la carpeta `public/`
   - Asegurar que mod_rewrite esté habilitado

## Estructura del Proyecto

```
/
├── public/                 # Punto de entrada público
│   ├── index.php          # Entrada principal
│   ├── css/               # Archivos CSS
│   ├── js/                # Archivos JavaScript
│   └── .htaccess          # Configuración Apache
├── app/
│   ├── controllers/       # Controladores MVC
│   ├── models/           # Modelos de datos
│   ├── views/            # Vistas/templates
│   └── core/             # Clases principales del framework
├── config/               # Archivos de configuración
├── database/
│   └── migrations/       # Migraciones de base de datos
└── README.md
```

## Uso

### Registro de Usuarios

1. Navegar a `/users/register`
2. Completar el formulario con:
   - Nombre completo
   - Correo electrónico
   - Contraseña (mínimo 8 caracteres, mayúsculas y números)
   - Rol (viewer, editor, admin)
3. El sistema validará los datos y creará el usuario

### Usuario Administrador Inicial

El sistema incluye un usuario administrador por defecto:
- **Email:** admin@sistema.com
- **Contraseña:** password123

⚠️ **Importante:** Cambiar estas credenciales en producción.

## Desarrollo

### Agregar Nuevas Rutas

Editar `public/index.php`:

```php
$router->get('/nueva-ruta', 'Controller@method');
$router->post('/nueva-ruta', 'Controller@method');
```

### Crear Nuevos Controladores

```php
<?php
require_once __DIR__ . '/../core/Controller.php';

class MiController extends Controller {
    public function index() {
        $this->view('mi-vista', ['data' => $data]);
    }
}
```

### Crear Nuevos Modelos

```php
<?php
require_once __DIR__ . '/../core/Model.php';

class MiModel extends Model {
    protected $table = 'mi_tabla';
    
    public function customMethod() {
        // Lógica del modelo
    }
}
```

## Seguridad

- Contraseñas hasheadas con `password_hash()`
- Protección CSRF en formularios
- Sanitización de entradas
- Prevención de SQL injection con prepared statements
- Headers de seguridad configurados

## Próximas Funcionalidades

- [ ] Sistema de autenticación (login/logout)
- [ ] Verificación por email
- [ ] Gestión completa de usuarios (CRUD)
- [ ] Sistema de permisos granular
- [ ] Logging de actividades
- [ ] Administración de tareas
- [ ] Dashboard con reportes

## Contribuciones

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crear una rama para tu feature (`git checkout -b feature/nueva-caracteristica`)
3. Commit tus cambios (`git commit -am 'Agrega nueva característica'`)
4. Push a la rama (`git push origin feature/nueva-caracteristica`)
5. Crear un Pull Request

## Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo [LICENSE](LICENSE) para más detalles.
