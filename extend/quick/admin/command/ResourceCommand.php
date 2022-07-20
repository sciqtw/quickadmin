<?php


declare (strict_types=1);

namespace quick\admin\command;

use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\helper\Str;

/**
 * Class ResourceCommand
 * @package quick\admin\command
 */
class ResourceCommand extends Command
{
    protected $type = "resource";


    protected function configure()
    {
        $this->setName('quick:resource')
            ->addArgument('name', Argument::REQUIRED, "The name of the class")
            ->addOption('model', "-m", Option::VALUE_OPTIONAL)
            ->setDescription('Create a new resource class');
    }


    protected function getStub(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'Resource.stub';
    }

    protected function execute(Input $input, Output $output)
    {
        $name = trim($input->getArgument('name'));

        $classname = $this->getClassName($name);

        $pathname = $this->getPathName($classname);

        $model = $input->getOption('model');


        if (is_file($pathname)) {
            $output->writeln('<error>' . $this->type . ':' . $classname . ' already exists!</error>');
            return false;
        }

        if (!is_dir(dirname($pathname))) {
            mkdir(dirname($pathname), 0755, true);
        }

        file_put_contents($pathname, $this->buildClass($classname, $model));

        $output->writeln('<info>' . $this->type . ':' . $classname . ' created successfully.</info>');
    }


    /**
     * @param string $name
     * @param $model
     * @return string|string[]
     */
    protected function buildClass(string $name, $model)
    {
        $stub = file_get_contents($this->getStub());

        $namespace = trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');

        $class = str_replace($namespace . '\\', '', $name);

        if (is_null($model)) {
            $model = $this->app->getNamespace() . "\\model\\" . $class;
        } elseif (!Str::startsWith($model, [$this->app->getNamespace(), '\\',])) {
            $model = $this->app->getNamespace() . "\\" . $model;
        }


        return str_replace(['{%className%}', '{%actionSuffix%}', '{%namespace%}', '{%app_namespace%}', '{%modelClass%}'], [
            $class,
            $this->app->config->get('route.action_suffix'),
            $namespace,
            $this->app->getNamespace(),
            $model
        ], $stub);
    }


    protected function getNamespace(string $app): string
    {
        return 'app' . ($app ? '\\' . $app : '') . "\\admin\\quick";
    }


    protected function getPathName(string $name): string
    {
        $name = str_replace('app\\', '', $name);

        return $this->app->getBasePath() . ltrim(str_replace('\\', '/', $name), '/') . '.php';
    }

    protected function getClassName(string $name): string
    {
        if (strpos($name, '\\') !== false) {
            return $name;
        }

        if (strpos($name, '@')) {
            [$app, $name] = explode('@', $name);
        } else {
            $app = '';
        }

        if (strpos($name, '/') !== false) {
            $name = str_replace('/', '\\', $name);
        }

        return $this->getNamespace($app) . '\\' . $name;
    }
}
