<?php

namespace App\Enums;

enum BetStatus: string
{
    case Pending = 'pending';
    case Won = 'won';
    case Lost = 'lost';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Won => 'Won',
            self::Lost => 'Lost',
            self::Cancelled => 'Cancelled',
            self::Refunded => 'Refunded',
        };
    }
}
