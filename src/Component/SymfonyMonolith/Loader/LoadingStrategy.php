<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Loader;

use Acme\Component\SymfonyMonolith\ApplicationRegistry;

interface LoadingStrategy
{
    public function getApplication(ApplicationRegistry $registry): ?string;
}
