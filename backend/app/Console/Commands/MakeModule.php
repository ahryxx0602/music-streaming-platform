<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeModule extends Command
{
    protected $signature = 'make:module {name : The name of the module}';
    protected $description = 'Create a new Domain Module structure (Modular Monolith)';

    public function handle()
    {
        $name = $this->argument('name');
        $modulePath = app_path("Modules/{$name}");

        if (File::exists($modulePath)) {
            $this->error("Module {$name} already exists!");
            return;
        }

        $directories = [
            'Controllers',
            'Requests',
            'Services',
            'Repositories',
            'Events',
            'Listeners',
            'Jobs',
            'Models',
            'Policies'
        ];

        foreach ($directories as $dir) {
            File::makeDirectory("{$modulePath}/{$dir}", 0755, true);
        }

        $this->info("Module {$name} created successfully with full Domain-Driven structure.");
    }
}
