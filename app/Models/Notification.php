<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
#[Fillable([ 'type',
        'message',
        'actor_name',
        'actor_avatar',
        'read_at',])]
class Notification extends Model 
{
    /** @use HasFactory<\Database\Factories\NotificationFactory> */
    use HasFactory;
    protected function casts():array{
        return [ 
            'read_at' => 'datetime'
        ] ;
    }
}