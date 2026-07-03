<?php

namespace App\Models;

if (class_exists(\MongoDB\Laravel\Auth\User::class)) {
    abstract class DatabaseAuthenticatable extends \MongoDB\Laravel\Auth\User
    {
    }
} else {
    abstract class DatabaseAuthenticatable extends \Illuminate\Foundation\Auth\User
    {
    }
}
