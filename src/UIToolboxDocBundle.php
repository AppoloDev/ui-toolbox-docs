<?php

namespace AppoloDev\UIToolboxDocBundle;

use AppoloDev\UIToolboxDocBundle\DependencyInjection\UIToolboxDocExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class UIToolboxDocBundle extends AbstractBundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new UIToolboxDocExtension();
    }
}
