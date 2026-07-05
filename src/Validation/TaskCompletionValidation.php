<?php
namespace App\Validation;


class TaskCompletionValidation {
    private $data = [];
    private $errors = [];
    public function __construct(private array $input) {}


    public function validate() {
        if ($this->processCompletion() === false) {
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

    private function processCompletion() {
        $is_completed = $this->input['is_completed'] ?? '';
        $id = $this->input['id'] ?? '';

        if ($id === '') {
            $this->errors['id'][] = 'Invalid value';
            return false;
        }

        if (!ctype_digit($id)) {
            $this->errors['id'][] = 'Invalid value';
            return false;
        }
        $id = (int)$id;

        if ($id === 0) {
            $this->errors['id'][] = 'Invalid value';
            return false;
        }

        if ($is_completed === '') {
            $this->errors['is_completed'][] = 'Invalid state of the task';
            return false;
        }

        if ($is_completed !== '0' && $is_completed !== '1') {
            $this->errors['is_completed'][] = 'Task must be in checked or unchecked state';
            return false;
        } 

        $is_completed = (int)$is_completed;
        $this->data['id'] = $id;
        $this->data['is_completed'] = $is_completed;
        return true;

    }




}