<?php

require_once __DIR__ . '/../core/Model.php';

class User extends Model {
    protected $table = 'users';

    public function create($userData) {
        // Sanitize input data
        $userData = $this->sanitize($userData);
        
        // Validate data before creating
        $validation = $this->validateUserData($userData);
        if (!$validation['valid']) {
            throw new Exception(implode(', ', $validation['errors']));
        }

        // Check if user already exists
        if ($this->userExists($userData['email'])) {
            throw new Exception('El email ya está registrado');
        }

        // Hash password
        $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        // Add timestamps
        $userData['created_at'] = date('Y-m-d H:i:s');
        $userData['updated_at'] = date('Y-m-d H:i:s');

        return $this->insert($userData);
    }

    public function validateUserData($data) {
        $errors = [];
        $valid = true;

        // Validate required fields
        $requiredFields = ['name', 'email', 'password', 'role'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[] = "El campo {$field} es obligatorio";
                $valid = false;
            }
        }

        // Validate email format
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El formato del email no es válido';
            $valid = false;
        }

        // Validate password strength
        if (!empty($data['password'])) {
            $password = $data['password'];
            if (strlen($password) < 8) {
                $errors[] = 'La contraseña debe tener al menos 8 caracteres';
                $valid = false;
            }
            if (!preg_match('/[A-Z]/', $password)) {
                $errors[] = 'La contraseña debe contener al menos una letra mayúscula';
                $valid = false;
            }
            if (!preg_match('/[0-9]/', $password)) {
                $errors[] = 'La contraseña debe contener al menos un número';
                $valid = false;
            }
        }

        // Validate role
        $validRoles = ['admin', 'editor', 'viewer'];
        if (!empty($data['role']) && !in_array($data['role'], $validRoles)) {
            $errors[] = 'El rol seleccionado no es válido';
            $valid = false;
        }

        // Validate name length
        if (!empty($data['name']) && strlen($data['name']) > 100) {
            $errors[] = 'El nombre no puede exceder 100 caracteres';
            $valid = false;
        }

        return [
            'valid' => $valid,
            'errors' => $errors
        ];
    }

    public function userExists($email) {
        $user = $this->findBy('email', $email);
        return $user !== false;
    }

    public function getUserByEmail($email) {
        return $this->findBy('email', $email);
    }

    public function getUserById($id) {
        return $this->find($id);
    }

    public function getAllUsers() {
        return $this->all();
    }

    public function updateUser($id, $userData) {
        // Sanitize input data
        $userData = $this->sanitize($userData);
        
        // Add updated timestamp
        $userData['updated_at'] = date('Y-m-d H:i:s');
        
        // If password is being updated, hash it
        if (isset($userData['password']) && !empty($userData['password'])) {
            $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        } else {
            unset($userData['password']); // Don't update password if empty
        }

        return $this->update($id, $userData);
    }

    public function deleteUser($id) {
        return $this->delete($id);
    }

    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}