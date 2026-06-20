<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory;

    protected $fillable = [
        'pillar_id',
        'user_id',
        'name',
        'description',
        'color',
    ];

    // Relationships
    public function pillar()
    {
        return $this->belongsTo(Pillar::class);
    }

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function references()
    {
        return $this->hasMany(Reference::class, 'domain_id');
    }

    public function vocabulary()
    {
        return $this->hasMany(Vocabulary::class, 'domain_id');
    }

    public function links()
    {
        return $this->hasMany(Link::class, 'domain_id');
    }

    // Scopes
    public function scopeForPillar($query, $pillarId)
    {
        return $query->where('pillar_id', $pillarId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
