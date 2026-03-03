<?php 


class TasksController {
    public function __construct(private DatabaseTable $tasksTable) {}


    public function list() {
        $tasks = $this->tasksTable->findAll();

        $totalTasks = $this->tasksTable->totalTasks();

        return ['tasks' => $tasks, 'totalTasks' => $totalTasks];
        
    }

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

    }
    public function insertEdit() { 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $page_title = 'Insert task';
                $this->tasksTable->save($_POST['task']);
                return ['page_title' => $page_title];
            } catch (PDOException $e) {
                $page_title = 'Error';
                $errors['form'] = ['Server error: Please try again.'];
                return ['errors' => $errors];
            }
        } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $page_title = 'Edit task';

            $taskId = $_GET['task_id'] ?? null;

            if ($taskId < '0') {
                $errors[] = ['Erorr: Invalid primary key provided.'];
            }
            $taskId = (int)$taskId;
            $task = $this->tasksTable->find($taskId);

            if ($task) {
                $old_task = [
                    'task_id' => $task['task_id'],
                    'task_title' => $task['task_title'],
                    'task_description' => $task['task_description'],
                    'due_at' => $task['due_at'],
                    'priority' => $task['priority']
                ];
                return ['old_task' => $old_task];
            }
        }
    }
    
}