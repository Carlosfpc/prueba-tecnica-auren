<?php

namespace App\Console\Commands;

use App\Jobs\SyncCountriesJob;
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
    public function handle(): int
    {
        $this->info('Dispatching country synchronization job to the queue...');

        SyncCountriesJob::dispatch();

        $this->info('Job dispatched successfully! The synchronization will run in the background.');
        $this->comment('Run "php artisan queue:work" to process the queue.');

        return Command::SUCCESS;
    }
}
