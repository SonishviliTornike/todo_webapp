<?php 

namespace App\Model;

class DatabaseTable {
    
    public function __construct(private \PDO $pdo, private string $table, private string $primaryKey){}

    public function findAll() {

        $query = 'SELECT * FROM `' . $this->table . '`';

        $stmt  = $this->pdo->prepare($query);

        $stmt->execute();


        return $stmt->fetchAll();

    }

    public function delete(int $taskId) {
        $query = 'DELETE FROM `' . $this->table . '` WHERE `' . $this->primaryKey . '` = :id';


        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':id', $taskId, \PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() === 1;

    }
    

   
    public function insert(array $values) {
        if (!isset($values)) {
            throw new \InvalidArgumentException('Error: Empty values provided!');
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
            throw new \InvalidArgumentException("Error: emprty array was provided.");
        }

        $query = 'UPDATE `' . $this->table . '` SET ';

        foreach($values as $key => $value) {
            $query .= '`' . $key . '` ' . ' = :' . $key . ', ';
        }

        $query = rtrim($query, ', ');

        
        $query .= ' WHERE `' . $this->primaryKey . '` = :primaryKey';

        $values['primaryKey'] = $values['id'];

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
            throw new \InvalidArgumentException('Error: Invalid argument provided.');
        }

        $query = 'SELECT * FROM `'  .  $this->table . '` WHERE ' . $this->primaryKey . ' = :value';
        
        $stmt = $this->pdo->prepare($query);

        $values = [':value' => $value];

        $stmt->execute($values);

        return $stmt->fetch();
    }

}