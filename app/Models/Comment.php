<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['nickname', 'email', 'website', 'content', 'work_id'];

    public function hasOnePost()
    {
        return $this->hasOne('App\Models\Post', 'id', 'post_id');
    }
}
