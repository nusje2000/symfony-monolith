<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith;

use Acme\Component\SymfonyMonolith\Exception\MissingApplication;
use Acme\Component\SymfonyMonolith\Factory\KernelFactory;
use Symfony\Component\HttpKernel\KernelInterface;

final class ApplicationRegistry
{
    /**
     * @var array<string, KernelFactory>
     */
    public $applications = [];

    /**
     * @var array<string, KernelInterface>
     */
    public $kernels = [];

    public function register(string $name, KernelFactory $factory): void
    {
        $this->applications[$name] = $factory;
    }

    /**
     * @return array<int, string>
     */
    public function getApplicationNames(): array
    {
        return array_keys($this->applications);
    }

    public function hasApplication(string $name): bool
    {
        return isset($this->applications[$name]);
    }

    public function getKernel(string $application): KernelInterface
    {
        if (isset($this->kernels[$application])) {
            return $this->kernels[$application];
        }

        $factory = $this->applications[$application] ?? null;
        if (null === $factory) {
            throw MissingApplication::create($application);
        }

        return $factory->create();
    }
}
