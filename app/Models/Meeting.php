<?php

namespace App\Models;

use Database\Factories\MeetingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'date', 'notes'])]
class Meeting extends Model
{
    /** @use HasFactory<MeetingFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}
