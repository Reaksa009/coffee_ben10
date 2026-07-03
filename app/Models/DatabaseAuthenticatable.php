<?php

namespace App\Models;

$dbConnection = $_ENV['DB_CONNECTION'] ?? getenv('DB_CONNECTION') ?? (function_exists('env') ? env('DB_CONNECTION') : null);
if ($dbConnection === 'mongodb' && class_exists(\MongoDB\Laravel\Auth\User::class)) {
    abstract class DatabaseAuthenticatable extends \MongoDB\Laravel\Auth\User
    {
    }
} else {
    abstract class DatabaseAuthenticatable extends \Illuminate\Foundation\Auth\User
    {
    }
}
