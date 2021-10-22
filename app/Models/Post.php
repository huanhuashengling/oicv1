<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'students_id', 'lesson_logs_id', 'export_name', 'file_ext', 'cover_ext', 'post_code'
    ];

    protected $casts = [
      'created_at' => 'datetime:Y-m-d',
      'updated_at' => 'datetime:Y-m-d',
      ];

    public function hasManyComments()
    {
        return $this->hasMany('App\Models\Comment', 'post_id', 'id');
    }
}
