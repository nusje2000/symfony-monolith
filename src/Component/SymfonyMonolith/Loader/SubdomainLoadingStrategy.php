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
        // example: subdomain.admin.localhost
        $parts = explode('.', $host);

        // remove localhost
        array_pop($parts);

        // extract admin from subdomain.admin
        $application = end($parts);

        if (false !== $application && $registry->hasApplication($application)) {
            return $application;
        }

        return null;
    }
}
