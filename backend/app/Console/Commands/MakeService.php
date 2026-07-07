<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeService extends Command
{
    protected $signature = 'make:service {name : The name of the service class} {--module= : The module to create the service in}';
    protected $description = 'Create a new Service class';

    public function handle()
    {
        $name = $this->argument('name');
        $module = $this->option('module');
        
        $namespace = 'App\Services';
        $path = app_path("Services/{$name}.php");

        if ($module) {
            $namespace = "App\Modules\\{$module}\Services";
            $path = app_path("Modules/{$module}/Services/{$name}.php");
            
            if (!File::exists(app_path("Modules/{$module}/Services"))) {
                File::makeDirectory(app_path("Modules/{$module}/Services"), 0755, true);
            }
        } elseif (!File::exists(app_path("Services"))) {
            File::makeDirectory(app_path("Services"), 0755, true);
        }

        if (File::exists($path)) {
            $this->error("Service {$name} already exists!");
            return;
        }

        $stub = "<?php\n\nnamespace {$namespace};\n\nclass {$name}\n{\n    public function __construct()\n    {\n        //\n    }\n}\n";

        File::put($path, $stub);

        $this->info("Service {$name} created successfully.");
    }
}
