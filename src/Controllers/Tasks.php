<?php 

namespace App\Controllers;
use App\Model\DatabaseTable;
use App\Validation\TaskValidation;
use App\Model\TasksTable;


class Tasks {
    public function __construct(private DatabaseTable $database_table, private TasksTable $tasks_table) {}

    
    public function list(): array {
        $page_title = 'Tasks';
        $tasks = $this->database_table->findAll();

        $totalTasks = $this->tasks_table->totalTasks();

        return [
            'page_title' => $page_title, 
            'template' => 'view_tasks.html.php',
            'variables' => [
                'tasks' => $tasks,
                'totalTasks' => $totalTasks
            ]
        ];
        
    }

    public function set_task_completed_submit() {
        $task_id_raw = $_POST['id'] ?? null;
        $is_completed_raw = $_POST['is_completed'] ?? 0;
        if (!isset($task_id_raw) || !ctype_digit($task_id_raw)) {
            http_response_code(400);
            exit('Invalid task id.');
        }

        if ($is_completed_raw !== '0' && $is_completed_raw !== '1') {
            http_response_code(400);
            exit('Error: task must be checked or unchecked');
        }

        $task_id = (int)$task_id_raw;
        $is_completed = (int)$is_completed_raw;

        $values = [
            'id' => $task_id,
            'is_completed' => $is_completed
        ];
        $this->tasks_table->setTaskCompleted($values);
        header('Location: /tasks/list');
    }

    public function insert_edit_submit(): array { 
        $page_title = 'Insert task';
        if (isset($_POST['task'])) {
            $validation = new TaskValidation($_POST['task']);
            [$values, $errors] = $validation->processTaskSubmit();
            if($errors) {
                return ['page_title' => $page_title, 'template' => 'insertEdit.html.php', 'variables' => ['task' => $_POST['task'], 'errors' => $errors]];
            }
            $this->database_table->save($values);
            header('Location: /tasks/list');
            exit;

        }
        return ['page_title' => $page_title, 'template' => 'insertEdit.html.php', 'variables' => ['']];

    }

    public function insert_edit($task_id = null): array {    
        if (isset($task_id)){
            $page_title = 'Edit task';
            if($task_id <= 0) {
                $page_title = 'Error';
                $errors[] = ['Error: Invalid primary key provided.'];
                return ['page_title' => $page_title, 'template'=> 'insertEdit.html.php', 'variables' => [ 'errors' => $errors]];

            }
            $task = $this->database_table->find($task_id);
            
            return ['page_title' => $page_title, 'template' => 'insertEdit.html.php', 'variables' => ['task' => $task]];
        }
        $page_title = 'Insert task';
        return ['page_title' => $page_title, 'template' => 'insertEdit.html.php', 'variables' => ['']];
        }
    

    public function deleteSubmit() {
        $task_id = $_POST['id'] ?? null;

        if ($task_id === null || $task_id <= 0) {
                $errors = ['Error: Invalid primary key provided.'];
                $page_title = 'Error';
                return ['errors' => $errors, 'page_title' => $page_title];
        }
        $task_id = (int)$task_id;

        $this->database_table->delete($task_id);

        header('Location: /tasks/list');


        
    }

    public function home() {
        $page_title = 'Home Page';

        $result = $this->tasksTable->showHighPriorityTasks();

        return ['page_title' => $page_title, 'template' => 'home.html.php', 'variables' => [
            'tasks' => $result,
        ]];
    }
    
}