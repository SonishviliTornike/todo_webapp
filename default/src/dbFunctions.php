<?php 
function totalTasks(PDO $pdo, string $table){

    $allowed = ['tasks'];
    if(!in_array($table, $allowed, true)) {
        throw new InvalidArgumentException('Invalid table name');
    }
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM `' . $table. '`');

    $stmt->execute();
    
    $row = $stmt->fetch(PDO::FETCH_NUM);

    return $row[0];
}

function findAll(PDO $pdo, string $table) {
    $query = 'SELECT * FROM `' . $table . '`'; 

    $allowed = [
        'tasks'
    ];
    if(!in_array($table, $allowed, true)) {
        throw new InvalidArgumentException('Invalid table name');
    }

    $stmt = $pdo->prepare($query);

    $stmt->execute();

    return $stmt->fetchAll();
}


function delete(PDO $pdo,  string $table, string $field, int $value) {
    $query = 'DELETE FROM `' . $table . '` WHERE `' . $field . '` = :value';
    
    $allowed = [
        'tasks' => ['task_id']
        ];

    if(!array_key_exists($table, $allowed)){
        throw new InvalidArgumentException('Invalid table name provided');
    }
    
    if (!in_array($field, $allowed[$table], true)){
        throw new InvalidArgumentException('Invalid field name provided.');
    }

    $stmt = $pdo->prepare($query);

    $values = [
        'value' => $value
    ];
    $stmt->execute($values);
    return  $stmt->rowCount() === 1;
}

function find(PDO $pdo, string $table , string $field, int $value) {
    $query = 'SELECT * FROM `' . $table . '` WHERE `' . $field . '` = :value';

    $allowed = [
        'tasks' => ['task_id']
    ];

    if (!array_key_exists($table, $allowed)) {
        throw new InvalidArgumentException('Invalid table name provided');
    }
    
    if (!in_array($field, $allowed[$table], true)) {
        throw new InvalidArgumentException('Invalid field name provided.');
    }

    $stmt = $pdo-> prepare($query);

    $values = [
        ':value' => $value
    ];
    $stmt->execute($values);

    return $stmt->fetch();
}



function setTaskCompleted(PDO $pdo, int $taskId, int $isCompleted){
    $query = "UPDATE `tasks` SET `is_completed` = :is_completed WHERE `task_id` = :task_id";

    $allowed = [
        'tasks' => ['is_completed']

    ];

    if (!array_key_exists('tasks', $allowed)){
        throw new InvalidArgumentException('Invalid table name provided.');
    }

    if (!in_array('is_completed', $allowed['tasks'], true)) {
        throw new InvalidArgumentException('Invalid field name provided.');
    }
    
    $stmt = $pdo->prepare($query);
    
    $values = [
        ':is_completed' => $isCompleted,
        ':task_id' => $taskId
        ];
        
        $stmt->execute($values);
        }
        
        function showHighPriorityTasks($pdo){
            $query = "SELECT `task_id`, `task_title`, `task_description`, `due_at`, `priority`, `is_completed` FROM `tasks` 
    WHERE `priority` < 2";

$stmt = $pdo->query($query);

return $stmt->fetchAll();
}

function insert(PDO $pdo, string $table, array $fields, array $values){
    if (empty($values)) {
        throw new InvalidArgumentException('Error: Empty values provided,');
    } 
    
    unset($values['task_id']);
    $allowed = [
        'tasks' => [ 'task_title', 'task_description', 'priority', 'due_at'],
        ];
    
        if(!array_key_exists($table, $allowed)){
            throw new InvalidArgumentException('Error: Invalid table name');
        }
            
    $query = 'INSERT INTO `' . $table . '` (';

    foreach ($fields as $field){
        if(!in_array($field, $allowed[$table], true)){
            throw new InvalidArgumentException('Error: Invalid table fields.');
        }
        $query .= '`' . $field . '`, ';
        
    }
            
            
    $query = rtrim($query, ', ');
    
    $query .= ') VALUES (';
    
    foreach ($fields as $field) {
        $query .= ':' . $field . ', ';
    }
        
    $query = rtrim($query, ', ');
    $query .= ');';

    $stmt = $pdo->prepare($query);
    $stmt->execute($values);
}
                            
function update(PDO $pdo, string $table, array $fields,  string $primaryKey, array $values) {
    if (empty($values)) {
        throw new InvalidArgumentException('Error: empty values provided');
    }

    $allowed = [
        'tasks' => ['task_title', 'task_description', 'due_at', 'priority']
    ];

    if (!array_key_exists($table, $allowed)) {
        throw new InvalidArgumentException('Error: Invalid database table');
    }

    $query = 'UPDATE `' . $table . '` SET ';

    foreach ($fields as $field) {
        if(!in_array($field, $allowed[$table])) {
            throw new InvalidArgumentException('Error: Invalid fields.');
        }

        $query .= ' `' . $field . '` = :' . $field . ', ';
    }

    $query = rtrim($query, ', ');
    $query .= ' WHERE `' . $primaryKey . '` = :primaryKey';


    $stmt = $pdo->prepare($query);
    
    $values['primaryKey'] = $values[$primaryKey];
    
    unset($values[$primaryKey]);

    
    $stmt->execute($values);

}

function save(PDO $pdo, string $table, string $primaryKey, array $fields, array $record ) {
    if (!empty($record[$primaryKey])){
        update($pdo, $table, $fields, $primaryKey, $record);
        return ;
    }
    insert($pdo, $table, $fields, $record);

}
