<?php 
namespace App\Validation;

use DateTimeImmutable;

class TaskValidation {
    private $data = [];
    private $errors = []; 

    public function __construct(private array $input) {}
    
    public function processPostRequest() {
        $this->processId();
        $this->processTaskTitle();
        $this->processTaskText();
        $this->processPriority();
        $this->processDate();
        

        return [$this->data, $this->errors];
    }


    private function processId() {
        $this->data['id'] = trim($this->input['id'] ?? '');
        if ($this->data['id'] == '') {
            unset($this->data['id']);
        } else if ((int)$this->data['id'] < 0 || !ctype_digit($this->data['id'])) {
            $this->errors['id'][] = 'Task can\'t be inserted or updated due to invalid id value';
        }
    }

    private function processTaskTitle() {
        $this->data['task_title'] = trim($this->input['task_title'] ?? '');
        if (empty($this->data['task_title']) || mb_strlen($this->data['task_title']) > 100) {
            $this->errors['task_title'][] = 'Task title can\'t be empty or more than 100 characters';
        }

    }

    private function processTaskText() {
        $this->data['task_description'] = trim($this->data['task_description'] ?? '');
        if (empty($data['task_description']) || mb_strlen($this->data['task_description']) > 1000) {
            $this->errors['task_description'][] = 'Task can\'t be empty or more than 1000 characters';
        }        
    }

    private function processPriority() {
        $this->data['priority'] = trim($this->input['priority'] ?? '2');

        if (empty($this->data['priority']) || !ctype_digit($this->data['priority'])) {
            $this->errors['priority'][] = 'Priority must be High, Medium, Low';
        }
        

        $p = (int)$this->data['priority'];
        if (!in_array($p, [1,2,3], true)) {
            $this->errors['priority'][] = 'Priority must be High, Medium, Low';
        }

        $this->data['priority'] = $p;

    }

    private function processDate() {
        $this->data['due_at'] = null;
        $this->data['due_at_raw'] = trim($this->input['due_at'] ?? '');

        if ($this->data['due_at_raw'] !== '') {
            $dt = DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $this->data['due_at_raw']);
            $err = DateTimeImmutable::getLastErrors() ?: ['warning_count' => 0, 'err_count' => 0];
            $now = new DateTimeImmutable();
            if (!$dt || $err['warning_count'] != 0 || $err['err_count'] != 0 ) {
                $this->errors['due_at'][] = 'Invalid deadline value';
            } else {
                if ($dt < $now) {
                    $this->errors['due_at'][] = 'Invalid deadline value';
                } else {
                    $this->data['due_at'] = $dt->format('Y-m-d H:i:s');
                }
            }
            unset($this->data['due_at_raw']);

        } else {
            unset($this->data['due_at_raw']);
            $this->data['due_at'] = new DateTimeImmutable();
        }
    }

}