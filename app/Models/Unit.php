<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description'];
    protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
      ];

    public function courses()
    {
        return $this->belongsTo(Course::class, 'courses_id');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'units_id');
    }
}
