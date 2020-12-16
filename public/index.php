<?php

use Acme\Application\Admin\Kernel as AdminKernel;
use Acme\Application\Api\Kernel as ApiKernel;
use Acme\Application\Client\Kernel as ClientKernel;
use Acme\Component\SymfonyMonolith\ApplicationLoader;
use Acme\Component\SymfonyMonolith\ApplicationRegistry;
use Acme\Component\SymfonyMonolith\Factory\ConstructorFactory;
use Acme\Component\SymfonyMonolith\Loader\ArgvLoadingStrategy;
use Acme\Component\SymfonyMonolith\Loader\EnvironmentLoadingStrategy;
use Acme\Component\SymfonyMonolith\Loader\SubdomainLoadingStrategy;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__) . '/vendor/autoload.php';

(new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

$debug = (bool) $_SERVER['APP_DEBUG'];
$environment = $_SERVER['APP_ENV'];

$registry = new ApplicationRegistry();
$registry->register('admin', new ConstructorFactory(AdminKernel::class, $environment, $debug));
$registry->register('client', new ConstructorFactory(ClientKernel::class, $environment, $debug));
$registry->register('api', new ConstructorFactory(ApiKernel::class, $environment, $debug));

$loader = new ApplicationLoader($registry, [
    new EnvironmentLoadingStrategy(),
    new ArgvLoadingStrategy(),
    new SubdomainLoadingStrategy(),
]);
$loader->load();

$_SERVER['SYMFONY_CACHE_DIR'] = dirname(__DIR__) . '/var/cache/' . $loader->getLoadedApplication() . '/' . $environment;
$_SERVER['SYMFONY_LOG_DIR'] = dirname(__DIR__) . '/var/log/' . $loader->getLoadedApplication() . '/' . $environment;
$_SERVER['SYMFONY_PROJECT_DIR'] = dirname(__DIR__);

$kernel = $loader->getLoadedKernel();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
