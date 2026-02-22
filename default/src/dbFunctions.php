<?php 

function processDate($values) {
    foreach ($values as $key => $value) {
        if($value instanceof DateTime){
            $values[$key] = $value->format('Y-m-d');
        }
    }
    return $values;
}

function totalTasks($pdo){
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM `tasks`');

    $stmt->execute();
    
    $row = $stmt->fetch(PDO::FETCH_NUM);

    return $row[0];
}

function allTasks($pdo) {
    $sql = 'SELECT `task_id`, `task_title`, `task_description`,
    `due_at`, `priority`, `is_completed` FROM
        `todo_webapp`.`tasks`';

    $stmt = $pdo->prepare($sql);

    $stmt->execute();

    $tasks = [];
    while ($row = $stmt->fetch()) {
        $tasks[] = [
            'task_id' => $row['task_id'],
            'task_title' => $row['task_title'],
            'task_description' => $row['task_description'],
            'due_at' => $row['due_at'],
            'priority' => $row['priority'],
            'is_completed' => $row['is_completed']
        
        ];
    }

    return $tasks;
}


function delete($pdo,  $table, $field, $taskId){
    $query = 'DELETE FROM `' . $table . '` WHERE `' . $field . '` = :value';

    $stmt = $pdo->prepare($query);

    $values = [
        ':value' => $taskId
    ];

    $stmt->execute($values);
}

function get($pdo, $taskId) {
    $sql = 'SELECT `task_id`, `task_title`, `task_description`, `due_at` FROM `todo_webapp`.`tasks` WHERE task_id = :task_id';
    $stmt = $pdo-> prepare($sql);
    $values = [
        ':task_id' => $taskId
    ];
    $stmt->execute($values);

    return $stmt->fetch();
}


function updateTask($pdo, $taskId, $taskTitle, $taskDescription, $dueAt) {
    $sql = 'UPDATE `todo_webapp`.`tasks` SET
    `task_title` = :task_title,
    `task_description` = :task_description,
    `due_at` = :due_at
    WHERE `task_id` = :task_id';

    $stmt = $pdo->prepare($sql);

    $values = [
        ':task_title' => $taskTitle,
        ':task_description' => $taskDescription,
        ':due_at' => $dueAt,
        ':task_id' => $taskId
    ];

    $stmt->execute($values);

}

function setTaskCompleted(PDO $pdo, int $taskId, int $isCompleted){
    $query = "UPDATE `tasks` SET `is_completed` = :is_completed WHERE `task_id` = :task_id";

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

function insert(PDO $pdo, string $table, array $values){
    if (empty($values)) {
        throw new InvalidArgumentException('Error: Empty values provided,');
    } 

    $allowed = [
        'tasks' => [ 'task_title', 'task_description', 'priority', 'due_at'],
    ];

    if(!array_key_exists($table, $allowed)){
        throw new InvalidArgumentException('Error: Invalid table name');
    }

    $query = 'INSERT INTO `' . $table . '` (';

    $fields = array_keys($values);
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