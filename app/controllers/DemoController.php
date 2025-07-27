<?php

require_once __DIR__ . '/../core/Controller.php';

class DemoController extends Controller {
    
    public function showRegister() {
        $data = [
            'title' => 'Registro de Usuario - Demo',
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
        // Demo registration - simulate successful registration
        $_SESSION['success'] = 'Usuario registrado exitosamente (DEMO - sin base de datos)';
        
        // Store demo data for showing
        $userData = [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'role' => $_POST['role'] ?? 'viewer'
        ];
        
        $_SESSION['demo_user'] = $userData;
        $this->redirect('demo/success');
    }

    public function success() {
        $data = [
            'title' => 'Registro Exitoso - Demo',
            'user' => $_SESSION['demo_user'] ?? null
        ];
        
        $this->view('demo/success.view', $data);
    }

    public function validateEmail() {
        header('Content-Type: application/json');
        
        $email = $_POST['email'] ?? '';
        
        if (empty($email)) {
            $this->json(['valid' => false, 'message' => 'Email es requerido']);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(['valid' => false, 'message' => 'Formato de email inválido']);
        }

        // Simulate that some emails are already taken
        $takenEmails = ['admin@sistema.com', 'test@example.com', 'user@demo.com'];
        
        if (in_array($email, $takenEmails)) {
            $this->json(['valid' => false, 'message' => 'El email ya está registrado (demo)']);
        }

        $this->json(['valid' => true, 'message' => 'Email disponible']);
    }
}