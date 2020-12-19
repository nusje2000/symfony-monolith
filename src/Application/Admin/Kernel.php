<?php

declare(strict_types=1);

namespace Acme\Application\Admin;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        /** @var array<class-string<BundleInterface>, array<string, bool>> $contents */
        $contents = require __DIR__ . '/Resources/config/bundles.php';

        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('./Resources/config/{packages}/*.yaml');
        $container->import('./Resources/config/{packages}/' . $this->environment . '/*.yaml');

        if (is_file(__DIR__ . '/Resources/config/services.yaml')) {
            $container->import('./Resources/config/services.yaml');
            $container->import('./Resources/config/{services}_' . $this->environment . '.yaml');
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('./Resources/config/{routes}/' . $this->environment . '/*.yaml');
        $routes->import('./Resources/config/{routes}/*.yaml');

        if (is_file(__DIR__ . '/Resources/config/routes.yaml')) {
            $routes->import('./Resources/config/routes.yaml');
        }
    }

    public function getCacheDir(): string
    {
        return (string) ($_SERVER['SYMFONY_CACHE_DIR'] ?? parent::getCacheDir());
    }

    public function getLogDir(): string
    {
        return (string) ($_SERVER['SYMFONY_LOG_DIR'] ?? parent::getLogDir());
    }

    public function getProjectDir(): string
    {
        return (string) ($_SERVER['SYMFONY_PROJECT_DIR'] ?? parent::getProjectDir());
    }
}
