<?php 

namespace App\Controllers;
use App\Model\DatabaseTable;
use App\Validation\TaskValidation;
use App\Validation\TaskCompletionValidation;
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

    public function setTaskCompletedSubmit(): void{
        $validation = new TaskCompletionValidation($_POST);
        $state = $validation->validate();
        $values = $validation->getData();


        if ($state === false) {
            $errors = $validation->getErrors();
            http_response_code(400);
            header('Content-type: application/json');
            echo json_encode(['ok' => false, 'Error' => $errors]);
            exit();

        } 

        

        if ($this->tasksTable->setTaskCompleted($values) === true) {
            http_response_code(200);
            header('Content-type: application/json');
            echo json_encode(['ok' => true]);
            exit();
        } 
    }

    public function insertEditSubmit(): array { 
        $pageTitle = 'Insert task';
        if (isset($_POST['task'])) {
            $validation = new TaskValidation($_POST['task']);
            $state  = $validation->validate();
            if($state === false) {
                $errors = $validation->getErrors();
                return ['pageTitle' => $pageTitle, 'template' => 'insertEdit.html.php', 'variables' => ['task' => $_POST['task'], 'errors' => $errors]];
            }

            $values = $validation->getData();

            $this->databaseTable->save($values);
            http_response_code(200);
            header('Location: /tasks/list');
            exit();
        }
        return ['pageTitle' => $pageTitle, 'template' => 'insertEdit.html.php', 'variables' => ['']];

    }

    public function insertEdit($taskId = null): array {    
        if (isset($taskId)){
            $errors = [];
            if($taskId <= 0) {
                $errors['Erorr'][] = 'Error: Invalid primary key provided.';
                return ['pageTitle' => 'Error', 'template'=> 'insertEdit.html.php', 'variables' => [ 'errors' => $errors]];

            }
            $task = $this->databaseTable->find($taskId);
            
            return ['pageTitle' => 'Edit task', 'template' => 'insertEdit.html.php', 'variables' => ['task' => $task]];
        }

        return ['pageTitle' => 'Insert Task', 'template' => 'insertEdit.html.php', 'variables' => ['']];
        }
    

    public function deleteSubmit() {
        $taskId = $_POST['id'] ?? null;

        if ($taskId === null || $taskId <= 0) {
                $errors['Erorr'][] = 'Error: Invalid primary key provided.';
                return ['errors' => $errors, 'template' => 'view_tasks.html.php', 'pageTitle' => 'Error'];
        }
        $taskId = (int)$taskId;

        $this->databaseTable->delete($taskId);
        http_response_code(200);
        header('Location: /tasks/list');
        exit();


        
    }

    public function index() {
        $pageTitle = 'Home Page';

        $result = $this->tasksTable->showHighPriorityTasks();

        return ['pageTitle' => $pageTitle, 'template' => 'index.html.php', 'variables' => [
            'tasks' => $result,
        ]];
    }
 
}