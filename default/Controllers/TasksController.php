<?php 
require_once __DIR__ . '/../src/validation/tasks.php';

class TasksController {
    public function __construct(private DatabaseTable $tasksTable) {}


    public function list() {
        $page_title = 'Tasks';
        $tasks = $this->tasksTable->findAll();

        $totalTasks = $this->tasksTable->totalTasks();

        return [
            'page_title' => $page_title, 
            'template' => 'view_tasks.html.php',
            'variables' => [
                'tasks' => $tasks,
                'totalTasks' => $totalTasks
            ]
        ];
        
    }
    //ამოსაღები და ცალკე გასატანი იქნება 
    public function setTaskCompleted() {
        $taskIdRaw = $_POST['task_id'] ?? null;
        $isCompletedRaw = $_POST['is_completed'] ?? 0;

        if (!ctype_digit((string)$taskIdRaw) || !isset($taskIdRaw)) {
            http_response_code(400);
            exit('Error: invalid task');
        }

        if ($isCompletedRaw !== '0' && $isCompletedRaw !== '1') {
            http_response_code(400);
            exit('Error: task must be checked or unchecked');
        }

        $taskId = (int)$taskIdRaw;
        $isCompleted = (int)$isCompletedRaw;

        $values = [
            'task_id' => $taskId,
            'is_completed' => $isCompleted
        ];

        $this->tasksTable->setTaskCompleted($values);
        header('Location: /index.php?action=list');
    }

    public function insertEdit() { 
        $page_title = 'Insert task';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['task'])) {
                [$values, $errors] = taskValidation($_POST['task']);
                $this->tasksTable->save($values);
                if($errors) {
                    return ['page_title' => $page_title, 'variables' => ['errors' => $errors]];
                }
                header('Location: /index.php?action=list');
                exit;

            } else {
                $taskId = $_POST['task_id'] ?? '0';
                if(isset($taskId)){
                    $page_title = 'Edit task';
                    if ($taskId < '0') {
                        $page_title = 'Error';
                        $errors[] = ['Erorr: Invalid primary key provided.'];
                        return ['page_title' => $page_title, 'variables' => ['errors' => $errors]];
                    }
                    $taskId = (int)$taskId;
                    $old_task = $this->tasksTable->find($taskId);
                    
                    return ['page_title' => $page_title, 'template' => 'insertEdit.html.php', 'variables' => [
                        'old_task' => $old_task
                        ]
                    ];
    
                }

            }
                
        } else {
        return ['page_title' => $page_title, 'template' => 'insertEdit.html.php', 'variables' => ['']];
    }
}

    public function delete() {
        if (isset($_POST['task_id'])) {
            $taskId = $_POST['task_id'] ?? 0;
            if ($taskId >! 0 ) {
                $errors = ['Error Invalid primary key provided.'];
                $page_title = 'Error';
                return ['errors' => $errors, 'page_title' => $page_title];
                }
            $taskId = (int)$taskId;

            $this->tasksTable->delete($taskId);

            return $this->list();


            
        }
    }

    public function home() {
        $page_title = 'Home Page';

        $welcome = 'Welcome';
        $tasks = [];
        $result = $this->tasksTable->showHighPriortyTasks();
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