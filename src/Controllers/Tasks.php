<?php 

namespace App\Controllers;
use App\Model\DatabaseTable;
use App\Validation\TaskValidation;
use App\Model\TasksTable;


class Tasks {
    public function __construct(private DatabaseTable $databaseTable, private TasksTable $tasksTable) {}

    
    public function list(): array {
        $pageTitle = 'Tasks';
        $tasks = $this->databaseTable->findAll();

        $totalTasks = $this->tasksTable->totalTasks();

        return [
            'pageTitle' => $pageTitle, 
            'template' => 'view_tasks.html.php',
            'variables' => [
                'tasks' => $tasks,
                'totalTasks' => $totalTasks
            ]
        ];
        
    }

    public function setTaskCompletedSubmit() {
        $taskIdRaw = $_POST['id'] ?? null;
        $IsCompletedRaw = $_POST['is_completed'] ?? 0;
        if (!isset($taskIdRaw) || !ctype_digit($taskIdRaw)) {
            http_response_code(400);
            exit('Invalid task id.');
        }

        if ($IsCompletedRaw !== '0' && $IsCompletedRaw !== '1') {
            http_response_code(400);
            exit('Error: task must be checked or unchecked');
        }

        $taskId = (int)$taskIdRaw;
        $isCompleted = (int)$IsCompletedRaw;

        $values = [
            'id' => $taskId,
            'is_completed' => $isCompleted
        ];
        $this->tasksTable->setTaskCompleted($values);
        header('Location: /tasks/list');
    }

    public function insertEditSubmit(): array { 
        $pageTitle = 'Insert task';
        if (isset($_POST['task'])) {
            $validation = new TaskValidation($_POST['task']);
            [$values, $errors] = $validation->processTaskSubmit();
            if($errors) {
                return ['pageTitle' => $pageTitle, 'template' => 'insertEdit.html.php', 'variables' => ['task' => $_POST['task'], 'errors' => $errors]];
            }
            $this->databaseTable->save($values);
            header('Location: /tasks/list');
            exit;

        }
        return ['pageTitle' => $pageTitle, 'template' => 'insertEdit.html.php', 'variables' => ['']];

    }

    public function insertEdit($taskId = null): array {    
        if (isset($taskId)){
            $pageTitle = 'Edit task';
            if($taskId <= 0) {
                $pageTitle = 'Error';
                $errors[] = ['Error: Invalid primary key provided.'];
                return ['pageTitle' => $pageTitle, 'template'=> 'insertEdit.html.php', 'variables' => [ 'errors' => $errors]];

            }
            $task = $this->databaseTable->find($taskId);
            
            return ['pageTitle' => $pageTitle, 'template' => 'insertEdit.html.php', 'variables' => ['task' => $task]];
        }

        return ['pageTitle' => 'Insert Task', 'template' => 'insertEdit.html.php', 'variables' => ['']];
        }
    

    public function deleteSubmit() {
        $taskId = $_POST['id'] ?? null;

        if ($taskId === null || $taskId <= 0) {
                $errors = ['Error: Invalid primary key provided.'];
                return ['errors' => $errors, 'pageTitle' => 'Error'];
        }
        $taskId = (int)$taskId;

        $this->databaseTable->delete($taskId);

        header('Location: /tasks/list');


        
    }

    public function home() {
        $pageTitle = 'Home Page';

        $result = $this->tasksTable->showHighPriorityTasks();

        return ['pageTitle' => $pageTitle, 'template' => 'home.html.php', 'variables' => [
            'tasks' => $result,
        ]];
    }
    
}