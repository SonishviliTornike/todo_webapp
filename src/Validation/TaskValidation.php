<?php 
namespace App\Validation;

use DateTimeImmutable;


class TaskValidation {
    private $data = [];
    private $errors = []; 

    public function __construct(private array $input) {}
    
    public function processPostRequest() {
        $this->processFlow();

        return [$this->data, $this->errors];
    }

    private function processFlow() {
        $this->processId();
        
        $this->processTaskTitle();

        $this->processTaskText();

        $this->processPriority();

        $this->processDate();

    }

    private function processId() {
        $id = trim($this->input['id'] ?? '');

        if ($id === '') {
            return;
        }
        
        if((int)$id <= 0 || !ctype_digit($id)) {
            $this->errors['id'][] = 'Task cant be updated invalid id.';
            return;
        } else {
            $this->data['id'] = (int)$id;
        }
    }

    private function processTaskTitle() {
        $this->data['task_title'] = trim($this->input['task_title'] ?? '');
        if (empty($this->data['task_title']) || mb_strlen($this->data['task_title']) > 100) {
            $this->errors['task_title'][] = 'Task title can\'t be empty or more than 100 characters';
            return;
        }

    }

    private function processTaskText() {
        $this->data['task_description'] = trim($this->input['task_description'] ?? '');
        if (empty($this->data['task_description']) || mb_strlen($this->data['task_description']) > 1000) {
            $this->errors['task_description'][] = 'Task can\'t be empty or more than 1000 characters';
            return;
        }        
    }

    private function processPriority() {
        $this->data['priority'] = trim($this->input['priority'] ?? '2');

        if (empty($this->data['priority']) || !ctype_digit($this->data['priority'])) {
            $this->errors['priority'][] = 'Priority must be High, Medium, Low';
            return;
        }else {
            $p = (int)$this->data['priority'];
            if (!in_array($p, [1,2,3], true)) {
                $this->errors['priority'][] = 'Priority must be High, Medium, Low';
                return;
            }
    
            $this->data['priority'] = $p;

        }

    }

    private function processDate() {
        $this->data['due_at'] = null;
        $due_raw = trim($this->input['due_at'] ?? '');
        $now = new DateTimeImmutable();

        if ($due_raw === '') {
            $this->data['due_at'] = $now->format('Y-m-d H:i');
            return;
        }
        $dt = DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $due_raw);
        $err = DateTimeImmutable::getLastErrors() ?: ['warning_count' => 0, 'error_count' => 0];
        if (!$dt || $err['warning_count'] != 0 || $err['error_count'] != 0 ) {
            $this->errors['due_at'][] = 'Invalid deadline value.';
            return;
        }
        if ($dt < $now) {
            $this->errors['due_at'][] = 'Deadline can\'t be past time.';   
            return;
        }
        $this->data['due_at'] = $dt->format('Y-m-d H:i');
    }
}