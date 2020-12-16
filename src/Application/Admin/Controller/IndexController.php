<?php

declare(strict_types=1);

namespace Acme\Application\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class IndexController extends AbstractController
{
    public function index(): Response
    {
        return new Response('Admin environment');
    }
}
