<?php 

namespace App\Core;

class Authentication {
    public function __construct(private \App\Model\DatabaseTable $users, private string $password_column) {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    }


    public function login(string $identity, string $user_column_name, string $password):bool {
        $user = $this->users->find($identity, $user_column_name);
        if ($user !== false && password_verify($password, $user[$this->password_column])) {
            session_regenerate_id();
            $_SESSION['user_name'] = $user['user_name'];
            $_SESSION['user_id'] = $user['id'];

            return true;
        }
        return false;
    }

    public function isLoggedIn(): bool {
      return isset($_SESSION['user_id']);
    }

    public function logout(): void{
        session_unset();

        session_destroy();
    }
}