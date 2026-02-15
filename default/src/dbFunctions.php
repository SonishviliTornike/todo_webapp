<?php 

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


function deleteTask($pdo, $taskId){
    $sql = 'DELETE FROM `todo_webapp`.`tasks` WHERE task_id = :task_id';
    $stmt = $pdo->prepare($sql);

    $values = [
        ':task_id' => $taskId
    ];

    $stmt->execute($values);
}

function getTask($pdo, $taskId) {
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

function toggleTask($pdo, $taskId, $isCompleted){
    $sql = 'UPDATE `todo_webapp`.`tasks` SET
    is_completed = :is_completed
    WHERE task_id = :task_id';

    $stmt = $pdo->prepare($sql);

    $values = [
        ':is_completed' => $isCompleted,
        ':task_id' => $taskId
    ];

    $stmt->execute($values);
}

function getByPriority($pdo){
    $sql = 'SELECT `task_id`, `task_title`, `task_description`, `due_at`, `priority` FROM `tasks`
    WHERE `priority` < 3';

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $result = $stmt->fetchAll();

    return $result;
}

function insertTask($pdo, $taskTitle, $taskDescription, $dueAt, $priority){
    
    $sql = 'INSERT INTO `todo_webapp`.`tasks` (
        `task_title`, `task_description`, `created_at`, due_at, priority) VALUES (
        :task_title, :task_description, NOW(), :due_at, :priority)';
    $stmt = $pdo->prepare($sql);
    
    $values = [
        ':task_title' => $taskTitle,
        ':task_description' => $taskDescription,
        ':due_at' => $dueAt,
        ':priority' => $priority
    ];

    $stmt->execute($values);
}