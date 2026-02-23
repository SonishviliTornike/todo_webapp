<?php 

function taskCreateValidation(array $input) {
    $errors = [];
    $data = [];

    $data['task_title'] = trim($input['task_title'] ?? '');
    $data['task_description'] = trim($input['task_description'] ?? '');
    $data['priority'] = trim($input['priority'] ?? '2');
    $data['due_at_raw'] = trim($input['due_at'] ?? '');
    
    if ($data['task_title'] === '' || mb_strlen($data['task_title']) > 100) {
        $errors['task_title'][] = 'Title is required and must be less than 100 characters.';
    }

    if ($data['task_description'] === '' || mb_strlen($data['task_description']) > 1000 ){
        $errors['task_description'][] = 'Description must be filled and must be less tha 1000 characters.';
    } 

    if (!ctype_digit($data['priority'])) {
        $errors['priority'][] = 'Priority must be High, Medium, Low';
    } else {
        $p = (int)$data['priority'];
        if (!in_array($p, [1,2,3], true)){
            $errors['priority'][] = 'Priority must be High, Medium, Low';
        }
        $data['priority'] = $p;
    }

    $data['due_at'] = null;

    if($data['due_at_raw'] !== '') {
        $dt = DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $data['due_at_raw']);
        $err = DateTimeImmutable::getLastErrors() ?: ['warning_count' => 0, 'error_count' => 0];
        if (!$dt || $err['warning_count'] || $err['error_count']) {
            $errors['due_at'][] = 'Invalid deadline value';
        } else {
            $data['due_at'] = $dt->format('Y-m-d H:i:s');
        }
    }

    $today_date = new DateTimeImmutable();
    $today_date = $today_date->format('Y-m-d:H:i:s');
    
    if ($data['due_at'] < $today_date){
        $errors['due_at'][] = 'Cannot select past date';
    }

    unset($data['due_at_raw']);  
    return [$data, $errors];
}