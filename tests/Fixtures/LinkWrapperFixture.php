<?php

declare(strict_types=1);

namespace PhoneBurner\Tests\LinkTortilla\Fixtures;

use PhoneBurner\LinkTortilla\LinkWrapper;
use Psr\Link\LinkInterface;

class LinkWrapperFixture implements LinkInterface
{
    use LinkWrapper;

    public function __construct(LinkInterface $link)
    {
        $this->setWrapped($link);
    }
}
