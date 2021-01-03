<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Loader;

use Acme\Component\SymfonyMonolith\Exception\MissingApplication;

final class ApplicationRegistry
{
    /**
     * @var array<string, Application>
     */
    public $applications = [];

    public function register(Application ...$applications): void
    {
        foreach ($applications as $application) {
            $this->applications[$application->name()] = $application;
        }
    }

    /**
     * @return array<int, string>
     */
    public function getApplicationNames(): array
    {
        return array_keys($this->applications);
    }

    public function getApplication(string $name): Application
    {
        $application = $this->applications[$name] ?? null;
        if (null === $application) {
            throw MissingApplication::create($name);
        }

        return $application;
    }

    public function hasApplication(string $name): bool
    {
        return isset($this->applications[$name]);
    }
}
