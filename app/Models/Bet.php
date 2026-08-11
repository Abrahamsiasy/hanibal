<?php

namespace App\Models;

use App\Enums\BetStatus;
use Database\Factories\BetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bet extends Model
{
    /** @use HasFactory<BetFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'city_event_id',
        'betting_option_id',
        'stake',
        'odds',
        'potential_payout',
        'status',
        'settled_at',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'pending',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'stake' => 'decimal:2',
            'odds' => 'decimal:2',
            'potential_payout' => 'decimal:2',
            'status' => BetStatus::class,
            'settled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cityEvent(): BelongsTo
    {
        return $this->belongsTo(CityEvent::class);
    }

    public function bettingOption(): BelongsTo
    {
        return $this->belongsTo(BettingOption::class);
    }
}
