<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dispatch_id',
        'marked_as_read',
        'description'
    ];

    public function dispatch(){
        return $this->BelongsTo(Dispatch::class);
    }

     public function user(){
        return $this->BelongsTo(User::class);
    }
}
