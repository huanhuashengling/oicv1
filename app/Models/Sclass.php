<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sclass extends Model
{
    use HasFactory;

    protected $fillable = [
        'schools_id', 'enter_school_year', 'class_title', 'class_num', 'is_graduated'
    ];
}
