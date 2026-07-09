<?php
namespace App\Model;

enum UpdateResult {
    case Changed;
    case Unchanged;
    case NotFound;
}