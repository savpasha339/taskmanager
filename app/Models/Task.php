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
        return $result ?: []; // Всегда возвращаем массив
    }

    public function create() {
        $query = "INSERT INTO {$this->table} (user_id, title, description, priority, due_date) 
                 VALUES (:user_id, :title, :description, :priority, :due_date)";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
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

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update() {
        $query = "UPDATE {$this->table} SET 
                 title = :title, 
                 description = :description, 
                 status = :status, 
                 priority = :priority, 
                 due_date = :due_date 
                 WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':priority', $this->priority);
        $stmt->bindParam(':due_date', $this->due_date);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);

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