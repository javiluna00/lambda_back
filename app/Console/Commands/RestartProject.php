<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;

class RestartProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:restart-project';

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
        $this->output->progressStart(6);
        // migracion
        Artisan::call('migrate:fresh', [
            '--force' => true,
        ]);
        $this->info(" 🎯 Migraciones ejecutadas!");
        $this->output->progressAdvance();
        // Seeder
        Artisan::call('db:seed', [
            '--force' => true,
        ]);
        $this->info(" 🧩 Seeders ejecutados!");
        $this->output->progressAdvance();


        // key
        Artisan::call('key:generate', [
            '--force' => true,
        ]);
        $this->info(" 🔑 Api key generada!");
        $this->output->progressAdvance();

        // optimize
        Artisan::call('optimize');
        $this->output->progressAdvance();
        $this->info(" 📦 Proyecto optimizado!");

        // Borrar
        $file = new Filesystem;
        $file->cleanDirectory('storage/app');
        $this->output->progressAdvance();
        $this->info(" ✨ Storage vacio!");

        // disks
        Artisan::call('storage:link');
        $this->output->progressAdvance();
        $this->info(" 💿 Discos linkeados!");

        $this->output->progressFinish();
        $this->info("Proyecto restaurado 🎉🎉🎉");
    }
}
