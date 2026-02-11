<?php

namespace Modules\Outlet\Console\Commands;

use Illuminate\Console\Command;

class OutletCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'outlet:list {--active : Show only active outlets}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all outlets in the system';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Outlet Module Command');
        $this->line('---------------------');

        if ($this->option('active')) {
            $this->info('Showing active outlets only...');
        } else {
            $this->info('Showing all outlets...');
        }

        // TODO: Add logic to fetch and display outlets from database
        $this->warn('No outlets found. Database integration pending.');

        return Command::SUCCESS;
    }
}
