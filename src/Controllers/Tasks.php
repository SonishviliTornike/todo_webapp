<?php 

namespace App\Controllers;
use App\Model\DatabaseTable;
use App\Validation\TaskValidation;
use App\Model\TasksTable;


class Tasks {
    public function __construct(private DatabaseTable $databaseTable, private TasksTable $tasksTable) {}

    
    public function list() {
        $page_title = 'Tasks';
        $tasks = $this->databaseTable->findAll();

        $totalTasks = $this->databaseTable->totalTasks();

        return [
            'page_title' => $page_title, 
            'template' => 'view_tasks.html.php',
            'variables' => [
                'tasks' => $tasks,
                'totalTasks' => $totalTasks
            ]
        ];
        
    }

    public function setTaskCompletedSubmit() {
        $taskIdRaw = $_POST['id'] ?? null;
        $isCompletedRaw = $_POST['is_completed'] ?? 0;
        if (!isset($taskIdRaw) || !ctype_digit($taskIdRaw)) {
            http_response_code(400);
            exit('Invalid task id.');
        }

        if ($isCompletedRaw !== '0' && $isCompletedRaw !== '1') {
            http_response_code(400);
            exit('Error: task must be checked or unchecked');
        }

        $taskId = (int)$taskIdRaw;
        $isCompleted = (int)$isCompletedRaw;

        $values = [
            'id' => $taskId,
            'is_completed' => $isCompleted
        ];
        $this->tasksTable->setTaskCompleted($values);
        header('Location: /tasks/list');
    }

    public function insertEditSubmit() { 
        $page_title = 'Insert task';
        if (isset($_POST['task'])) {
            $validation = new TaskValidation($_POST['task']);
            [$values, $errors] = $validation->processPostRequest();
            if($errors) {
                return ['page_title' => $page_title, 'template' => 'insertEdit.html.php', 'variables' => ['task' => $_POST['task'], 'errors' => $errors]];
            }
            $this->databaseTable->save($values);
            header('Location: /tasks/list');
            exit;

        }
        return ['page_title' => $page_title, 'template' => 'insertEdit.html.php', 'variables' => ['']];

    }

    public function insertEdit($taskId = null) {    
        if (isset($taskId)){
            $page_title = 'Edit task';
            if($taskId <= 0) {
                $page_title = 'Error';
                $errors[] = ['Error: Invalid primary key provided.'];
                return ['page_title' => $page_title, 'template'=> 'insertEdit.html.php', 'variables' => [ 'errors' => $errors]];

            }
            $task = $this->databaseTable->find($taskId) ?? null;

            return ['page_title' => $page_title, 'template' => 'insertEdit.html.php', 'variables' => ['task' => $task ?? null]];
        }
        $page_title = 'Insert task';
        return ['page_title' => $page_title, 'template' => 'insertEdit.html.php', 'variables' => ['']];
        }
    

    public function deleteSubmit() {
        if (isset($_POST['id'])) {
            $taskId = $_POST['id'] ?? 0;
            if ($taskId <= 0 ) {
                $errors = ['Error: Invalid primary key provided.'];
                $page_title = 'Error';
                return ['errors' => $errors, 'page_title' => $page_title];
                }
            $taskId = (int)$taskId;

            $this->databaseTable->delete($taskId);

            header('Location: /tasks/list');


            
        }
    }

    public function home() {
        $page_title = 'Home Page';

        $welcome = 'Welcome';
        $tasks = [];
        $result = $this->tasksTable->showHighPriorityTasks();
        foreach($result as $row) {
            $tasks[] = array(
                'task_title' => $row['task_title'],
                'task_description' => $row['task_description'],
                'due_at' => $row['due_at'],
                'priority' => $row['priority'],  
                );
        }

        return ['page_title' => $page_title, 'template' => 'home.html.php', 'variables' => [
            'tasks' => $tasks,
        ]];
    }
    
}