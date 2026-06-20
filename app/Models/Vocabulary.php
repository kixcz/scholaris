<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vocabulary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'term',
        'definition',
        'personal_understanding',
        'example',
        'related_refs',
    ];

    protected $casts = [
        'related_refs' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::addGlobalScope('user', function ($query) {
            if (auth()->check()) {
                $query->where('user_id', auth()->id());
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('term', 'like', "%{$search}%")
              ->orWhere('definition', 'like', "%{$search}%");
        });
    }
}
