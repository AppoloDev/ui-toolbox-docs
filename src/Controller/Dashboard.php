<?php

namespace AppoloDev\UIToolboxDocBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/', name: 'dashboard', methods: ['GET'])]
class Dashboard extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render('@UIToolboxDoc/index.html.twig');
    }
}
