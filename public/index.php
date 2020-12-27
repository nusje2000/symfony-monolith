<?php

declare(strict_types=1);

use Acme\Application\ApplicationRegistryFactory;
use Acme\Component\SymfonyMonolith\ApplicationLoader;
use Acme\Component\SymfonyMonolith\Asset\AssetRouter;
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

$registry = ApplicationRegistryFactory::create($environment, $debug);

$loader = new ApplicationLoader($registry);
$loader->load();

(new KernelEnvironmentInitializer($loader))->initialize(dirname(__DIR__));

$kernel = $loader->getLoadedKernel();
$request = Request::createFromGlobals();

// First search if requested path is an asset, then let the kernel handle the request
$response = AssetRouter::create()->handle($kernel, $request) ?? $kernel->handle($request);
$response->send();

if ($kernel instanceof TerminableInterface) {
    $kernel->terminate($request, $response);
}
