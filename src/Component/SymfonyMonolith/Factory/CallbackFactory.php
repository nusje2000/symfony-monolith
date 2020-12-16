<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Factory;

use Acme\Component\SymfonyMonolith\Exception\InvalidKernelFactory;
use Symfony\Component\HttpKernel\KernelInterface;

final class CallbackFactory implements KernelFactory
{
    /**
     * @var callable
     */
    private $creationMethod;

    public function __construct(callable $creationMethod)
    {
        $this->creationMethod = $creationMethod;
    }

    public function create(): KernelInterface
    {
        $kernel = call_user_func($this->creationMethod);

        if (!$kernel instanceof KernelInterface) {
            throw InvalidKernelFactory::createdInvalidKernel($kernel);
        }

        return $kernel;
    }
}
