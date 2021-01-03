<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Loader;

use Acme\Component\SymfonyMonolith\Exception\NoApplicationLoaded;
use Acme\Component\SymfonyMonolith\Exception\NoLoadableApplicationFound;
use Acme\Component\SymfonyMonolith\Loader\Strategy\LoadingStrategy;

final class ApplicationLoader
{
    /**
     * @var ApplicationRegistry
     */
    private $registry;

    /**
     * @var array<LoadingStrategy>
     */
    private $loadingStrategies;

    /**
     * @var Application|null
     */
    private $loadedApplication;

    /**
     * @param array<LoadingStrategy> $loadingStrategies
     */
    public function __construct(ApplicationRegistry $registry, array $loadingStrategies)
    {
        $this->registry = $registry;

        $this->loadingStrategies = $loadingStrategies;
    }

    public function load(): void
    {
        $this->loadedApplication = null;

        foreach ($this->loadingStrategies as $loadingStrategy) {
            $this->loadedApplication = $loadingStrategy->getApplication($this->registry);

            if (null !== $this->loadedApplication) {
                return;
            }
        }

        throw NoLoadableApplicationFound::create();
    }

    public function getLoadedApplication(): Application
    {
        if (null === $this->loadedApplication) {
            throw NoApplicationLoaded::create();
        }

        return $this->loadedApplication;
    }
}
