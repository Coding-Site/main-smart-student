<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EducationLevel extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'name_en',
        'image',
    ];
    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }
}
