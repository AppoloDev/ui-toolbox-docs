<?php

namespace AppoloDev\UIToolboxDocBundle\Controller;

use AppoloDev\UIToolboxDocBundle\Registry\ComponentDocumentationRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/component/{slug}', name: 'component')]
class ComponentShow extends AbstractController
{
    public function __invoke(string $slug, ComponentDocumentationRegistry $registry): Response
    {
        $component = null;
        foreach ($registry->all() as $item) {
            if (($item['slug'] ?? null) === $slug) {
                $component = $item;
                break;
            }
        }

        if (!$component) {
            throw $this->createNotFoundException('Component not found');
        }

        return $this->render('@UIToolboxDoc/show.html.twig', [
            'component' => $component,
        ]);
    }
}
