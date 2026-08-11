<?php

namespace App\Models;

use Database\Factories\CityEventFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CityEvent extends Model
{
    /** @use HasFactory<CityEventFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'city_id',
        'event_id',
        'active',
        'winning_betting_option_id',
        'settled_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'settled_at' => 'datetime',
        ];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function bettingOptions(): HasMany
    {
        return $this->hasMany(BettingOption::class);
    }

    public function bets(): HasMany
    {
        return $this->hasMany(Bet::class);
    }

    public function winningBettingOption(): BelongsTo
    {
        return $this->belongsTo(BettingOption::class, 'winning_betting_option_id');
    }

    public function isSettled(): bool
    {
        return $this->settled_at !== null;
    }
}
