<?php

namespace BeMyKad\Symfony\BeMyKadBundle;

use BeMyKad\Symfony\BeMyKadBundle\DependencyInjection\BeMyKadExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BeMyKadBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if ($this->extension === null) {
            $this->extension = new BeMyKadExtension;
        }

        return $this->extension;
    }
}
