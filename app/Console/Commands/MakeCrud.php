<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeCrud extends Command
{
    protected $signature = 'make:crud {name}';

    protected $description = 'Crea la estructura completa de un CRUD';

    public function handle()
    {
        $name = $this->argument('name');

        $model = Str::studly($name);
        $singular = Str::camel($name);
        $plural = Str::plural(Str::snake($name));

        $this->info("Creando CRUD para {$model}...");

        /*
        |--------------------------------------------------------------------------
        | Model + Migration + Factory
        |--------------------------------------------------------------------------
        */

        Artisan::call('make:model', [
            'name' => $model,
            '-m' => true,
            '-f' => true,
        ]);

        $this->output->write(Artisan::output());

        /*
        |--------------------------------------------------------------------------
        | Controller
        |--------------------------------------------------------------------------
        */

        Artisan::call('make:controller', [
            'name' => "{$model}Controller",
            '--resource' => true,
        ]);

        $this->output->write(Artisan::output());

        /*
        |--------------------------------------------------------------------------
        | Requests
        |--------------------------------------------------------------------------
        */

        Artisan::call('make:request', [
            'name' => "Store{$model}Request",
        ]);

        Artisan::call('make:request', [
            'name' => "Update{$model}Request",
        ]);

        /*
        |--------------------------------------------------------------------------
        | Repository
        |--------------------------------------------------------------------------
        */

        $repositoryPath = app_path("Repositories/{$model}Repository.php");
        $interfacePath = app_path("Repositories/Interfaces/{$model}RepositoryInterface.php");

        File::ensureDirectoryExists(dirname($repositoryPath));
        File::ensureDirectoryExists(dirname($interfacePath));

        File::put($interfacePath, $this->repositoryInterface($model));

        File::put($repositoryPath, $this->repository($model));

        /*
        |--------------------------------------------------------------------------
        | Service
        |--------------------------------------------------------------------------
        */

        $servicePath = app_path("Services/{$model}Service.php");

        File::ensureDirectoryExists(dirname($servicePath));

        File::put($servicePath, $this->service($model));

        /*
        |--------------------------------------------------------------------------
        | Views
        |--------------------------------------------------------------------------
        */

        $viewsPath = resource_path("views/{$plural}");

        File::ensureDirectoryExists($viewsPath);

        File::put(
            "{$viewsPath}/index.blade.php",
            $this->indexView($model)
        );

        File::put(
            "{$viewsPath}/create.blade.php",
            $this->createView($model)
        );

        File::put(
            "{$viewsPath}/edit.blade.php",
            $this->editView($model)
        );

        File::put(
            "{$viewsPath}/show.blade.php",
            $this->showView($model)
        );

        /*
        |--------------------------------------------------------------------------
        | Resultado
        |--------------------------------------------------------------------------
        */

        $this->newLine();

        $this->info("CRUD de {$model} creado correctamente.");

        $this->newLine();

        $this->line("Modelo:");
        $this->line("app/Models/{$model}.php");

        $this->line("Controller:");
        $this->line("app/Http/Controllers/{$model}Controller.php");

        $this->line("Requests:");
        $this->line("app/Http/Requests/Store{$model}Request.php");
        $this->line("app/Http/Requests/Update{$model}Request.php");

        $this->line("Repository:");
        $this->line("app/Repositories/{$model}Repository.php");

        $this->line("Interface:");
        $this->line("app/Repositories/Interfaces/{$model}RepositoryInterface.php");

        $this->line("Service:");
        $this->line("app/Services/{$model}Service.php");

        $this->line("Views:");
        $this->line("resources/views/{$plural}");

        return Command::SUCCESS;
    }

    private function repositoryInterface($model)
    {
        return <<<PHP
<?php

namespace App\Repositories\Interfaces;

interface {$model}RepositoryInterface
{
    public function all();

    public function find(\$id);

    public function create(array \$data);

    public function update(\$id, array \$data);

    public function delete(\$id);
}
PHP;
    }

    private function repository($model)
    {
        return <<<PHP
<?php

namespace App\Repositories;

use App\Models\\{$model};
use App\Repositories\Interfaces\\{$model}RepositoryInterface;

class {$model}Repository implements {$model}RepositoryInterface
{
    public function all()
    {
        return {$model}::all();
    }

    public function find(\$id)
    {
        return {$model}::findOrFail(\$id);
    }

    public function create(array \$data)
    {
        return {$model}::create(\$data);
    }

    public function update(\$id, array \$data)
    {
        \$registro = {$model}::findOrFail(\$id);

        \$registro->update(\$data);

        return \$registro;
    }

    public function delete(\$id)
    {
        \$registro = {$model}::findOrFail(\$id);

        return \$registro->delete();
    }
}
PHP;
    }

    private function service($model)
    {
        return <<<PHP
<?php

namespace App\Services;

use App\Repositories\Interfaces\\{$model}RepositoryInterface;

class {$model}Service
{
    public function __construct(
        protected {$model}RepositoryInterface \$repository
    ) {}

    public function listar()
    {
        return \$this->repository->all();
    }

    public function buscar(\$id)
    {
        return \$this->repository->find(\$id);
    }

    public function crear(array \$data)
    {
        return \$this->repository->create(\$data);
    }

    public function actualizar(\$id, array \$data)
    {
        return \$this->repository->update(\$id, \$data);
    }

    public function eliminar(\$id)
    {
        return \$this->repository->delete(\$id);
    }
}
PHP;
    }

    private function indexView($model)
    {
        return <<<BLADE
<!DOCTYPE html>
<html>
<head>
    <title>{$model}</title>
</head>
<body>

<h1>{$model}</h1>

<a href="{{ route('{$this->routeName($model)}.create') }}">
    Crear
</a>

</body>
</html>
BLADE;
    }

    private function createView($model)
    {
        return <<<BLADE
<!DOCTYPE html>
<html>
<head>
    <title>Crear {$model}</title>
</head>
<body>

<h1>Crear {$model}</h1>

<form action="{{ route('{$this->routeName($model)}.store') }}" method="POST">
    @csrf

    <button type="submit">
        Guardar
    </button>
</form>

</body>
</html>
BLADE;
    }

    private function editView($model)
    {
        return <<<BLADE
<!DOCTYPE html>
<html>
<head>
    <title>Editar {$model}</title>
</head>
<body>

<h1>Editar {$model}</h1>

</body>
</html>
BLADE;
    }

    private function showView($model)
    {
        return <<<BLADE
<!DOCTYPE html>
<html>
<head>
    <title>Ver {$model}</title>
</head>
<body>

<h1>Ver {$model}</h1>

</body>
</html>
BLADE;
    }

    private function routeName($model)
    {
        return Str::plural(Str::snake($model));
    }
}