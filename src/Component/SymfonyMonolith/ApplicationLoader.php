<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith;

use Acme\Component\SymfonyMonolith\Exception\NoApplicationLoaded;
use Acme\Component\SymfonyMonolith\Exception\NoLoadableApplicationFound;
use Acme\Component\SymfonyMonolith\Loader\LoadingStrategy;
use Symfony\Component\HttpKernel\KernelInterface;

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
     * @var string|null
     */
    private $loadedApplication;

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

    public function getLoadedApplication(): string
    {
        if (null === $this->loadedApplication) {
            throw NoApplicationLoaded::create();
        }

        return $this->loadedApplication;
    }

    public function getLoadedKernel(): KernelInterface
    {
        return $this->registry->getKernel($this->loadedApplication);
    }
}