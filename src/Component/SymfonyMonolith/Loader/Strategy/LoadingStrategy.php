<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Loader\Strategy;

use Acme\Component\SymfonyMonolith\Loader\Application;
use Acme\Component\SymfonyMonolith\Loader\ApplicationRegistry;

interface LoadingStrategy
{
    public function getApplication(ApplicationRegistry $registry): ?Application;
}
