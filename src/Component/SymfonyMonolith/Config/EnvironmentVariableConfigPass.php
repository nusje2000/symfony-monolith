<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Config;

use Acme\Component\SymfonyMonolith\Exception\InvalidConfig;

final class EnvironmentVariableConfigPass
{
    public const ENV_PATTERN = '/%env\(?((?<type>[a-z]+):)(?<env_name>[a-zA-Z_]+)\)%/';

    /**
     * @param array<mixed> $config
     *
     * @return array<mixed>
     */
    public static function process(array $config): array
    {
        /** @var mixed $item */
        foreach ($config as &$item) {
            if (is_array($item)) {
                $item = self::process($item);
            }

            if (is_string($item)) {
                $item = self::processString($item);
            }
        }

        return $config;
    }

    /**
     * @return bool|string|float|int
     */
    private static function processString(string $string)
    {
        /** @psalm-var array{type: string, env_name: string, 0: string} $match */
        while (0 !== preg_match(self::ENV_PATTERN, $string, $match)) {
            $type = $match['type'] ?? 'string';
            $envName = $match['env_name'];
            /** @psalm-var scalar $env */
            $env = $_ENV[$envName];

            if ('string' !== $type && strlen($string) !== strlen($match[0])) {
                throw InvalidConfig::invalidString($string, 'Using a non-string environment variable inside of a string is not supported');
            }

            if ('string' !== $type) {
                return self::castType($type, $env);
            }

            $string = str_replace($match[0], (string) $env, $string);
        }

        return $string;
    }

    /**
     * @param bool|string|float|int $value
     *
     * @return bool|string|float|int
     */
    private static function castType(string $type, $value)
    {
        switch ($type) {
            case 'string':
                return $value;
            case 'int':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'bool':
                return (bool) $value;
            default:
                throw new InvalidConfig(sprintf('Invalid type "%s".', $type));
        }
    }
}
