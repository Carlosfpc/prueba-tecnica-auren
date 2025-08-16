<?php

namespace App\Console\Commands;

use App\Actions\SyncCountriesAction;
use Illuminate\Console\Command;

class SyncCountriesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'countries:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches countries from the API and upserts them into the database';

    /**
     * Execute the console command.
     */
    public function handle(SyncCountriesAction $syncAction): int // <-- 4. Inject the Action here.
    {
        $this->info('Starting country synchronization...');

        $summary = $syncAction->execute();

        $this->info('Synchronization Complete!');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Fetched', $summary['total_fetched']],
                ['Upserted Records', $summary['upserted']],
                ['Status', $summary['status']],
            ]
        );

        return Command::SUCCESS;
    }
}
