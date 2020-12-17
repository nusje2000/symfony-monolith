<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Loader;

use Acme\Component\SymfonyMonolith\ApplicationRegistry;
use Symfony\Component\Console\Input\ArgvInput;

final class ArgvLoadingStrategy implements LoadingStrategy
{
    public function getApplication(ApplicationRegistry $registry): ?string
    {
        $input = new ArgvInput();

        /** @var mixed $application */
        $application = $input->getParameterOption(['-a', '--application'], null);

        if (is_string($application) && $registry->hasApplication($application)) {
            return $application;
        }

        return null;
    }
}
