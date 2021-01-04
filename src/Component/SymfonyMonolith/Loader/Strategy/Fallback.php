<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Loader\Strategy;

use Acme\Component\SymfonyMonolith\Loader\Application;
use Acme\Component\SymfonyMonolith\Loader\ApplicationRegistry;

final class Fallback implements LoadingStrategy
{
    /**
     * @var string
     */
    private $default;

    public function __construct(string $default)
    {
        $this->default = $default;
    }

    public function getApplication(ApplicationRegistry $registry): ?Application
    {
        return $registry->getApplication($this->default);
    }
}
