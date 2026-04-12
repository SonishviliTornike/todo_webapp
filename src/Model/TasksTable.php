<?php 

namespace App\Model;
use App\Core\DatabaseConnection;

class TasksTable {
    public function __construct(private \PDO $pdo,  private string $table) {}

    public function setTaskCompleted(array $values) {
        $query = 'UPDATE `' . $this->table . '` SET `is_completed` = :is_completed WHERE `id` = :id';

        $stmt = $this->pdo->prepare($query);        

        $stmt->execute($values);

    }

    
    public function showHighPriorityTasks(int $limit = 15) {
        $query = "SELECT `task_title`, `task_description`, `due_at`, `priority`, is_completed FROM  `{$this->table}` 
            WHERE `priority` < 2 
            AND `is_completed` = 0 
            ORDER BY `priority` ASC, `due_at` ASC 
            LIMIT :limit 
        ";

        $stmt = $this->pdo->prepare($query);  

        $stmt->bindValue(':limit' , $limit, \PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    
    }

}