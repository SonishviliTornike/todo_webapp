<?php

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/dbFunctions.php';



$table = 'tasks';

$tasks = findAll($pdo, $table);

$totalTasks = totalTasks($pdo, $table);

ob_start();
         
include __DIR__. '/../templates/view_tasks.html.php';

$output = ob_get_clean();

$page_title = 'Tasks';


include __DIR__ . '/../templates/layout.html.php';