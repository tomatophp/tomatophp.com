<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TomatoPHP\FilamentCms\Models\Post;

class Like extends Model
{
    use HasFactory;

    protected $fillable = ['account_id', 'post_id'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
