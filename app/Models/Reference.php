<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pillar_id',
        'domain_id',
        'topic_id',
        'title',
        'authors',
        'year',
        'journal',
        'doi',
        'url',
        'type',
        'tier',
        'pillar',
        'status',
        'pdf_path',
        'notes',
        'tags',
    ];

    protected $casts = [
        'year' => 'integer',
        'tier' => 'integer',
        'tags' => 'array',
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

    public function pillar(): BelongsTo
    {
        return $this->belongsTo(Pillar::class);
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'ref_id');
    }

    // Scopes
    public function scopeByTier($query, $tiers)
    {
        return $query->whereIn('tier', $tiers);
    }

    public function scopeByPillar($query, $pillars)
    {
        return $query->whereIn('pillar', $pillars);
    }

    public function scopeByStatus($query, $statuses)
    {
        return $query->whereIn('status', $statuses);
    }

    public function scopeByType($query, $types)
    {
        return $query->whereIn('type', $types);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('authors', 'like', "%{$search}%")
              ->orWhere('journal', 'like', "%{$search}%");
        });
    }
}
