<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class DatabaseYearExpression
{
    /**
     * Build a portable SQL expression that extracts a year from a date column.
     */
    public static function fromDateColumn(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "CAST(strftime('%Y', {$column}) AS INTEGER)",
            'mysql', 'mariadb' => "YEAR({$column})",
            default => "EXTRACT(YEAR FROM {$column})",
        };
    }
}
