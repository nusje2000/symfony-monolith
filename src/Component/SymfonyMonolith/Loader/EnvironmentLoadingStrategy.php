<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Loader;

use Acme\Component\SymfonyMonolith\ApplicationRegistry;

final class EnvironmentLoadingStrategy implements LoadingStrategy
{
    private const DEFAULT_ENVIRONMENT_NAME = 'SYMFONY_APPLICATION';

    /**
     * @var string
     */
    private $environmentName;

    public function __construct(?string $environmentName = null)
    {
        $this->environmentName = $environmentName ?? self::DEFAULT_ENVIRONMENT_NAME;
    }

    public function getApplication(ApplicationRegistry $registry): ?string
    {
        /** @var mixed $application */
        $application = $_SERVER[$this->environmentName] ?? null;

        if (is_string($application) && $registry->hasApplication($application)) {
            return $application;
        }

        return null;
    }
}
