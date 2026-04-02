<?php 
namespace App\Validation;

use DateTimeImmutable;

class TaskValidation {
    public function __construct(private array $input) {}


    private function sanitizingData() {
        $data = [];

        $data['id'] = trim($this->input['id'] ?? '');
        $data['task_title'] = trim($this->input['task_title'] ?? '');
        $data['task_description'] = trim($this->input['task_description'] ?? '');
        $data['priority'] = trim($this->input['priority'] ?? '');
        $data['due_at_raw'] = trim($this->input['due_at_raw'] ?? '');

        return $data;
    } 

    public function validate() {
        $data = $this->sanitizingData();
        $errors = [];
        // if ($data['id'] == '') {
        //     unset($data['id']);
        // }
        // if ((int)$data['id'] < 0 || !ctype_digit($data['id'])) {
        //     $errors['id'][] = 'Task can\'t be updated.';
        // }

        if ($data['task_title'] === '' || mb_strlen($data['task_title']) > 100) {
            $errors['task_title'][] = 'Task title can\'t be empty or more than 100 characters.';
        }

        if ($data['task_description'] === '' || mb_strlen($data['task_description']) > 1000) {
            $errors['task_description'][] = 'Task description can\'t be empty or more than 1000 characters.';
        }

        if (!ctype_digit($data['priority'] )) {
            $errors['priority'][] = 'Task priority must be High, Medium, Low';
        } else {
            $p = (int)$data['priority'];
            if (!in_array($p, [1,2,3], true)) {
                $errors['priority'][] = 'Priority must be High, Medium, Low';
            }
            $data['priority'] = $p;
        }
        $data['due_at'] = null;

        if($data['due_at_raw'] !== '') {
            $dt = DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $data['due_at_raw']);
            $err = DateTimeImmutable::getLastErrors() ?: ['warning_count' => 0, 'error_count' => 0];
            $now = new DateTimeImmutable();
            if (!$dt || $err['warning_count'] || $err['error_count']) {
                $errors['due_at'][] = 'Invalid deadline value.';
            } else {
                if ($dt < $now) {
                    $errors['due_at'][] = 'Cannot add past date.';
                } else {
                    $data['due_at'] = $dt->format('Y-m-d H:i:s');
                }
            }
        }
        unset($data['due_at_raw']);
        return [$data, $errors];
    }
}