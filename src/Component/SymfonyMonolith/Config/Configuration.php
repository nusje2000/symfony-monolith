<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Config;

use Acme\Component\SymfonyMonolith\Loader\Application;

final class Configuration
{
    /**
     * @var array<Application>
     */
    private $applications;

    /**
     * @param array<Application> $applications
     */
    public function __construct(array $applications)
    {
        $this->applications = $applications;
    }

    /**
     * @return array<Application>
     */
    public function applications(): array
    {
        return $this->applications;
    }
}
