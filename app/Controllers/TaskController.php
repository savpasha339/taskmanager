<?php
namespace App\Controllers;

use App\Models\Task;

session_start();

class TaskController {
    
    public function index() {
        $this->checkAuth();

        $taskModel = new Task();
        $tasks = $taskModel->getAllByUser($_SESSION['user_id']);
        $stats = $taskModel->getStats($_SESSION['user_id']);

        include __DIR__ . '/../../resources/views/tasks/index.php';
    }

    public function create() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $task = new Task();
            $task->user_id = $_SESSION['user_id'];
            $task->title = $_POST['title'] ?? '';
            $task->description = $_POST['description'] ?? '';
            $task->priority = $_POST['priority'] ?? 'medium';
            $task->due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

            if (empty($task->title)) {
                $_SESSION['error'] = 'Название задачи обязательно';
                include __DIR__ . '/../../resources/views/tasks/create.php';
                return;
            }

            if ($task->create()) {
                $_SESSION['success'] = 'Задача успешно создана!';
                header('Location: /tasks');
                exit;
            } else {
                $_SESSION['error'] = 'Ошибка при создании задачи';
                include __DIR__ . '/../../resources/views/tasks/create.php';
                return;
            }
        }

        include __DIR__ . '/../../resources/views/tasks/create.php';
    }

    public function edit() {
        $this->checkAuth();

        if (!isset($_GET['id'])) {
            $_SESSION['error'] = 'Задача не найдена';
            header('Location: /tasks');
            exit;
        }

        $taskModel = new Task();
        $task = $taskModel->getById($_GET['id'], $_SESSION['user_id']);

        if (!$task) {
            $_SESSION['error'] = 'Задача не найдена';
            header('Location: /tasks');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $taskModel = new Task();
            $taskModel->id = $_POST['id'];
            $taskModel->user_id = $_SESSION['user_id'];
            $taskModel->title = $_POST['title'] ?? '';
            $taskModel->description = $_POST['description'] ?? '';
            $taskModel->status = $_POST['status'] ?? 'pending';
            $taskModel->priority = $_POST['priority'] ?? 'medium';
            $taskModel->due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

            if (empty($taskModel->title)) {
                $_SESSION['error'] = 'Название задачи обязательно';
                include __DIR__ . '/../../resources/views/tasks/edit.php';
                return;
            }

            if ($taskModel->update()) {
                $_SESSION['success'] = 'Задача успешно обновлена!';
                header('Location: /tasks');
                exit;
            } else {
                $_SESSION['error'] = 'Ошибка при обновлении задачи';
                include __DIR__ . '/../../resources/views/tasks/edit.php';
                return;
            }
        }

        include __DIR__ . '/../../resources/views/tasks/edit.php';
    }

    public function delete() {
        $this->checkAuth();

        if (!isset($_GET['id'])) {
            $_SESSION['error'] = 'Задача не найдена';
            header('Location: /tasks');
            exit;
        }

        $taskModel = new Task();
        if ($taskModel->delete($_GET['id'], $_SESSION['user_id'])) {
            $_SESSION['success'] = 'Задача успешно удалена!';
        } else {
            $_SESSION['error'] = 'Ошибка при удалении задачи';
        }

        header('Location: /tasks');
        exit;
    }

    public function updateStatus() {
        $this->checkAuth();

        if (!isset($_GET['id']) || !isset($_GET['status'])) {
            $_SESSION['error'] = 'Неверные параметры';
            header('Location: /tasks');
            exit;
        }

        $taskModel = new Task();
        if ($taskModel->updateStatus($_GET['id'], $_SESSION['user_id'], $_GET['status'])) {
            $_SESSION['success'] = 'Статус задачи обновлен!';
        } else {
            $_SESSION['error'] = 'Ошибка при обновлении статуса';
        }

        header('Location: /tasks');
        exit;
    }

    private function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }
}
?>