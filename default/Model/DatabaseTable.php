<?php 

class DatabaseTable {
    
    public function __construct(private PDO $pdo, private string $table, private string $primaryKey){}


    public function totalTasks() {
        $query = 'SELECT COUNT(*) FROM `' . $this->table . '` ';
        
        $stmt = $this->pdo->query($query);
        
        return $stmt->fetch(PDO::FETCH_NUM);
    }

    public function findAll() {

        $query = 'SELECT * FROM `' . $this->table . '`';

        $stmt  = $this->pdo->prepare($query);

        $stmt->execute();


        return $stmt->fetchAll();

    }

    public function delete(int $taskId) {
        $query = 'DELETE FROM `' . $this->table . '` WHERE `' . $this->primaryKey . '` = :task_id';


        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':task_id', $taskId, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() === 1;

    }
    

    public function showHighPriortyTasks(int $limit = 15) {
        $query = "SELECT `task_title`, `task_description`, `due_at`, `priority`, is_completed FROM  `{$this->table}` 
            WHERE `priority` < 2 
            AND `is_completed` = 0 
            ORDER BY `priority` ASC, `due_at` ASC 
            LIMIT :limit 
        ";

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':limit' , $limit, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
        
    }

    public function setTaskCompleted(array $values) {
        $query = 'UPDATE `' . $this->table . '` SET `is_completed` = :is_completed WHERE `task_id` = :task_id';

        $stmt = $this->pdo->prepare($query);        

        $stmt->execute($values);

    }
    
    //Must finish insert function and then make classes for controllers to make it even dry
    public function insert(array $values) {
        if (!isset($values)) {
            throw new InvalidArgumentException('Error: Empty values provided!');
        }

        $query = 'INSERT INTO `' . $this->table . '` (';

        foreach ($values as $key => $value) {
            $query .= '`' . $key . '`, ';
        }
        
        $query = rtrim($query, ', ');

        $query .= ') VALUES (';
        
        foreach ($values as $key => $value) {
            $query .= ':' . $key . ', ';
        }

        $query = rtrim($query, ', ');

        $query .= ');';

        $stmt = $this->pdo->prepare($query);

        $stmt->execute($values);

    }

    private function update($values) {

        if (!isset($values)) {
            throw new InvalidArgumentException("Error: emprty array was provided.");
        }

        $query = 'UPDATE `' . $this->table . '` SET ';

        foreach($values as $key => $value) {
            $query .= '`' . $key . '` ' . ' = :' . $key . ', ';
        }

        $query = rtrim($query, ', ');

        
        $query .= ' WHERE `' . $this->primaryKey . '` = :primaryKey';

        $values['primaryKey'] = $values['task_id'];

        $stmt = $this->pdo->prepare($query);   


        $stmt->execute($values);
    }

    public function save($record) {
        if (empty($record[$this->primaryKey])){
            unset($record[$this->primaryKey]);
            $this->insert($record);
        } else {
            $this->update($record);

        }

    }

    public function find(int $value) {
        if(empty($value)){
            throw new InvalidArgumentException('Error: Invalid argument provided.');
        }

        $query = 'SELECT * FROM `'  .  $this->table . '` WHERE ' . $this->primaryKey . ' = :value';
        
        $stmt = $this->pdo->prepare($query);

        $values = [':value' => $value];

        $stmt->execute($values);

        return $stmt->fetch();
    }

}