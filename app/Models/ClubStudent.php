<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubStudent extends Model
{
    use HasFactory;

    protected $fillable = ['clubs_id', 'students_id', 'status'];
}
