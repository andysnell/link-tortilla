<?php

declare(strict_types=1);

namespace PhoneBurner\LinkTortilla;

use Psr\Link\LinkInterface;

trait LinkWrapper
{
    private LinkInterface $wrapped;

    protected function setWrapped(LinkInterface $link): void
    {
        $this->wrapped = $link;
    }

    public function getHref(): string
    {
        return $this->wrapped->getHref();
    }

    public function isTemplated(): bool
    {
        return $this->wrapped->isTemplated();
    }

    public function getRels(): array
    {
        return $this->wrapped->getRels();
    }

    public function getAttributes(): array
    {
        return $this->wrapped->getAttributes();
    }
}
