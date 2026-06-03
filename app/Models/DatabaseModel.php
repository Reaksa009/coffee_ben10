<?php

namespace App\Models;

if (env('DB_CONNECTION') === 'mongodb' && class_exists(\MongoDB\Laravel\Eloquent\Model::class)) {
    abstract class DatabaseModel extends \MongoDB\Laravel\Eloquent\Model
    {
    }
} else {
    abstract class DatabaseModel extends \Illuminate\Database\Eloquent\Model
    {
    }
}
