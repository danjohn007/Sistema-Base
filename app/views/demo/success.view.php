<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Registro Exitoso' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }
        .success-icon {
            font-size: 4rem;
            color: #28a745;
            animation: bounce 1s ease-in-out;
        }
        @keyframes bounce {
            0%, 20%, 60%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            80% {
                transform: translateY(-5px);
            }
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
    <div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
        <div class="row w-100 justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-10">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="success-icon mb-4">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        
                        <h2 class="text-success mb-4">¡Registro Exitoso!</h2>
                        
                        <?php if ($user): ?>
                            <div class="alert alert-success mx-3" role="alert">
                                <h5 class="alert-heading">Usuario Creado (Demo)</h5>
                                <hr>
                                <p><strong>Nombre:</strong> <?= htmlspecialchars($user['name']) ?></p>
                                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                                <p><strong>Rol:</strong> 
                                    <?php
                                    $roles = [
                                        'admin' => 'Administrador',
                                        'editor' => 'Editor', 
                                        'viewer' => 'Visualizador'
                                    ];
                                    echo $roles[$user['role']] ?? ucfirst($user['role']);
                                    ?>
                                </p>
                                <p class="mb-0"><small class="text-muted">Fecha: <?= date('d/m/Y H:i:s') ?></small></p>
                            </div>
                        <?php endif; ?>
                        
                        <p class="text-muted mb-4">
                            El usuario ha sido registrado exitosamente en el sistema.<br>
                            <small><em>Nota: Esta es una demostración sin conexión a base de datos.</em></small>
                        </p>
                        
                        <div class="d-grid gap-2 d-md-block">
                            <a href="/demo/register" class="btn btn-primary">
                                <i class="bi bi-person-plus me-2"></i>Registrar Otro Usuario
                            </a>
                            <a href="/demo/register" class="btn btn-outline-secondary">
                                <i class="bi bi-list me-2"></i>Ver Lista de Usuarios
                            </a>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="text-muted">
                            <h6>Características del Sistema:</h6>
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li><i class="bi bi-check-circle text-success me-2"></i>Arquitectura MVC</li>
                                        <li><i class="bi bi-check-circle text-success me-2"></i>Validación de datos</li>
                                        <li><i class="bi bi-check-circle text-success me-2"></i>Seguridad CSRF</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li><i class="bi bi-check-circle text-success me-2"></i>Hash de contraseñas</li>
                                        <li><i class="bi bi-check-circle text-success me-2"></i>Interfaz responsive</li>
                                        <li><i class="bi bi-check-circle text-success me-2"></i>Validación en tiempo real</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>