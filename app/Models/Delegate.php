<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Delegate extends Model
{
    use HasFactory, Notifiable, HasApiTokens;
    protected $fillable = [
        'user_id',
    ];
    public function user()
    {
        return $this->morphOne(User::class, 'userable');
    }
}
