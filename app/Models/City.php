<?php

namespace App\Models;

use Database\Factories\CityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    /** @use HasFactory<CityFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'hero_title',
        'hero_subtitle',
        'hero_image',
        'venue',
        'active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function banners(): HasMany
    {
        return $this->hasMany(CityBanner::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function cityEvents(): HasMany
    {
        return $this->hasMany(CityEvent::class);
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'city_events')
            ->withPivot(['id', 'active'])
            ->withTimestamps();
    }
}
