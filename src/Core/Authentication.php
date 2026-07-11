<?php 

namespace App\Core;

class Authentication {
    public function __construct(private \App\Model\DatabaseTable $users, private string $passwordColumn) {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    }


    public function login(string $identity, string $userColumnName, string $password):bool {
        $user = $this->users->find($identity, $userColumnName);
        if ($user !== false && password_verify($password, $user[$this->passwordColumn])) {
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

    public function getUserId(): int {
        if (!isset($_SESSION['user_id'])) {
            throw new \RuntimeException('getUserId called without an authenticated session - check route protection');
        }

        return $_SESSION['user_id'];
    }
}

