<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain_id',
        'pillar_id',
        'user_id',
        'name',
        'description',
    ];

    // Relationships
    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function pillar()
    {
        return $this->belongsTo(Pillar::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function references()
    {
        return $this->hasMany(Reference::class, 'topic_id');
    }

    public function vocabulary()
    {
        return $this->hasMany(Vocabulary::class, 'topic_id');
    }

    public function links()
    {
        return $this->hasMany(Link::class, 'topic_id');
    }

    // Scopes
    public function scopeForDomain($query, $domainId)
    {
        return $query->where('domain_id', $domainId);
    }

    public function scopeForPillar($query, $pillarId)
    {
        return $query->where('pillar_id', $pillarId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
