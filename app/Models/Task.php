<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Task {
    private $db;
    private $table = 'tasks';

    public $id;
    public $user_id;
    public $title;
    public $description;
    public $comment;
    public $status;
    public $priority;
    public $due_date;
    public $created_at;
    public $updated_at;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllByUser($user_id) {
        $query = "SELECT * FROM {$this->table} WHERE user_id = :user_id 
                 ORDER BY 
                     CASE priority 
                         WHEN 'high' THEN 1 
                         WHEN 'medium' THEN 2 
                         WHEN 'low' THEN 3 
                     END,
                     due_date ASC, created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ?: [];
    }

    public function getActiveTasks($user_id, $sort_by = 'priority', $sort_order = 'ASC') {
        $allowed_sorts = ['created_at', 'due_date', 'title', 'priority'];
        $sort_by = in_array($sort_by, $allowed_sorts) ? $sort_by : 'priority';
        $sort_order = $sort_order === 'DESC' ? 'DESC' : 'ASC';

        if ($sort_by === 'priority') {
            $query = "SELECT * FROM {$this->table} 
                     WHERE user_id = :user_id AND status = 'pending'
                     ORDER BY 
                         CASE priority 
                             WHEN 'high' THEN 1 
                             WHEN 'medium' THEN 2 
                             WHEN 'low' THEN 3 
                         END {$sort_order},
                         due_date ASC";
        } else {
            $query = "SELECT * FROM {$this->table} 
                     WHERE user_id = :user_id AND status = 'pending'
                     ORDER BY {$sort_by} {$sort_order}";
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCompletedTasks($user_id, $sort_by = 'created_at', $sort_order = 'DESC') {
        $allowed_sorts = ['created_at', 'updated_at', 'due_date', 'title', 'priority'];
        $sort_by = in_array($sort_by, $allowed_sorts) ? $sort_by : 'created_at';
        $sort_order = $sort_order === 'ASC' ? 'ASC' : 'DESC';

        $query = "SELECT * FROM {$this->table} 
                 WHERE user_id = :user_id AND status = 'completed'
                 ORDER BY {$sort_by} {$sort_order}";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO {$this->table} (user_id, title, description, comment, priority, due_date) 
                 VALUES (:user_id, :title, :description, :comment, :priority, :due_date)";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':comment', $this->comment);
        $stmt->bindParam(':priority', $this->priority);
        $stmt->bindParam(':due_date', $this->due_date);

        return $stmt->execute();
    }

    public function getById($id, $user_id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id AND user_id = :user_id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Отладочная информация
        if (!$result) {
            error_log("Задача не найдена: ID=$id, UserID=$user_id");
        } else {
            error_log("Задача найдена: ID=" . $result['id'] . ", Title=" . $result['title']);
        }
        
        return $result;
    }

    public function update() {
        // Получаем данные из POST
        $id = $_POST['id'] ?? null;
        $user_id = $_SESSION['user_id'] ?? null;
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $comment = $_POST['comment'] ?? '';
        $status = $_POST['status'] ?? 'pending';
        $priority = $_POST['priority'] ?? 'medium';
        $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

        if (!$id || !$user_id) {
            return false;
        }

        $query = "UPDATE {$this->table} SET 
                title = :title, 
                description = :description, 
                comment = :comment,
                status = :status, 
                priority = :priority, 
                due_date = :due_date,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = :id AND user_id = :user_id";
        
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':comment', $comment);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':priority', $priority);
        $stmt->bindParam(':due_date', $due_date);

        return $stmt->execute();
    }

    public function delete($id, $user_id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $user_id);

        return $stmt->execute();
    }

    public function updateStatus($id, $user_id, $status) {
        $query = "UPDATE {$this->table} SET status = :status WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $user_id);

        return $stmt->execute();
    }

    public function getStats($user_id) {
        $tasks = $this->getAllByUser($user_id);
        
        $stats = [
            'total' => count($tasks),
            'completed' => 0,
            'pending' => 0,
            'high_priority' => 0
        ];
        
        foreach ($tasks as $task) {
            if ($task['status'] == 'completed') {
                $stats['completed']++;
            } else {
                $stats['pending']++;
            }
            
            if ($task['priority'] == 'high') {
                $stats['high_priority']++;
            }
        }
        
        return $stats;
    }
}
?>