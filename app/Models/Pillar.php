<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pillar extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'icon',
        'color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function domains()
    {
        return $this->hasMany(Domain::class);
    }

    public function topics()
    {
        return $this->hasManyThrough(Topic::class, Domain::class);
    }

    public function references()
    {
        return $this->hasMany(Reference::class, 'pillar_id');
    }

    public function notebooks()
    {
        return $this->hasMany(Notebook::class, 'pillar_id');
    }

    public function vocabulary()
    {
        return $this->hasMany(Vocabulary::class, 'pillar_id');
    }

    public function links()
    {
        return $this->hasMany(Link::class, 'pillar_id');
    }

    public function roadmaps()
    {
        return $this->hasMany(Roadmap::class, 'pillar_id');
    }

    public function weeklyGoals()
    {
        return $this->hasManyThrough(WeeklyGoal::class, Roadmap::class, 'pillar_id', 'roadmap_id', 'id', 'id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
