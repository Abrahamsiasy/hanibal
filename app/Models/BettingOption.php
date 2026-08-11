<?php

namespace App\Models;

use Database\Factories\BettingOptionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BettingOption extends Model
{
    /** @use HasFactory<BettingOptionFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'city_event_id',
        'name',
        'odds',
        'active',
        'position',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'active' => true,
        'position' => 0,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'odds' => 'decimal:2',
            'active' => 'boolean',
            'position' => 'integer',
        ];
    }

    public function cityEvent(): BelongsTo
    {
        return $this->belongsTo(CityEvent::class);
    }

    public function bets(): HasMany
    {
        return $this->hasMany(Bet::class);
    }
}
