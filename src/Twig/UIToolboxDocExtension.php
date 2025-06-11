<?php

namespace AppoloDev\UIToolboxDocBundle\Twig;

use AppoloDev\UIToolboxDocBundle\Registry\ComponentDocumentationRegistry;
use Twig\Attribute\AsTwigFunction;

readonly class UIToolboxDocExtension
{
    public function __construct(
        private ComponentDocumentationRegistry $registry,
    ) {
    }

    #[AsTwigFunction(name: 'getUiToolboxComponents')]
    public function getUiToolboxComponents(): array
    {
        $grouped = [];

        foreach ($this->registry->all() as $component) {
            $grouped[$component['category']][] = $component;
        }

        \ksort($grouped);

        return $grouped;
    }
}
