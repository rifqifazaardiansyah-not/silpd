<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetPostgresSequences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:reset-sequences';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset PostgreSQL sequences to match max ID in tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (DB::getDriverName() !== 'pgsql') {
            $this->error('This command only works with PostgreSQL database.');
            return 1;
        }

        $this->info('Resetting PostgreSQL sequences...');

        // Get all tables in public schema
        $tables = DB::select("
            SELECT tablename 
            FROM pg_tables 
            WHERE schemaname = 'public'
            ORDER BY tablename
        ");

        $resetCount = 0;

        foreach ($tables as $table) {
            $tableName = $table->tablename;

            // Find columns with sequences (auto-increment)
            $sequences = DB::select("
                SELECT 
                    column_name,
                    pg_get_serial_sequence(?, column_name) as sequence_name
                FROM information_schema.columns
                WHERE table_name = ?
                AND table_schema = 'public'
                AND column_default LIKE 'nextval%'
            ", [$tableName, $tableName]);

            foreach ($sequences as $seq) {
                if ($seq->sequence_name) {
                    // Get max value from table
                    $maxId = DB::table($tableName)->max($seq->column_name) ?? 0;
                    $nextVal = $maxId + 1;

                    // Reset sequence
                    DB::statement("SELECT setval(?, ?, false)", [$seq->sequence_name, $nextVal]);

                    $this->line("✓ {$tableName}.{$seq->column_name} → sequence reset to {$nextVal}");
                    $resetCount++;
                }
            }
        }

        $this->newLine();
        $this->info("✓ Successfully reset {$resetCount} sequences!");

        return 0;
    }
}
