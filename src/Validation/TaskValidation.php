<?php 
namespace App\Validation;

use DateTimeImmutable;


class TaskValidation {
    private $data = [];
    private $errors = []; 

    public function __construct(private array $input) {}
    
    public function validate(): bool {
        $this->run();
        if (!empty($this->errors)) {
            return false;
        }

        return true;
    }
    
    private function run(): void {
        $this->processId();
        $this->processTaskTitle();
        $this->processTaskText();
        $this->processPriority();
        $this->processDate();
    }
    public function getData(): array {
        return $this->data;
    }

    public function getErrors(): array {
        return $this->errors;
    }



    private function processId(): void {
        $id = trim($this->input['id'] ?? '');

        if ($id === '') {
            return;
        }
        
        if((int)$id <= 0 || !ctype_digit($id)) {
            $this->errors['id'][] = 'Task cant be updated invalid id.';
            return;
        }
        $this->data['id'] = (int)$id;
        
    }

    private function processTaskTitle(): void {
        $rawData = trim($this->input['task_title'] ?? '');

        if ($rawData === '') {
            $this->errors['task_title'][] = 'Task title can\'t be empty';
            return;
        }

        if (mb_strlen($rawData) > 50) {
            $this->errors['task_title'][] = 'Task title can\'t be more than 50 characters';
            return;
        }

        $this->data['task_title'] = $rawData;
    }

    private function processTaskText(): void {
        $rawData =  trim($this->input['task_description'] ?? '');

        if ($rawData === '') {
            $this->errors['task_description'][] = 'Task can\'t be empty';
            return;
        }

        if (mb_strlen($rawData) > 255) {
            $this->errors['task_description'][] = 'Task can\'t be more than 255 characters';
            return;
        }        
        $this->data['task_description'] = $rawData;
    }

    private function processPriority(): void {
        $rawData = trim($this->input['priority'] ?? '2');

        if ($rawData === '' || !ctype_digit($rawData)) {
            $this->errors['priority'][] = 'Priority must be High, Medium, Low';
            return;
        }else {
            $p = (int)$rawData;
            if (!in_array($p, [1,2,3], true)) {
                $this->errors['priority'][] = 'Priority must be High, Medium, Low';
                return;
            }
    
            $this->data['priority'] = $p;

        }

    }

    private function processDate(): void {
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