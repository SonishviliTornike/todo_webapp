<?php

require __DIR__ . '/../src/db.php';

$sql_query = 'SELECT `task_id`, `task_title`, `task_description`,
    `due_at`, `priority`, `is_completed` FROM
        `todo_webapp`.`tasks`';

$tasks = $pdo->query($sql_query);

ob_start();
         
include __DIR__. '/../templates/view_tasks.html.php';

$output = ob_get_clean();

$page_title = 'Tasks';


include __DIR__ . '/../templates/layout.html.php';