<?php
/**
 * Sistema Base - Instalador para Servidor
 * Ejecutable para instalación en servidor Apache desde cPanel
 * 
 * Este script permite configurar la base de datos del sistema
 * solicitando los parámetros de conexión necesarios.
 */

// Verificar si ya existe configuración
$envFile = dirname(__DIR__) . '/.env';
$isInstalled = file_exists($envFile);

// Procesar formulario de instalación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$isInstalled) {
    $db_name = trim($_POST['db_name'] ?? '');
    $db_user = trim($_POST['db_user'] ?? '');
    $db_pass = $_POST['db_pass'] ?? '';
    $db_host = trim($_POST['db_host'] ?? 'localhost');
    $db_port = trim($_POST['db_port'] ?? '3306');
    $create_db = isset($_POST['create_db']);
    
    $errors = [];
    $success = false;
    
    // Validaciones básicas
    if (empty($db_name)) {
        $errors[] = 'El nombre de la base de datos es requerido';
    }
    if (empty($db_user)) {
        $errors[] = 'El usuario de la base de datos es requerido';
    }
    
    if (empty($errors)) {
        try {
            // Probar conexión a la base de datos
            $dsn = "mysql:host={$db_host};port={$db_port};charset=utf8mb4";
            if (!$create_db) {
                $dsn .= ";dbname={$db_name}";
            }
            
            $pdo = new PDO($dsn, $db_user, $db_pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            
            // Si se seleccionó crear base de datos
            if ($create_db) {
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $pdo->exec("USE `{$db_name}`");
                
                // Crear tabla de usuarios
                $sqlFile = dirname(__DIR__) . '/database/migrations/create_users_table.sql';
                if (file_exists($sqlFile)) {
                    $sql = file_get_contents($sqlFile);
                    // Remover las líneas de CREATE DATABASE y USE
                    $sql = preg_replace('/^CREATE DATABASE.*$/m', '', $sql);
                    $sql = preg_replace('/^USE.*$/m', '', $sql);
                    $pdo->exec($sql);
                }
            }
            
            // Crear archivo .env
            $envContent = "# Configuración de Base de Datos\n";
            $envContent .= "DB_HOST={$db_host}\n";
            $envContent .= "DB_PORT={$db_port}\n";
            $envContent .= "DB_DATABASE={$db_name}\n";
            $envContent .= "DB_USERNAME={$db_user}\n";
            $envContent .= "DB_PASSWORD={$db_pass}\n\n";
            $envContent .= "# Configuración de la aplicación\n";
            $envContent .= "APP_ENV=production\n";
            $envContent .= "APP_DEBUG=false\n";
            $envContent .= "APP_URL=" . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}\n\n";
            $envContent .= "# Configuración de sesión\n";
            $envContent .= "SESSION_LIFETIME=120\n";
            
            if (file_put_contents($envFile, $envContent)) {
                $success = true;
            } else {
                $errors[] = 'No se pudo crear el archivo de configuración. Verifique los permisos del directorio.';
            }
            
        } catch (PDOException $e) {
            $errors[] = 'Error de conexión a la base de datos: ' . $e->getMessage();
        } catch (Exception $e) {
            $errors[] = 'Error durante la instalación: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Base - Instalador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .installer-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        .installer-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .installer-body {
            padding: 2rem;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="installer-card">
                    <div class="installer-header">
                        <h1 class="h3 mb-0">Sistema Base</h1>
                        <p class="mb-0">Instalador para Servidor</p>
                    </div>
                    
                    <div class="installer-body">
                        <?php if ($isInstalled): ?>
                            <div class="alert alert-info">
                                <h4 class="alert-heading">¡Sistema ya instalado!</h4>
                                <p>El sistema ya ha sido configurado anteriormente.</p>
                                <hr>
                                <p class="mb-0">
                                    <a href="/" class="btn btn-primary">Ir al Sistema</a>
                                    <small class="text-muted d-block mt-2">
                                        <strong>Nota de seguridad:</strong> Por favor elimine este archivo (install.php) del servidor por motivos de seguridad.
                                    </small>
                                </p>
                            </div>
                        <?php elseif (isset($success) && $success): ?>
                            <div class="alert alert-success">
                                <h4 class="alert-heading">¡Instalación exitosa!</h4>
                                <p>El sistema ha sido configurado correctamente.</p>
                                <hr>
                                <div class="d-grid gap-2">
                                    <a href="/" class="btn btn-primary">Ir al Sistema</a>
                                    <button onclick="deleteSelf()" class="btn btn-outline-danger btn-sm">
                                        Eliminar instalador (recomendado)
                                    </button>
                                </div>
                                <small class="text-muted mt-2 d-block">
                                    <strong>Credenciales de administrador:</strong><br>
                                    Email: admin@sistema.com<br>
                                    Contraseña: password123<br>
                                    <em>¡Cambie estas credenciales inmediatamente!</em>
                                </small>
                            </div>
                        <?php else: ?>
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <h5>Errores encontrados:</h5>
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?= htmlspecialchars($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="db_host" class="form-label">Servidor de Base de Datos</label>
                                    <input type="text" class="form-control" id="db_host" name="db_host" 
                                           value="<?= htmlspecialchars($_POST['db_host'] ?? 'localhost') ?>" required>
                                    <div class="form-text">Generalmente 'localhost' en hosting compartido</div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="db_name" class="form-label">Nombre de Base de Datos *</label>
                                            <input type="text" class="form-control" id="db_name" name="db_name" 
                                                   value="<?= htmlspecialchars($_POST['db_name'] ?? '') ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="db_port" class="form-label">Puerto</label>
                                            <input type="number" class="form-control" id="db_port" name="db_port" 
                                                   value="<?= htmlspecialchars($_POST['db_port'] ?? '3306') ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="db_user" class="form-label">Usuario de Base de Datos *</label>
                                    <input type="text" class="form-control" id="db_user" name="db_user" 
                                           value="<?= htmlspecialchars($_POST['db_user'] ?? '') ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="db_pass" class="form-label">Contraseña de Base de Datos</label>
                                    <input type="password" class="form-control" id="db_pass" name="db_pass">
                                    <div class="form-text">Déjelo vacío si no tiene contraseña</div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="create_db" name="create_db"
                                               <?= isset($_POST['create_db']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="create_db">
                                            Crear base de datos y tablas automáticamente
                                        </label>
                                        <div class="form-text">
                                            Marque esta opción si la base de datos no existe o desea recrear las tablas
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        Instalar Sistema
                                    </button>
                                </div>
                            </form>
                            
                            <div class="mt-4 text-center">
                                <small class="text-muted">
                                    <strong>Nota:</strong> Asegúrese de tener los permisos necesarios para crear archivos 
                                    en el directorio del sistema y acceso a la base de datos.
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteSelf() {
            if (confirm('¿Está seguro de que desea eliminar el instalador? Esta acción no se puede deshacer.')) {
                fetch('install.php?delete=1', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'delete_installer=1'
                })
                .then(response => response.text())
                .then(data => {
                    if (data.includes('deleted')) {
                        alert('Instalador eliminado exitosamente.');
                        window.location.href = '/';
                    } else {
                        alert('No se pudo eliminar el instalador. Por favor elimínelo manualmente.');
                    }
                })
                .catch(error => {
                    alert('Error al eliminar el instalador. Por favor elimínelo manualmente.');
                });
            }
        }
    </script>
</body>
</html>

<?php
// Funcionalidad para auto-eliminación del instalador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_installer'])) {
    if (unlink(__FILE__)) {
        echo 'deleted';
    } else {
        echo 'error';
    }
    exit;
}
?>