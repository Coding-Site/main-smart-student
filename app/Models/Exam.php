<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'semester',
        'first',
        'second',
        'unsolved',
        'solved',
        'final',
        'material_id',
        'classroom_id',
    ];
}
