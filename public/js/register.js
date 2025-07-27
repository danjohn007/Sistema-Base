document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const roleSelect = document.getElementById('role');
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirm');
    const submitBtn = document.getElementById('submitBtn');
    const togglePassword = document.getElementById('togglePassword');
    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');

    // Password visibility toggle
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });

    togglePasswordConfirm.addEventListener('click', function() {
        const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordConfirmInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });

    // Real-time validation functions
    function validateName() {
        const name = nameInput.value.trim();
        const nameError = document.getElementById('name-error');
        
        if (name.length === 0) {
            setFieldError(nameInput, nameError, 'El nombre es obligatorio');
            return false;
        }
        
        if (name.length > 100) {
            setFieldError(nameInput, nameError, 'El nombre no puede exceder 100 caracteres');
            return false;
        }
        
        if (name.length < 2) {
            setFieldError(nameInput, nameError, 'El nombre debe tener al menos 2 caracteres');
            return false;
        }
        
        setFieldValid(nameInput, nameError);
        return true;
    }

    function validateEmail() {
        const email = emailInput.value.trim();
        const emailError = document.getElementById('email-error');
        
        if (email.length === 0) {
            setFieldError(emailInput, emailError, 'El email es obligatorio');
            return false;
        }
        
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            setFieldError(emailInput, emailError, 'Por favor ingresa un email válido');
            return false;
        }
        
        setFieldValid(emailInput, emailError);
        
        // Check email availability via AJAX
        checkEmailAvailability(email);
        return true;
    }

    function validateRole() {
        const role = roleSelect.value;
        const roleError = document.getElementById('role-error');
        
        if (role === '') {
            setFieldError(roleSelect, roleError, 'Por favor selecciona un rol');
            return false;
        }
        
        setFieldValid(roleSelect, roleError);
        return true;
    }

    function validatePassword() {
        const password = passwordInput.value;
        const passwordError = document.getElementById('password-error');
        
        if (password.length === 0) {
            setFieldError(passwordInput, passwordError, 'La contraseña es obligatoria');
            return false;
        }
        
        if (password.length < 8) {
            setFieldError(passwordInput, passwordError, 'La contraseña debe tener al menos 8 caracteres');
            return false;
        }
        
        if (!/[A-Z]/.test(password)) {
            setFieldError(passwordInput, passwordError, 'La contraseña debe contener al menos una letra mayúscula');
            return false;
        }
        
        if (!/[0-9]/.test(password)) {
            setFieldError(passwordInput, passwordError, 'La contraseña debe contener al menos un número');
            return false;
        }
        
        setFieldValid(passwordInput, passwordError);
        
        // Re-validate password confirmation if it has been filled
        if (passwordConfirmInput.value) {
            validatePasswordConfirm();
        }
        
        return true;
    }

    function validatePasswordConfirm() {
        const password = passwordInput.value;
        const passwordConfirm = passwordConfirmInput.value;
        const passwordConfirmError = document.getElementById('password-confirm-error');
        
        if (passwordConfirm.length === 0) {
            setFieldError(passwordConfirmInput, passwordConfirmError, 'Confirma tu contraseña');
            return false;
        }
        
        if (password !== passwordConfirm) {
            setFieldError(passwordConfirmInput, passwordConfirmError, 'Las contraseñas no coinciden');
            return false;
        }
        
        setFieldValid(passwordConfirmInput, passwordConfirmError);
        return true;
    }

    function setFieldError(field, errorElement, message) {
        field.classList.add('is-invalid');
        field.classList.remove('is-valid');
        errorElement.textContent = message;
    }

    function setFieldValid(field, errorElement) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
        errorElement.textContent = '';
    }

    // Email availability check
    function checkEmailAvailability(email) {
        fetch('/users/validate-email', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'email=' + encodeURIComponent(email)
        })
        .then(response => response.json())
        .then(data => {
            const emailError = document.getElementById('email-error');
            if (!data.valid) {
                setFieldError(emailInput, emailError, data.message);
            } else {
                setFieldValid(emailInput, emailError);
            }
        })
        .catch(error => {
            console.log('Error checking email availability:', error);
        });
    }

    // Event listeners for real-time validation
    nameInput.addEventListener('blur', validateName);
    nameInput.addEventListener('input', function() {
        if (this.classList.contains('is-invalid') || this.classList.contains('is-valid')) {
            validateName();
        }
    });

    emailInput.addEventListener('blur', validateEmail);
    emailInput.addEventListener('input', function() {
        if (this.classList.contains('is-invalid') || this.classList.contains('is-valid')) {
            // Debounce email validation
            clearTimeout(this.emailTimeout);
            this.emailTimeout = setTimeout(validateEmail, 500);
        }
    });

    roleSelect.addEventListener('change', validateRole);

    passwordInput.addEventListener('blur', validatePassword);
    passwordInput.addEventListener('input', function() {
        if (this.classList.contains('is-invalid') || this.classList.contains('is-valid')) {
            validatePassword();
        }
    });

    passwordConfirmInput.addEventListener('blur', validatePasswordConfirm);
    passwordConfirmInput.addEventListener('input', function() {
        if (this.classList.contains('is-invalid') || this.classList.contains('is-valid')) {
            validatePasswordConfirm();
        }
    });

    // Form submission validation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const isNameValid = validateName();
        const isEmailValid = validateEmail();
        const isRoleValid = validateRole();
        const isPasswordValid = validatePassword();
        const isPasswordConfirmValid = validatePasswordConfirm();
        
        if (isNameValid && isEmailValid && isRoleValid && isPasswordValid && isPasswordConfirmValid) {
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Registrando...';
            
            // Submit form
            this.submit();
        } else {
            // Focus on first invalid field
            const firstInvalidField = form.querySelector('.is-invalid');
            if (firstInvalidField) {
                firstInvalidField.focus();
            }
        }
    });

    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});