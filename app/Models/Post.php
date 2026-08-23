<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title',
    'excerpt',
    'content',
    'category',
    'author_name',
    'published_at', ])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    #[Scope()]
    public function inCategory(Builder $query, ?string $category): void
    {
        if ($category) {
            $query->where('category', $category);
        }
    }

    #[Scope()]
    public function search(Builder $query, ?string $term)
    {
        if ($term) {
            $query->where(function (Builder $q) use ($term) {
                $q->where('title', 'LIKE', "%{$term}%")
                    ->orWhere('excerpt', 'LIKE', "%{$term}%");
            });
        }
    }
}
