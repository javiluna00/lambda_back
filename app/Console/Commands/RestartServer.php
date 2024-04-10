<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RestartServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Restarting server...');

        exec('php artisan down');
        sleep(1); // Espera 1 segundo
        shell_exec('start /B php artisan migrate:fresh');
        sleep(1); // Espera 1 segundo
        shell_exec('start /B php artisan db:seed');
        sleep(1); // Espera 1 segundo
        shell_exec('start /B php artisan serve');
        sleep(1); // Espera 1 segundo
        exec('php artisan up');

        $this->info('Server restarted successfully.');
    }
}
