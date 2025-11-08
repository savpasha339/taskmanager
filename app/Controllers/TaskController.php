<?php
namespace App\Controllers;

use App\Models\Task;

session_start();

class TaskController {
    
    public function index() {
        $this->checkAuth();

        $taskModel = new Task();
        
        $sort_by = $_GET['sort'] ?? 'priority';
        $sort_order = $_GET['order'] ?? 'ASC';
        
        $tasks = $taskModel->getActiveTasks($_SESSION['user_id'], $sort_by, $sort_order);
        $stats = $taskModel->getStats($_SESSION['user_id']);

        include __DIR__ . '/../../resources/views/tasks/index.php';
    }

    public function history() {
        $this->checkAuth();

        $taskModel = new Task();
        
        $sort_by = $_GET['sort'] ?? 'created_at';
        $sort_order = $_GET['order'] ?? 'DESC';
        
        $tasks = $taskModel->getCompletedTasks($_SESSION['user_id'], $sort_by, $sort_order);
        $stats = $taskModel->getStats($_SESSION['user_id']);

        include __DIR__ . '/../../resources/views/tasks/history.php';
    }

    public function create() {
        $this->checkAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $task = new Task();
            $task->user_id = $_SESSION['user_id'];
            $task->title = $_POST['title'] ?? '';
            $task->description = $_POST['description'] ?? '';
            $task->comment = $_POST['comment'] ?? '';
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

    // Детальная отладка
    error_log("=== EDIT METHOD STARTED ===");
    error_log("GET parameters: " . print_r($_GET, true));
    error_log("POST parameters: " . print_r($_POST, true));
    error_log("SESSION user_id: " . ($_SESSION['user_id'] ?? 'not set'));

    // Проверяем ID разными способами
    $task_id = null;
    
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $task_id = (int)$_GET['id'];
        error_log("ID from GET: " . $task_id);
    } elseif (isset($_POST['id']) && !empty($_POST['id'])) {
        $task_id = (int)$_POST['id'];
        error_log("ID from POST: " . $task_id);
    } else {
        error_log("❌ ID not found in GET or POST");
        $_SESSION['error'] = 'ID задачи не указан';
        header('Location: /tasks');
        exit;
    }

    $user_id = $_SESSION['user_id'];
    error_log("User ID: " . $user_id);

    $taskModel = new Task();
    $task = $taskModel->getById($task_id, $user_id);

    if (!$task) {
        error_log("❌ Task not found: ID=$task_id, UserID=$user_id");
        $_SESSION['error'] = "Задача #$task_id не найдена";
        header('Location: /tasks');
        exit;
    }

    error_log("✅ Task found: " . $task['title']);

    // Обработка POST запроса
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        error_log("Processing POST request");
        
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $comment = trim($_POST['comment'] ?? '');
        $status = $_POST['status'] ?? 'pending';
        $priority = $_POST['priority'] ?? 'medium';
        $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

        if (empty($title)) {
            $_SESSION['error'] = 'Название задачи обязательно';
            $task = array_merge($task, [
                'title' => $title,
                'description' => $description,
                'comment' => $comment,
                'status' => $status,
                'priority' => $priority,
                'due_date' => $due_date
            ]);
        } else {
            // Обновляем задачу
            $success = $taskModel->update();
            
            if ($success) {
                $_SESSION['success'] = 'Задача успешно обновлена!';
                header('Location: /tasks');
                exit;
            } else {
                $_SESSION['error'] = 'Ошибка при обновлении задачи';
            }
        }
    }

    include __DIR__ . '/../../resources/views/tasks/edit.php';
    error_log("=== EDIT METHOD FINISHED ===");
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