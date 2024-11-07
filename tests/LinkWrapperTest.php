<?php

declare(strict_types=1);

namespace PhoneBurner\LinkTortilla\Tests;

use PhoneBurner\LinkTortilla\Link;
use PhoneBurner\LinkTortilla\Tests\Fixtures\LinkWrapperFixture;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class LinkWrapperTest extends TestCase
{
    #[Test]
    public function linkWrapperWrapsLinkInterface(): void
    {
        $wrapped_link = Link::make('foo_rel', 'test_href_string')
            ->withAttribute('foo', 'bar')
            ->withAttribute('baz', 'quz');

        $link = new LinkWrapperFixture($wrapped_link);

        self::assertSame('test_href_string', $link->getHref());
        self::assertSame(['foo_rel'], $link->getRels());
        self::assertSame([
            'foo' => 'bar',
            'baz' => 'quz',
        ], $link->getAttributes());
        self::assertFalse($link->isTemplated());
    }

    #[Test]
    public function linkWrapperWrapsTemplatedLinkInterface(): void
    {
        $wrapped_link = Link::make('self', "https://example.com/orders/{order}");
        $link = new LinkWrapperFixture($wrapped_link);

        self::assertTrue($link->isTemplated());
    }
}
