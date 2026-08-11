<?php

namespace App\Enums;

enum WalletTransactionType: string
{
    case Deposit = 'deposit';
    case Withdrawal = 'withdrawal';
    case BetStake = 'bet_stake';
    case BetWin = 'bet_win';
    case BetRefund = 'bet_refund';

    public function label(): string
    {
        return match ($this) {
            self::Deposit => 'Deposit',
            self::Withdrawal => 'Withdrawal',
            self::BetStake => 'Bet Stake',
            self::BetWin => 'Bet Win',
            self::BetRefund => 'Bet Refund',
        };
    }

    public function isCredit(): bool
    {
        return match ($this) {
            self::Deposit, self::BetWin, self::BetRefund => true,
            self::Withdrawal, self::BetStake => false,
        };
    }
}
