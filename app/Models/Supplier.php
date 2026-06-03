<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends DatabaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_name',
        'phone',
        'email',
        'address',
        'notes',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
