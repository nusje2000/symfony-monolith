<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Loader\Strategy;

use Acme\Component\SymfonyMonolith\Loader\Application;
use Acme\Component\SymfonyMonolith\Loader\ApplicationRegistry;
use Symfony\Component\Console\Input\ArgvInput;

final class Argv implements LoadingStrategy
{
    public function getApplication(ApplicationRegistry $registry): ?Application
    {
        $input = new ArgvInput();

        /** @var mixed $application */
        $application = $input->getParameterOption(['-a', '--application'], null);

        if (is_string($application) && $registry->hasApplication($application)) {
            return $registry->getApplication($application);
        }

        return null;
    }
}
