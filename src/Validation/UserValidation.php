<?php
namespace App\Validation;

class UserValidation {
    private $data = [];
    private $errors = [];
    
    public function __construct(private array $input) {}

    public function processUserRegister(){
        $this->proccessFlow();
    }


    private function proccessFlow() {
        $this->processId();
        
        $this->processUserName();

        $this->processEmail();
    }


    private function processId() {
        $id = trim($this->input['id'] ?? '');

        if ($id === '') {
            return;
        }

        if((int)$id <= 0 || !ctype_digit($id)) {
            $this->errors['id'][] = 'User information can\'t be updated:Invalid id.';
            return;
        } else {
            $this->data['id'] = (int)$id;
        }
    }


    private function processUserName() {
        $userName = trim($this->input['userName'] ?? '');

        
        if (empty($userName) || strlen($userName) > 55  || strlen($userName < 3)) {
            $this->errors['userName'][] = 'User name must be min 3 characters long and max  55 characters long';
            return;
        }

        $this->data['userName'] = $userName;
    }

    private function processEmail() {
        $email = trim($this->input['email']) ?? '';
    

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'][] = 'Invalid email address.';
            return;
        }
        
        if (strlen($email) > 254) {
            $this->errors['email'][] = 'Email must be max 254 characters long.';
            return;
        }
        
        $splittedEmail = explode($email, '@');
        $hostname = $splittedEmail[1];
        $mxhosts = [];

        if (!getmxrr($hostname, $mxhosts)){
            $this->errors['email'][] = 'Invalid email domain address.';
            return;
        }

        $this->data['email'] = $email;
        


    }

}