<?php
namespace App\Controllers;

use App\Models\User;

session_start();

class AuthController {
    
    public function login() {
        // Если уже авторизован - редирект на задачи
        if (isset($_SESSION['user_id'])) {
            header('Location: /tasks');
            exit;
        }

        // Обработка формы входа
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = new User();
            $user->username = $_POST['username'] ?? '';
            $user->password = $_POST['password'] ?? '';

            if (empty($user->username) || empty($user->password)) {
                $_SESSION['error'] = 'Все поля обязательны для заполнения';
                $this->showLoginForm();
                return;
            }

            if ($user->login()) {
                $_SESSION['user_id'] = $user->id;
                $_SESSION['username'] = $user->username;
                $_SESSION['success'] = 'Добро пожаловать, ' . $user->username . '!';
                header('Location: /tasks');
                exit;
            } else {
                $_SESSION['error'] = 'Неверное имя пользователя или пароль';
                $this->showLoginForm();
                return;
            }
        }

        // Показать форму входа
        $this->showLoginForm();
    }

    public function register() {
        // Если уже авторизован - редирект на задачи
        if (isset($_SESSION['user_id'])) {
            header('Location: /tasks');
            exit;
        }

        // Обработка формы регистрации
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = new User();
            $user->username = $_POST['username'] ?? '';
            $user->email = $_POST['email'] ?? '';
            $user->password = $_POST['password'] ?? '';

            // Валидация
            if (empty($user->username) || empty($user->email) || empty($user->password)) {
                $_SESSION['error'] = 'Все поля обязательны для заполнения';
                $this->showRegisterForm();
                return;
            }

            if (strlen($user->password) < 6) {
                $_SESSION['error'] = 'Пароль должен содержать минимум 6 символов';
                $this->showRegisterForm();
                return;
            }

            if ($user->usernameExists()) {
                $_SESSION['error'] = 'Пользователь с таким именем уже существует';
                $this->showRegisterForm();
                return;
            }

            if ($user->emailExists()) {
                $_SESSION['error'] = 'Пользователь с таким email уже существует';
                $this->showRegisterForm();
                return;
            }

            if ($user->register()) {
                $_SESSION['success'] = 'Регистрация успешна! Теперь войдите в систему.';
                header('Location: /login');
                exit;
            } else {
                $_SESSION['error'] = 'Ошибка при регистрации';
                $this->showRegisterForm();
                return;
            }
        }

        // Показать форму регистрации
        $this->showRegisterForm();
    }

    public function logout() {
        session_destroy();
        header('Location: /login');
        exit;
    }

    private function showLoginForm() {
        include __DIR__ . '/../../resources/views/auth/login.php';
    }

    private function showRegisterForm() {
        include __DIR__ . '/../../resources/views/auth/register.php';
    }
}
?>