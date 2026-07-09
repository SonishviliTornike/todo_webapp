<?php 

namespace App\Model;
use App\Model\UpdateResult;

class TasksTable {
    public function __construct(private \PDO $pdo,  private string $table) {}

    public function setTaskCompleted(array $values): UpdateResult {
        $query = 'UPDATE `' . $this->table . '` SET `is_completed` = :is_completed WHERE `id` = :id';

        $stmt = $this->pdo->prepare($query);        

        $stmt->execute($values);

        $result = $stmt->rowCount();

        if ($result === 1) {
            return UpdateResult::Changed;
        }
        
        $taskExists = $this->taskExists($values['id']);
        if ($taskExists === true) {
            return UpdateResult::Unchanged;
        }
        return UpdateResult::NotFound;

    }

    private function taskExists(int $id): bool {
        $query = 'SELECT `id` FROM `' . $this->table . '` WHERE `id` = :value';

        $stmt = $this->pdo->prepare($query);
        $values = [
            ':value' => $id
        ];
        $stmt->execute($values);
        if ($stmt->fetchColumn() === false) {
        }
        return true;

    }
    public function showHighPriorityTasks(int $limit = 15): array {
        $query = "SELECT `id`, `task_title`, `task_description`, `due_at`, `priority`, is_completed FROM  `{$this->table}` 
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
    public function totalTasks(): array {
        $query = 'SELECT COUNT(*) FROM `' . $this->table . '` ';
        
        $stmt = $this->pdo->query($query);
        
        return $stmt->fetch(\PDO::FETCH_NUM);
    }

}