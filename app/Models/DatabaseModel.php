<?php

namespace App\Models;

$dbConnection = $_ENV['DB_CONNECTION'] ?? getenv('DB_CONNECTION') ?? (function_exists('env') ? env('DB_CONNECTION') : null);
if ($dbConnection === 'mongodb' && class_exists(\MongoDB\Laravel\Eloquent\Model::class)) {
    abstract class DatabaseModel extends \MongoDB\Laravel\Eloquent\Model
    {
    }
} else {
    abstract class DatabaseModel extends \Illuminate\Database\Eloquent\Model
    {
    }
}
