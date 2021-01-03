<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith;

use Acme\Component\SymfonyMonolith\Loader\ApplicationLoader;

final class KernelEnvironmentInitializer
{
    public const CACHE_DIR_ENVIRONMENT = 'APP_CACHE_DIR';
    public const LOG_DIR_ENVIRONMENT = 'APP_LOG_DIR';
    private const RELATIVE_CACHE_PATH = '/var/cache/{application}';
    private const RELATIVE_LOG_PATH = '/var/log/{application}';
    private const DIRECTORY_SEPARATORS = DIRECTORY_SEPARATOR . '/';

    /**
     * @var ApplicationLoader
     */
    private $applicationLoader;

    public function __construct(ApplicationLoader $applicationLoader)
    {
        $this->applicationLoader = $applicationLoader;
    }

    public function initialize(string $rootDir): void
    {
        $application = $this->applicationLoader->getLoadedApplication()->name();

        $_SERVER[self::CACHE_DIR_ENVIRONMENT] = $this->preparePath($rootDir, self::RELATIVE_CACHE_PATH, $application);
        $_SERVER[self::LOG_DIR_ENVIRONMENT] = $this->preparePath($rootDir, self::RELATIVE_LOG_PATH, $application);
    }

    private function preparePath(string $root, string $path, string $application): string
    {
        $path = str_replace(['{application}'], [$application], $path);

        return rtrim($root, self::DIRECTORY_SEPARATORS) . DIRECTORY_SEPARATOR . trim($path, self::DIRECTORY_SEPARATORS);
    }
}
