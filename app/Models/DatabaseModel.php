<?php

namespace App\Models;

$isMongo = false;
$conn = $_ENV['DB_CONNECTION'] ?? $_SERVER['DB_CONNECTION'] ?? getenv('DB_CONNECTION');

if ($conn === 'mongodb') {
    $isMongo = true;
} elseif (function_exists('env')) {
    try {
        if (env('DB_CONNECTION') === 'mongodb') {
            $isMongo = true;
        }
    } catch (\Throwable $e) {
        // Ignore errors during early load
    }
}

if ($isMongo && class_exists(\MongoDB\Laravel\Eloquent\Model::class)) {
    abstract class DatabaseModel extends \MongoDB\Laravel\Eloquent\Model
    {
    }
} else {
    abstract class DatabaseModel extends \Illuminate\Database\Eloquent\Model
    {
    }
}
