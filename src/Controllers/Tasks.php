<?php
namespace App\Controllers;
use App\Model\DatabaseTable;
use App\Validation\TaskValidation;
use App\Validation\TaskCompletionValidation;
use App\Model\TasksTable;
use App\Model\UpdateResult;
use App\Core\Authentication;

class Tasks {
    public function __construct(private DatabaseTable $databaseTable, private TasksTable $tasksTable, private Authentication $authentication) {}

    
    public function list(): array {
        $pageTitle = 'Tasks';

        $userId = $this->authentication->getUserId();

        $tasks = $this->tasksTable->findAllTasks($userId);

        $totalTasks = $this->tasksTable->totalTasks($userId);

        return [
            'pageTitle' => $pageTitle, 
            'template' => 'view_tasks.html.php',
            'variables' => [
                'tasks' => $tasks,
                'totalTasks' => $totalTasks
            ]
        ];
        
    }

    public function setTaskCompletedSubmit(): never {
        $validation = new TaskCompletionValidation($_POST);
        $state = $validation->validate();
        if ($state === false){ 
            $errors = $validation->getErrors();
            $this->jsonResponse(['ok' => false, 'errors' => $errors], 400);
            
        }

        $userId =  $this->authentication->getUserId();
        $values = $validation->getData();
        
        $result = $this->tasksTable->setTaskCompleted($values, $userId);

        match ($result) {
            UpdateResult::Changed => $this->jsonResponse(['ok' => true], 200),
            UpdateResult::Unchanged => $this->jsonResponse(['ok' => true], 200),
            UpdateResult::NotFound => $this->jsonResponse(['ok' => false], 404)
        };

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
            
            $userId = $this->authentication->getUserId();
            $values = $validation->getData();
            $values['user_id'] = $userId;
            if (!isset($values['id'])) {
                $this->databaseTable->save($values);
                http_response_code(200);
                header('Location: /tasks/list');
                exit();
            } else {
                $result = $this->tasksTable->updateTask($values);
                if ($result === UpdateResult::NotFound) {
                    http_response_code(404);
                    return ['pageTitle' => 'Not found', 'template' => 'insertEdit.html.php', 'variables' => ['task' => $_POST['task'], 'errors' => ['taskError' => ['Task not found']]]];
                }

                header('Location: /tasks/list');
                exit();
            }
                

        }
        return ['pageTitle' => $pageTitle, 'template' => 'insertEdit.html.php', 'variables' => ['']];

    }

    public function taskForm($taskId = null): array {    
        if (isset($taskId)){
            $errors = [];
            
            if($taskId <= 0) {
                $errors['Erorr'][] = 'Error: Invalid primary key provided.';
                return ['pageTitle' => 'Error', 'template'=> 'insertEdit.html.php', 'variables' => ['errors' => $errors]];

            }
            $userId = $this->authentication->getUserId();

            $task = $this->tasksTable->findTask($taskId, $userId);
            if ($task === false) {
                $tasks = $this->tasksTable->findAllTasks($userId);
                $totalTasks = $this->tasksTable->totalTasks($userId);
                http_response_code(404);
                return ['pageTitle' => 'Not found', 'template' => 'view_tasks.html.php', 'variables' => ['tasks' => $tasks, 'totalTasks'=> $totalTasks, 'errors' => ['taskError' => ['Task not found']]]];
                } 
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
        $userId = $this->authentication->getUserId();

        $this->tasksTable->deleteTask($taskId, $userId);
        http_response_code(200);
        header('Location: /tasks/list');
        exit();


        
    }

    public function index() {
        if ($this->authentication->isLoggedIn()) {
            $userId = $this->authentication->getUserId();
            $result = $this->tasksTable->showHighPriorityTasks($userId);
            return ['pageTitle' => 'Home page', 'template' => 'index.html.php', 'variables' => [
                'tasks' => $result,
            ]];
        }
           return ['pageTitle' => 'Welcome', 'template' => 'index.html.php', 'variables' => ['']];

    }

    private function jsonResponse(array $payload, int $responseCode): never {
            http_response_code($responseCode);
            header('Content-type: application/json');
            echo json_encode($payload);
            exit();
    }

}

