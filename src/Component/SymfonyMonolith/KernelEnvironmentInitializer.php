<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith;

final class KernelEnvironmentInitializer
{
    public const PROJECT_DIR_ENVIRONMENT = 'SYMFONY_PROJECT_DIR';
    public const CACHE_DIR_ENVIRONMENT = 'SYMFONY_CACHE_DIR';
    public const LOG_DIR_ENVIRONMENT = 'SYMFONY_LOG_DIR';
    private const RELATIVE_CACHE_PATH = '/var/cache/{application}/{environment}';
    private const RELATIVE_LOG_PATH = '/var/log/{application}/{environment}';
    private const DIRECTORY_SEPARATORS = DIRECTORY_SEPARATOR . '/';

    /**
     * @var ApplicationLoader
     */
    private $applicationLoader;

    public function __construct(ApplicationLoader $applicationLoader)
    {
        $this->applicationLoader = $applicationLoader;
    }

    public function initialize(string $projectDir): void
    {
        $application = $this->applicationLoader->getLoadedApplication();
        $environment = $this->applicationLoader->getLoadedKernel()->getEnvironment();

        $_SERVER[self::PROJECT_DIR_ENVIRONMENT] = rtrim($projectDir, self::DIRECTORY_SEPARATORS);
        $_SERVER[self::CACHE_DIR_ENVIRONMENT] = $this->preparePath($projectDir, self::RELATIVE_CACHE_PATH, $application, $environment);
        $_SERVER[self::LOG_DIR_ENVIRONMENT] = $this->preparePath($projectDir, self::RELATIVE_LOG_PATH, $application, $environment);
    }

    private function preparePath(string $root, string $path, string $application, string $environment): string
    {
        $path = str_replace(['{application}', '{environment}'], [$application, $environment], $path);

        return rtrim($root, self::DIRECTORY_SEPARATORS) . DIRECTORY_SEPARATOR . trim($path, self::DIRECTORY_SEPARATORS);
    }
}
