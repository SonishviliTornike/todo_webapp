<?php 
namespace App\Validation;

use DateTimeImmutable;


class TaskValidation {
    private $data = [];
    private $errors = []; 

    public function __construct(private array $input) {}
    
    public function validate() {
        if ($this->processFlow() === false ) {
            return false;
        }

        return true;
    }

    public function getData() {
        return $this->data;
    }

    public function getErrors() {
        return $this->errors;
    }

    private function processFlow() {
        if (!$this->processId()) {
            return false;
        }
        
        if (!$this->processTaskTitle()) {
            return false;
        }

        if (!$this->processTaskText()) {
            return false;
        }

        if (!$this->processPriority()) {
            return false;
        }

        if (!$this->processDate()) {
            return false;
        }

        return true;

    }

    private function processId() {
        $id = trim($this->input['id'] ?? '');

        if ($id === '') {
            return true;
        }
        
        if((int)$id <= 0 || !ctype_digit($id)) {
            $this->errors['id'][] = 'Task cant be updated invalid id.';
            return false;
        } else {
            $this->data['id'] = (int)$id;

            return true;
        }
    }

    private function processTaskTitle() {
        $rawData = trim($this->input['task_title'] ?? '');

        if ($rawData=== '' || mb_strlen($rawData) > 100) {
            $this->errors['task_title'][] = 'Task title can\'t be empty or more than 100 characters';
            return false;
        }

        $this->data['task_title'] = $rawData;
        return true;
    }

    private function processTaskText() {
        $rawData =  trim($this->input['task_description'] ?? '');

        if ($rawData === '' || mb_strlen($rawData) > 1000) {
            $this->errors['task_description'][] = 'Task can\'t be empty or more than 1000 characters';
            return false;
        }        
        $this->data['task_description'] = $rawData;
        return true;
    }

    private function processPriority() {
        $rawData = trim($this->input['priority'] ?? '2');

        if ($rawData === '' || !ctype_digit($rawData)) {
            $this->errors['priority'][] = 'Priority must be High, Medium, Low';
            return false;
        }else {
            $p = (int)$rawData;
            if (!in_array($p, [1,2,3], true)) {
                $this->errors['priority'][] = 'Priority must be High, Medium, Low';
                return false;
            }
    
            $this->data['priority'] = $p;
            return true;

        }

    }

    private function processDate() {
        $due_raw = trim($this->input['due_at'] ?? '');
        $now = new DateTimeImmutable();

        if ($due_raw === '') {
            $this->data['due_at'] = $now->format('Y-m-d H:i');
            return true;
        }
        $dt = DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $due_raw);
        $err = DateTimeImmutable::getLastErrors() ?: ['warning_count' => 0, 'error_count' => 0];
        if (!$dt || $err['warning_count'] != 0 || $err['error_count'] != 0 ) {
            $this->errors['due_at'][] = 'Invalid deadline value.';
            return false;
        }
        if ($dt < $now) {
            $this->errors['due_at'][] = 'Deadline can\'t be past time.';   
            return false;
        }
        $this->data['due_at'] = $dt->format('Y-m-d H:i');
        return true;
    }
}