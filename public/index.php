<?php

declare(strict_types=1);

use Acme\Application\Admin\Kernel as AdminKernel;
use Acme\Application\Api\Kernel as ApiKernel;
use Acme\Application\Client\Kernel as ClientKernel;
use Acme\Component\SymfonyMonolith\ApplicationLoader;
use Acme\Component\SymfonyMonolith\ApplicationRegistry;
use Acme\Component\SymfonyMonolith\Factory\ConstructorFactory;
use Acme\Component\SymfonyMonolith\KernelEnvironmentInitializer;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\TerminableInterface;

require dirname(__DIR__) . '/vendor/autoload.php';

(new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

$debug = (bool) $_SERVER['APP_DEBUG'];
$environment = (string) $_SERVER['APP_ENV'];

$registry = new ApplicationRegistry();
$registry->register('admin', new ConstructorFactory(AdminKernel::class, $environment, $debug));
$registry->register('client', new ConstructorFactory(ClientKernel::class, $environment, $debug));
$registry->register('api', new ConstructorFactory(ApiKernel::class, $environment, $debug));

$loader = new ApplicationLoader($registry);
$loader->load();

(new KernelEnvironmentInitializer($loader))->initialize(dirname(__DIR__));

$kernel = $loader->getLoadedKernel();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();

if ($kernel instanceof TerminableInterface) {
    $kernel->terminate($request, $response);
}
