<?php

abstract class Controller {
    protected function view($view, $data = []) {
        extract($data);
        
        $viewPath = __DIR__ . "/../views/{$view}.php";
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            throw new Exception("View not found: {$view}");
        }
    }

    protected function redirect($path) {
        $baseUrl = $this->getBaseUrl();
        // Remove leading slash from path to avoid double slashes
        $path = ltrim($path, '/');
        // Ensure baseUrl doesn't end with slash when adding path
        $baseUrl = rtrim($baseUrl, '/');
        header("Location: {$baseUrl}/{$path}");
        exit();
    }

    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    protected function validateCsrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            throw new Exception('CSRF token not found in session');
        }

        $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
        
        if (!hash_equals($_SESSION['csrf_token'], $token)) {
            throw new Exception('Invalid CSRF token');
        }
    }

    protected function generateCsrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $path = dirname($_SERVER['SCRIPT_NAME']);
        return $protocol . '://' . $host . $path;
    }

    protected function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([$this, 'sanitizeInput'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}