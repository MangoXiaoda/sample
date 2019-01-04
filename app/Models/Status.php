<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{

    protected $fillable = ['content'];

    // 关联模型

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}