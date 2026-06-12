<?php
namespace App\Validation;


class RegisterValidation {
    private $data = [];
    private $errors = [];
    
    public function __construct(private \App\Model\DatabaseTable $usersTable) {}

    public function processUserRegister(array $input){
        $this->proccessFlow($input);

        if (!empty($this->errors)) {
            return false;
        }   

        return true;


        
    }

    public function getData(): array {
        return $this->data;
    }

    public function getErrors(): array {
        return $this->errors;
    }
    private function proccessFlow(array $input) {
        $this->processId($input);
        
        $this->processUserName($input);

        $this->processEmail($input);

        $this->processFullName($input);

        $this->processPassword($input);
    }


    private function processId(array $input) {
        $id = trim($input['id'] ?? '');

        if ($id === '') {
            return;
        }
        
        if (!ctype_digit($id)) {
           $this->errors['id'][] = 'User information can\'t be updated:string detected.'; 
           return;
        }

        if((int)$id <= 0) {
            $this->errors['id'][] = 'User information can\'t be updated:Invalid id.';
            return;
        }

        $this->data['id'] = (int)$id;
        
    }


    private function processUserName(array $input) {
        $userName = trim($input['userName'] ?? '');

        
        if ($userName === ''){
            $this->errors['userName'][] = 'User name can\'t be blank.';
            return;
        }

        if(strlen($userName) < 3) {
            $this->errors['userName'][] = 'User name must be min 3 characters long.';
            return;

        } 
        
        if (strlen($userName) > 55) {
            $this->errors['userName'][] = 'User name must be max 55 characters long.';
            return;
        }

        $duplicateUser = $this->usersTable->find($userName, 'userName');
        
        if ($duplicateUser !== false) {
            $this->errors['userName'][] = 'This username already exists.';
            return;
        }
    
        
        $this->data['userName'] = $userName;
    }

    private function processEmail(array $input) {
        $email = trim($input['email'] ?? '');
    
        if ($email === '') {
            $this->errors['email'][] = 'Email can\'t be blank.';
            return;
        }
        
        if (strlen($email) > 254){
            $this->errors['email'][] = 'Email can\'t be more than 254 characters long.';
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'][] = 'Invalid email address.';
            return;
        }
        $splittedEmail = explode('@', $email);

        $hostname = strtolower($splittedEmail[1]);

        $checkedDns = dns_get_record($hostname);

        if ($checkedDns === false  || $checkedDns === []) {
            $this->errors['email'][] = 'Invalid email domain address.';
            return;
        }

        $duplicateEmail = $this->usersTable->find($email, 'email');

        if ($duplicateEmail !== false) {
            $this->errors['email'][] = 'Account with this email addres already exists.';
            return;
        }

        $this->data['email'] = $email;
    }

    private function processFullName(array $input) {
        $fullName = trim($input['fullName'] ?? '');

        if ($fullName === '') {
            $this->errors['fullName'][] = 'Full name can\'t be blank';
            return;
        }
        
        if (strlen($fullName) < 3 ) {
            $this->errors['fullName'][] = 'Full name can\'t be less than 3 characters long';
            return;
        }

        if(strlen($fullName) > 100) {
            $this->errors['fullName'][] = 'Full name can\'t be more than 100 characters long.';
            return;
        } 


        $this->data['fullName'] = $fullName;
    }

    private function processPassword(array $input) {
        $password = $input['password'] ?? '';
        

        if($password === '') {
            $this->errors['password'][] = 'Password cant be blank.';
            return;
        }

        if(strlen($password) < 11) {
            $this->errors['password'][] = 'Password must be minimum 11 characters long.';
            return;
        }
        if (strlen($password) > 15) {
            $this->errors['password'][] = 'Password must be maximum 15 characters long';
            return;
        }


       $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

       $this->data['passwordHash'] = $hashedPassword;

    }


}