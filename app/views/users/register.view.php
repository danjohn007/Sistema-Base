<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Sistema Base' ?></title>
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
        .invalid-feedback {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
        <div class="row w-100 justify-content-center">
            <div class="col-lg-5 col-md-7 col-sm-9">
                <div class="card">
                    <div class="card-header bg-transparent text-center border-0 pt-4">
                        <h2 class="mb-0">
                            <i class="bi bi-person-plus-fill text-primary"></i>
                            Registro de Usuario
                        </h2>
                        <p class="text-muted mt-2">Crea una nueva cuenta en el sistema</p>
                    </div>
                    <div class="card-body px-4 pb-4">
                        
                        <!-- Mensajes de éxito -->
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <?= htmlspecialchars($success) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Mensajes de error -->
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="/users/register" id="registerForm" novalidate>
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            
                            <!-- Nombre -->
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="bi bi-person me-1"></i>Nombre Completo
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="name" 
                                       name="name" 
                                       value="<?= htmlspecialchars($old_input['name'] ?? '') ?>"
                                       required 
                                       maxlength="100">
                                <div class="invalid-feedback" id="name-error"></div>
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope me-1"></i>Correo Electrónico
                                </label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       value="<?= htmlspecialchars($old_input['email'] ?? '') ?>"
                                       required>
                                <div class="invalid-feedback" id="email-error"></div>
                            </div>

                            <!-- Rol -->
                            <div class="mb-3">
                                <label for="role" class="form-label">
                                    <i class="bi bi-shield-check me-1"></i>Rol
                                </label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">Seleccionar rol...</option>
                                    <option value="viewer" <?= ($old_input['role'] ?? '') === 'viewer' ? 'selected' : '' ?>>
                                        Visualizador
                                    </option>
                                    <option value="editor" <?= ($old_input['role'] ?? '') === 'editor' ? 'selected' : '' ?>>
                                        Editor
                                    </option>
                                    <option value="admin" <?= ($old_input['role'] ?? '') === 'admin' ? 'selected' : '' ?>>
                                        Administrador
                                    </option>
                                </select>
                                <div class="invalid-feedback" id="role-error"></div>
                            </div>

                            <!-- Contraseña -->
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock me-1"></i>Contraseña
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control" 
                                           id="password" 
                                           name="password" 
                                           required 
                                           minlength="8">
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">
                                    <small>Mínimo 8 caracteres, debe incluir mayúsculas y números</small>
                                </div>
                                <div class="invalid-feedback" id="password-error"></div>
                            </div>

                            <!-- Confirmar Contraseña -->
                            <div class="mb-4">
                                <label for="password_confirm" class="form-label">
                                    <i class="bi bi-lock-fill me-1"></i>Confirmar Contraseña
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirm" 
                                           name="password_confirm" 
                                           required>
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            id="togglePasswordConfirm">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback" id="password-confirm-error"></div>
                            </div>

                            <!-- Botón de registro -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <i class="bi bi-person-plus me-2"></i>Registrar Usuario
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <p class="text-muted">
                                ¿Ya tienes una cuenta? 
                                <a href="/login" class="text-decoration-none">Iniciar Sesión</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/register.js"></script>
</body>
</html>