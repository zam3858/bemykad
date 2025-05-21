<?php

namespace BeMyKad\Symfony\BeMyKadBundle;

use BeMyKad\Symfony\BeMyKadBundle\DependencyInjection\BeMyKadExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class BeMyKadBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new BeMyKadExtension();
        }
        return $this->extension;
    }
}
