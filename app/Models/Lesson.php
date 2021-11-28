<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'teachers_id', 'title', 'subtitle', 'help_md_doc', 'allow_post_file_types', 'description', 'default_sb3_name', 'lesson_code'
    ];

    protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
      ];

    public function teacher()
    {
        return $this->belongsTo('App\Models\Teacher');
    }

    public function units()
    {
        return $this->belongsTo(Unit::class, 'units_id');
    }
}
