<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Loader;

use Acme\Component\SymfonyMonolith\Factory\KernelFactory;
use Symfony\Component\HttpKernel\KernelInterface;

final class Application
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var KernelFactory
     */
    private $kernelFactory;

    /**
     * @var KernelInterface|null
     */
    private $kernel;

    public function __construct(string $name, KernelFactory $kernelFactory)
    {
        $this->name = $name;
        $this->kernelFactory = $kernelFactory;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function kernel(): KernelInterface
    {
        if (null === $this->kernel) {
            $this->kernel = $this->kernelFactory->create();
        }

        return $this->kernel;
    }
}
