<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bout extends Model
{
    public $incrementing = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'sport',
        'bout_number',
        'is_main_event',
        'rounds',
        'weight_class',
        'red_fighter_id',
        'blue_fighter_id',
        'event_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_main_event' => 'boolean',
        ];
    }

    public function redFighter(): BelongsTo
    {
        return $this->belongsTo(Fighter::class, 'red_fighter_id');
    }

    public function blueFighter(): BelongsTo
    {
        return $this->belongsTo(Fighter::class, 'blue_fighter_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
