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

if ($isMongo && class_exists(\MongoDB\Laravel\Auth\User::class)) {
    abstract class DatabaseAuthenticatable extends \MongoDB\Laravel\Auth\User
    {
        public function getAttribute($key)
        {
            if ($key === 'id') {
                return (string) $this->getKey();
            }
            return parent::getAttribute($key);
        }

        public function getAttributeValue($key)
        {
            if ($key === 'id') {
                return (string) $this->getKey();
            }
            return parent::getAttributeValue($key);
        }
    }
} else {
    abstract class DatabaseAuthenticatable extends \Illuminate\Foundation\Auth\User
    {
    }
}
