<?php 

namespace App\Model;

class DatabaseTable {
    
    public function __construct(private \PDO $pdo, private string $table, private string $primaryKey, private ?array $allowedColumnNames){
        $this->allowedColumnNames[] = $this->primaryKey;
    }

    public function findAll(): array {
        $query = 'SELECT * FROM `' . $this->table . '`';

        $stmt  = $this->pdo->prepare($query);

        $stmt->execute();

        return $stmt->fetchAll();

    }

    public function delete(int $id): bool {
        $query = 'DELETE FROM `' . $this->table . '` WHERE `' . $this->primaryKey . '` = :id';


        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() === 1;
    }
    

   
    public function insert(array $values): void {
        if (empty($values)) {
            throw new \InvalidArgumentException('Error: Empty values provided!');
        }
        
        $this->assertColumnsAllowed(array_keys($values));

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

    private function update(array $values): void {

        if (empty($values)) {
            throw new \InvalidArgumentException("Error: empty array was provided.");
        }

        $this->assertColumnsAllowed(array_keys($values));

        $query = 'UPDATE `' . $this->table . '` SET ';

        foreach($values as $key => $value) {
            $query .= '`' . $key . '` ' . ' = :' . $key . ', ';
        }

        $query = rtrim($query, ', ');

        
        $query .= ' WHERE `' . $this->primaryKey . '` = :primaryKey';
        
        $values['primaryKey'] = $values[$this->primaryKey];
        


        $stmt = $this->pdo->prepare($query);   


        $stmt->execute($values);
    }

    public function save(array $record): void {
        if (empty($record[$this->primaryKey])) {
            unset($record[$this->primaryKey]);
            $this->insert($record);
        } else {
            $this->update($record);
        }

    }

    public function find($value, $columnName = null): array | false {
        if(empty($value)){
            throw new \InvalidArgumentException('Invalid arguments: Invalid values provided.');
        }
        if (!isset($columnName)){
            $columnName = $this->primaryKey;
        }

        $this->assertColumnsAllowed([$columnName]);


        $query = 'SELECT * FROM `'  .  $this->table . '` WHERE `' . $columnName . '` = :value';
        
        $stmt = $this->pdo->prepare($query);

        $values = [':value' => $value];

        $stmt->execute($values);

        return $stmt->fetch();
    }


    private function assertColumnsAllowed(array $columnNames): void {
        foreach ($columnNames as $columnName) {
            if (!in_array($columnName, $this->allowedColumnNames, true)) {
                throw new \InvalidArgumentException('Invalid argument: Invalid column name provided: ' . $columnName);
            }
        }
    }
}