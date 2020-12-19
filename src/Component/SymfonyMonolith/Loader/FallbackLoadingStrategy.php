<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Loader;

use Acme\Component\SymfonyMonolith\ApplicationRegistry;

final class FallbackLoadingStrategy implements LoadingStrategy
{
    /**
     * @var string
     */
    private $default;

    public function __construct(string $default)
    {
        $this->default = $default;
    }

    public function getApplication(ApplicationRegistry $registry): ?string
    {
        return $this->default;
    }
}
