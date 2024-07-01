<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'semester',
        'unsolved',
        'solved',
        'book',
        'material_id',
        'classroom_id',
    ];
}
