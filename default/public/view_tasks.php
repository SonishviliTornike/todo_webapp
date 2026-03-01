<?php

require_once __DIR__ . '/../src/Core/db.php';
require_once __DIR__ . '/../src/dbFunctions.php';




$tasks = findAll($pdo, 'tasks');

$totalTasks = totalTasks($pdo, 'tasks');

ob_start();
         
include __DIR__. '/../templates/view_tasks.html.php';

$output = ob_get_clean();

$page_title = 'Tasks';


include __DIR__ . '/../templates/layout.html.php';