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
        $userId = $this->authentication->getUserId();

        $tasks = $this->tasksTable->findAllTasks($userId);

        $totalTasks = $this->tasksTable->totalTasks($userId);

        return [
            'pageTitle' => 'Tasks', 
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
        if (isset($_POST['task'])) {
            
            $validation = new TaskValidation($_POST['task']);

            $state  = $validation->validate();
            if($state === false) {
                $errors = $validation->getErrors();
                return ['pageTitle' => 'Add task', 'template' => 'insertEdit.html.php', 'variables' => ['task' => $_POST['task'], 'errors' => $errors]];
            }
            
            $userId = $this->authentication->getUserId();
            $values = $validation->getData();
            $values['user_id'] = $userId;
            if (!isset($values['id'])) {
                $this->databaseTable->save($values);
                header('Location: /tasks/list');
                exit();
            } else {
                $result = $this->tasksTable->updateTask($values);
                if ($result === UpdateResult::NotFound) {
                    http_response_code(404);
                    return ['pageTitle' => 'Not found', 'template' => 'insertEdit.html.php', 'variables' => ['task' => $_POST['task'], 'errors' => ['_form' => ['Task not found']]]];
                }

                header('Location: /tasks/list');
                exit();
            }
                

        }
        return ['pageTitle' => 'Add task', 'template' => 'insertEdit.html.php', 'variables' => ['']];

    }

    public function taskForm($taskId = null): array {    
        if (isset($taskId)){
            $errors = [];
            
            if($taskId <= 0) {
                $errors['_form'][] = 'Invalid primary key provided.';
                return ['pageTitle' => 'Add task', 'template'=> 'insertEdit.html.php', 'variables' => ['errors' => $errors]];

            }
            $userId = $this->authentication->getUserId();

            $task = $this->tasksTable->findTask($taskId, $userId);
            if ($task === false) {
                $tasks = $this->tasksTable->findAllTasks($userId);
                $totalTasks = $this->tasksTable->totalTasks($userId);
                http_response_code(404);
                return ['pageTitle' => 'Not found', 'template' => 'view_tasks.html.php', 'variables' => ['tasks' => $tasks, 'totalTasks'=> $totalTasks, 'errors' => ['_form' => ['Task not found']]]];
                } 
            return ['pageTitle' => 'Edit task', 'template' => 'insertEdit.html.php', 'variables' => ['task' => $task]];            
        }  
        return ['pageTitle' => 'Add task', 'template' => 'insertEdit.html.php', 'variables' => ['']];
    }
    

    public function deleteSubmit(): array {
        $taskId = $_POST['id'] ?? null;
        $userId = $this->authentication->getUserId();
        if ($taskId === null || $taskId <= 0) {
            $errors['_form'][] = 'Invalid task';
            
            $page = $this->list();
            
            $page['variables']['errors'] = $errors;

            http_response_code(400);

            return $page;
        }
        $taskId = (int)$taskId;

        $this->tasksTable->deleteTask($taskId, $userId);
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

