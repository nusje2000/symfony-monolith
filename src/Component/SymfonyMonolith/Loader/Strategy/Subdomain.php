<?php

declare(strict_types=1);

namespace Acme\Component\SymfonyMonolith\Loader\Strategy;

use Acme\Component\SymfonyMonolith\Loader\Application;
use Acme\Component\SymfonyMonolith\Loader\ApplicationRegistry;
use Symfony\Component\HttpFoundation\Request;

final class Subdomain implements LoadingStrategy
{
    public function getApplication(ApplicationRegistry $registry): ?Application
    {
        $request = Request::createFromGlobals();
        $host = $request->getHost();

        $application = $this->extractApplicationName($registry, $host);
        if (null === $application) {
            return null;
        }

        return $registry->getApplication($application);
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
