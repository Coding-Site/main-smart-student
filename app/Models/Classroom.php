<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Classroom extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'name_en',
        'image',
    ];
    public function level(): BelongsTo
    {
        return $this->belongsTo(EducationLevel::class);
    }
}
