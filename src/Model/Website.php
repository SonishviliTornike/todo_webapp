<?php 


namespace App\Model;

interface Website {
    public function getDefaultRoute(): string;

    public function getController(string $taskController): ? object;
}