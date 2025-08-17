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
    protected $signature = 'countries:sync {--now}';

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
        // Check if the --now option was passed.
        if ($this->option('now')) {
            $this->info('Starting country synchronization synchronously...');

            // Dispatch the job and process it immediately in the current process.
            SyncCountriesJob::dispatchSync();

            $this->info('Synchronization completed successfully!');
        } else {
            $this->info('Dispatching country synchronization job to the queue...');

            // Default behavior: dispatch to the queue.
            SyncCountriesJob::dispatch();

            $this->info('Job dispatched successfully! The synchronization will run in the background.');
            $this->comment('Reminder: Run "php artisan queue:work" to process the jobs.');
        }

        return Command::SUCCESS;
    }
}
