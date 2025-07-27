<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';

class UserController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function showRegister() {
        $data = [
            'title' => 'Registro de Usuario',
            'csrf_token' => $this->generateCsrfToken(),
            'errors' => $_SESSION['errors'] ?? [],
            'old_input' => $_SESSION['old_input'] ?? [],
            'success' => $_SESSION['success'] ?? ''
        ];

        // Clear session messages
        unset($_SESSION['errors'], $_SESSION['old_input'], $_SESSION['success']);

        $this->view('users/register.view', $data);
    }

    public function register() {
        try {
            // Validate CSRF token
            $this->validateCsrfToken();

            // Get and sanitize input data
            $input = $this->sanitizeInput($_POST);
            
            $userData = [
                'name' => $input['name'] ?? '',
                'email' => $input['email'] ?? '',
                'password' => $input['password'] ?? '',
                'role' => $input['role'] ?? 'viewer'
            ];

            // Store old input in session for form repopulation on error
            $_SESSION['old_input'] = [
                'name' => $userData['name'],
                'email' => $userData['email'],
                'role' => $userData['role']
            ];

            // Validate password confirmation
            $passwordConfirm = $input['password_confirm'] ?? '';
            if ($userData['password'] !== $passwordConfirm) {
                throw new Exception('Las contraseñas no coinciden');
            }

            // Create user
            $userId = $this->userModel->create($userData);

            if ($userId) {
                $_SESSION['success'] = 'Usuario registrado exitosamente';
                unset($_SESSION['old_input']); // Clear old input on success
                $this->redirect('users/register');
            } else {
                throw new Exception('Error al crear el usuario');
            }

        } catch (Exception $e) {
            $_SESSION['errors'] = [$e->getMessage()];
            $this->redirect('users/register');
        }
    }

    public function index() {
        try {
            $users = $this->userModel->getAllUsers();
            
            $data = [
                'title' => 'Lista de Usuarios',
                'users' => $users
            ];

            $this->view('users/index.view', $data);
        } catch (Exception $e) {
            $_SESSION['errors'] = [$e->getMessage()];
            $this->redirect('');
        }
    }

    public function show($id) {
        try {
            $user = $this->userModel->getUserById($id);
            
            if (!$user) {
                throw new Exception('Usuario no encontrado');
            }

            $data = [
                'title' => 'Detalle de Usuario',
                'user' => $user
            ];

            $this->view('users/show.view', $data);
        } catch (Exception $e) {
            $_SESSION['errors'] = [$e->getMessage()];
            $this->redirect('users');
        }
    }

    // API endpoint for AJAX validation
    public function validateEmail() {
        header('Content-Type: application/json');
        
        $email = $_POST['email'] ?? '';
        
        if (empty($email)) {
            $this->json(['valid' => false, 'message' => 'Email es requerido']);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(['valid' => false, 'message' => 'Formato de email inválido']);
        }

        $exists = $this->userModel->userExists($email);
        
        if ($exists) {
            $this->json(['valid' => false, 'message' => 'El email ya está registrado']);
        }

        $this->json(['valid' => true, 'message' => 'Email disponible']);
    }
}