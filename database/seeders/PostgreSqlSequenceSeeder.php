<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostgreSqlSequenceSeeder extends Seeder
{
    /**
     * Sinkronkan seluruh sequence ID setelah data dengan ID eksplisit
     * dipindahkan dari Oracle ke PostgreSQL.
     */
    public function run(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $columns = DB::select(<<<'SQL'
            SELECT table_schema, table_name
            FROM information_schema.columns
            WHERE table_schema = current_schema()
              AND column_name = 'id'
              AND (is_identity = 'YES' OR column_default LIKE 'nextval(%')
            ORDER BY table_name
        SQL);

        foreach ($columns as $column) {
            $qualifiedTable = $column->table_schema.'.'.$column->table_name;
            $sequence = DB::selectOne(
                "SELECT pg_get_serial_sequence(?, 'id') AS name",
                [$qualifiedTable]
            )?->name;

            if (! $sequence) {
                continue;
            }

            $maxId = DB::table($qualifiedTable)->max('id');
            $value = $maxId === null ? 1 : (int) $maxId;

            DB::select(
                'SELECT setval(CAST(? AS regclass), ?, ?)',
                [$sequence, $value, $maxId !== null]
            );
        }

        $this->command?->info('Sequence PostgreSQL berhasil disinkronkan.');
    }
}
