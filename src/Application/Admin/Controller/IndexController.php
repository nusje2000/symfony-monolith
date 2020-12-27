<?php

declare(strict_types=1);

namespace Acme\Application\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class IndexController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('index.html.twig', [
            'project_dir' => dirname(__DIR__),
        ]);
    }
}
