<?php

namespace App\Support;

class StatusThreshold
{
    /**
     * >20% offline = Critical, otherwise Normal. Unknown when there's nothing to divide by.
     */
    public static function label(int $offline, int $total): string
    {
        if ($total <= 0) {
            return 'Unknown';
        }

        return (($offline / $total) * 100) > 20 ? 'Critical' : 'Normal';
    }
}