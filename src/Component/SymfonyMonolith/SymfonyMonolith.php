<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith;

use Acme\Component\SymfonyMonolith\Config\Factory;
use Acme\Component\SymfonyMonolith\Loader\ApplicationLoader;
use Acme\Component\SymfonyMonolith\Loader\ApplicationRegistry;
use Acme\Component\SymfonyMonolith\Loader\Strategy\Argv;
use Acme\Component\SymfonyMonolith\Loader\Strategy\Environment;
use Acme\Component\SymfonyMonolith\Loader\Strategy\LoadingStrategy;
use Acme\Component\SymfonyMonolith\Loader\Strategy\Subdomain;
use Symfony\Component\HttpKernel\KernelInterface;

final class SymfonyMonolith
{
    private const SAPI_CLI = 'cli';
    private const DEFAULT_CONFIG_NAME = 'symfony-monolith.yml';

    /**
     * @var ApplicationRegistry
     */
    private $registry;

    /**
     * @var ApplicationLoader
     */
    private $loader;

    /**
     * @var KernelEnvironmentInitializer
     */
    private $environmentInitializer;

    /**
     * @var string
     */
    private $rootDir;

    public function __construct(string $rootDir, ApplicationRegistry $registry, ApplicationLoader $applicationLoader)
    {
        $this->registry = $registry;
        $this->loader = $applicationLoader;
        $this->environmentInitializer = new KernelEnvironmentInitializer($this->loader);
        $this->rootDir = $rootDir;
    }

    public static function create(string $rootDir, ?string $configurationFile = null): self
    {
        $registry = new ApplicationRegistry();
        $config = Factory::createFromFile($configurationFile ?? $rootDir . '/' . self::DEFAULT_CONFIG_NAME);
        $registry->register(...$config->applications());

        return new self($rootDir, $registry, new ApplicationLoader($registry, self::getDefaultLoadingStrategies()));
    }

    public function registry(): ApplicationRegistry
    {
        return $this->registry;
    }

    public function initialize(): void
    {
        $this->loader->load();
        $this->environmentInitializer->initialize($this->rootDir);
    }

    public function applicationName(): string
    {
        return $this->loader->getLoadedApplication()->name();
    }

    public function kernel(): KernelInterface
    {
        return $this->loader->getLoadedApplication()->kernel();
    }

    /**
     * @return array<LoadingStrategy>
     */
    private static function getDefaultLoadingStrategies(): array
    {
        if (PHP_SAPI === self::SAPI_CLI) {
            return [
                new Argv(),
                new Environment(),
            ];
        }

        return [
            new Environment(),
            new Subdomain(),
        ];
    }
}
