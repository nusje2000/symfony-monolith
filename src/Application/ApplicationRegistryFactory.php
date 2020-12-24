<?php

declare(strict_types=1);

namespace Acme\Application;

use Acme\Application\Admin\Kernel as AdminKernel;
use Acme\Application\Api\Kernel as ApiKernel;
use Acme\Application\Client\Kernel as ClientKernel;
use Acme\Component\SymfonyMonolith\ApplicationRegistry;
use Acme\Component\SymfonyMonolith\Factory\ConstructorFactory;

final class ApplicationRegistryFactory
{
    public static function create(string $environment, bool $debug): ApplicationRegistry
    {
        $registry = new ApplicationRegistry();
        $registry->register('admin', new ConstructorFactory(AdminKernel::class, $environment, $debug));
        $registry->register('client', new ConstructorFactory(ClientKernel::class, $environment, $debug));
        $registry->register('api', new ConstructorFactory(ApiKernel::class, $environment, $debug));

        return $registry;
    }
}
