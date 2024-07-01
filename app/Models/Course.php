<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;
    protected $fillable = ['type', 'semester', 'expire_date', 'semester_price', 'month_price', 'material_id', 'teacher_id', 'classroom_id'];
}
