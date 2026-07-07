<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRepository extends Command
{
    protected $signature = 'make:repository {name : The name of the repository class} {--module= : The module to create the repository in}';
    protected $description = 'Create a new Repository class and Interface';

    public function handle()
    {
        $name = $this->argument('name');
        $module = $this->option('module');
        
        $namespace = 'App\Repositories';
        $path = app_path("Repositories/{$name}.php");
        $interfacePath = app_path("Repositories/{$name}Interface.php");

        if ($module) {
            $namespace = "App\Modules\\{$module}\Repositories";
            $path = app_path("Modules/{$module}/Repositories/{$name}.php");
            $interfacePath = app_path("Modules/{$module}/Repositories/{$name}Interface.php");
            
            if (!File::exists(app_path("Modules/{$module}/Repositories"))) {
                File::makeDirectory(app_path("Modules/{$module}/Repositories"), 0755, true);
            }
        } elseif (!File::exists(app_path("Repositories"))) {
            File::makeDirectory(app_path("Repositories"), 0755, true);
        }

        if (File::exists($path)) {
            $this->error("Repository {$name} already exists!");
            return;
        }

        $interfaceStub = "<?php\n\nnamespace {$namespace};\n\ninterface {$name}Interface\n{\n    //\n}\n";
        $classStub = "<?php\n\nnamespace {$namespace};\n\nclass {$name} implements {$name}Interface\n{\n    public function __construct()\n    {\n        //\n    }\n}\n";

        File::put($interfacePath, $interfaceStub);
        File::put($path, $classStub);

        $this->info("Repository {$name} and Interface created successfully.");
    }
}
