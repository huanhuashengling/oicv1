<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'teachers_id', 'sclasses_id', 'lessons_id', 'status', 'rethink', 'ended_at'
    ];
}

