<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Config;

use Acme\Component\SymfonyMonolith\Config\Definition as ConfigurationDefinition;
use Acme\Component\SymfonyMonolith\Exception\InvalidConfig;
use Acme\Component\SymfonyMonolith\Exception\UnreadableConfigFile;
use Acme\Component\SymfonyMonolith\Factory\ConstructorFactory;
use Acme\Component\SymfonyMonolith\Loader\Application;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

final class Factory
{
    public static function createFromFile(string $file): Configuration
    {
        $config = self::getConfigurationFromFile($file);
        $config = self::processConfiguration($config);

        return new Configuration(self::getApplicationsFromConfig($config));
    }

    /**
     * @return array<mixed>
     */
    private static function getConfigurationFromFile(string $file): array
    {
        $contents = file_get_contents($file);
        if (false === $contents) {
            throw UnreadableConfigFile::create($file);
        }

        /** @var mixed $config */
        $config = Yaml::parse($contents);
        if (is_array($config)) {
            return $config;
        }

        throw InvalidConfig::invalidConfigFile($file, 'Expected contents to be an array');
    }

    /**
     * @param array<mixed> $configuration
     *
     * @return array<mixed>
     */
    private static function processConfiguration(array $configuration): array
    {
        $processor = new Processor();
        $definition = new ConfigurationDefinition();

        $processed = $processor->processConfiguration(
            $definition,
            [$configuration]
        );

        return EnvironmentVariableConfigPass::process($processed);
    }

    /**
     * @param array<mixed> $config
     *
     * @return array<Application>
     */
    private static function getApplicationsFromConfig(array $config): array
    {
        $applications = [];

        /** @psalm-var array{name: string, kernel: array{class: class-string<KernelInterface>, arguments: array<mixed>}} $application */
        foreach ($config['applications'] as $application) {
            $name = $application['name'];
            /** @var class-string<KernelInterface> $kernelClass */
            $kernelClass = $application['kernel']['class'];
            $kernelArguments = $application['kernel']['arguments'];

            $applications[] = new Application($name, new ConstructorFactory($kernelClass, ...$kernelArguments));
        }

        return $applications;
    }
}
