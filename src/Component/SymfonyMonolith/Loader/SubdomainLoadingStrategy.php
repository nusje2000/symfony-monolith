<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Loader;

use Acme\Component\SymfonyMonolith\ApplicationRegistry;
use Symfony\Component\HttpFoundation\Request;

final class SubdomainLoadingStrategy implements LoadingStrategy
{
    public function getApplication(ApplicationRegistry $registry): ?string
    {
        $request = Request::createFromGlobals();
        $host = $request->getHost();

        return $this->extractApplicationName($registry, $host);
    }

    private function extractApplicationName(ApplicationRegistry $registry, string $host): ?string
    {
        $applications = $registry->getApplicationNames();
        foreach ($applications as $application) {
            if (0 === stripos($host, $application)) {
                return $application;
            }
        }

        return null;
    }
}
