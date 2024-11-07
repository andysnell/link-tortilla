<?php

declare(strict_types=1);

namespace PhoneBurner\LinkTortilla\Tests;

use Generator;
use GuzzleHttp\Psr7\Uri;
use PhoneBurner\LinkTortilla\Link;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;
use Psr\Link\EvolvableLinkInterface;
use Stringable;

class LinkTest extends TestCase
{
    #[Test]
    #[DataProvider('providesValidMakeInputs')]
    public function makeReturnsNewInstanceOfLink(
        string $rel,
        string|Stringable|UriInterface $href,
        string $expected,
    ): void {
        $link = Link::make($rel, $href);

        self::assertInstanceOf(EvolvableLinkInterface::class, $link);
        self::assertSame([$rel], $link->getRels());
        self::assertSame($expected, $link->getHref());
        self::assertSame([], $link->getAttributes());
        self::assertFalse($link->is_array);
    }

    #[Test]
    #[DataProvider('providesValidMakeInputs')]
    public function asArrayReturnsNewStaticInstanceOfLink(
        string $rel,
        string|Stringable|UriInterface $href,
        string $expected,
    ): void {
        $link = Link::make($rel, $href)->asArray();

        self::assertInstanceOf(EvolvableLinkInterface::class, $link);
        self::assertSame([$rel], $link->getRels());
        self::assertSame($expected, $link->getHref());
        self::assertSame([], $link->getAttributes());
        self::assertTrue($link->is_array);
    }

    public static function providesValidMakeInputs(): Generator
    {
        $args = [
            ["self", 'https://example.com'],
            ["self", 'https://example.com/foo/12345'],
            ["next", '/foo/12345'],
            ["self", 'https://example.com/orders/{order}'],
            ["order", '/orders/{order}'],
        ];

        $stringable = fn(string $href): Stringable => new class ($href) implements Stringable {
            public function __construct(private readonly string $href)
            {
            }

            public function __toString(): string
            {
                return $this->href;
            }
        };

        foreach ($args as [$rel, $href]) {
            yield [$rel, $href, $href];
            yield [$rel, $stringable($href), $href];
        }

        yield ["self", new Uri('https://example.com'), 'https://example.com'];
        yield ["self", new Uri('https://example.com/foo/12345'), 'https://example.com/foo/12345'];
        yield ["next", new Uri('/foo/12345'), '/foo/12345'];
    }

    #[Test]
    #[DataProvider('providesEmptyHrefArgument')]
    public function makeThrowsExceptionIfHrefIsEmpty(string|Stringable $href): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Link::make('foo', $href);
    }

    public static function providesEmptyHrefArgument(): Generator
    {
        yield [''];
        yield [
            new class () implements Stringable {
                public function __toString(): string
                {
                    return '';
                }
            },
        ];
    }

    #[Test]
    #[DataProvider('dataProviderWithHrefReturnsNewInstanceWithHref')]
    public function withHrefReturnsNewInstanceWithHref(string $original, string $new_href): void
    {
        $link = Link::make('self', $original);
        $evolved = $link->withHref($new_href);

        self::assertInstanceOf(EvolvableLinkInterface::class, $evolved);
        self::assertSame($new_href, $evolved->getHref());
        self::assertNotSame($link, $evolved);
    }

    public static function dataProviderWithHrefReturnsNewInstanceWithHref(): array
    {
        return [
            ["HTTPS://EXAMPLE.COM/FOO", "HTTPS://EXAMPLE.COM/BAR"],
            ["HTTPS://EXAMPLE.COM/FOO", "HTTPS://EXAMPLE.COM/FOO"],
        ];
    }

    #[Test]
    public function makeHandlesEmptyStringCaseForRelArgument(): void
    {
        self::assertSame([], Link::make('', 'https://example.com')->getRels());
    }

    #[Test]
    #[DataProvider('dataProviderIsTemplatedReturnsTrueIfLinkIsTemplated')]
    public function isTemplatedReturnsTrueIfLinkIsTemplated(bool $expected, string $href): void
    {
        self::assertSame($expected, Link::make('self', $href)->isTemplated());
    }

    public static function dataProviderIsTemplatedReturnsTrueIfLinkIsTemplated(): array
    {
        return [
            [false, "HTTPS://EXAMPLE.COM"],
            [false, "HTTPS://EXAMPLE.COM/FOO/12345"],
            [false, "/FOO/12345"],
            [true, "HTTPS://EXAMPLE.COM/ORDERS/{ORDER}"],
            [true, "/ORDERS/{ORDER}"],
            [true, "HTTPS://EXAMPLE.COM/FOO/12345{?PAGE}"],
        ];
    }

    #[Test]
    public function withAttributeReturnsNewInstanceWithAttribute(): void
    {
        $link = Link::make('self', 'https://example.com/foo');
        $evolved = $link->withAttribute('foo', 'bar');

        self::assertInstanceOf(EvolvableLinkInterface::class, $evolved);
        self::assertNotSame($link, $evolved);
        self::assertSame([], $link->getAttributes());
        self::assertSame(['foo' => 'bar'], $evolved->getAttributes());
        self::assertSame(['foo' => 'bar', 'baz' => 'qux'], $evolved->withAttribute('baz', 'qux')->getAttributes());
    }

    #[Test]
    public function withAttributeReturnsNewInstanceWithExistingAttributeOverwritten(): void
    {
        $link = Link::make('self', 'https://example.com/foo')->withAttribute('foo', 'bar');
        $evolved = $link->withAttribute('foo', 'foobar');

        self::assertInstanceOf(EvolvableLinkInterface::class, $evolved);
        self::assertNotSame($link, $evolved);
        self::assertSame('https://example.com/foo', $link->getHref());
        self::assertSame(['self'], $link->getRels());
        self::assertSame(['foo' => 'foobar'], $evolved->getAttributes());
    }

    #[Test]
    public function withoutAttributeReturnsNewInstanceWithoutAttribute(): void
    {
        $link = Link::make('self', 'https://example.com/foo')
            ->withAttribute('foo', 'bar')
            ->withAttribute('baz', 'qux');
        $evolved = $link->withoutAttribute('foo');

        self::assertInstanceOf(EvolvableLinkInterface::class, $evolved);
        self::assertNotSame($link, $evolved);
        self::assertSame('https://example.com/foo', $link->getHref());
        self::assertSame(['self'], $link->getRels());
        self::assertSame(['baz' => 'qux'], $evolved->getAttributes());
    }

    #[Test]
    public function withoutAttributeReturnsWithoutErrorIfAttributeDoesNotExist(): void
    {
        $link = Link::make('self', 'https://example.com/foo');
        $evolved = $link->withoutAttribute('does_not_exist');

        self::assertInstanceOf(EvolvableLinkInterface::class, $evolved);
        self::assertNotSame($link, $evolved);
        self::assertSame('https://example.com/foo', $link->getHref());
        self::assertSame(['self'], $link->getRels());
        self::assertSame([], $evolved->getAttributes());
    }

    #[Test]
    public function withRelReturnsNewInstanceWithRel(): void
    {
        $link = Link::make('self', 'https://example.com/foo');
        $evolved = $link->withRel('doc');

        self::assertInstanceOf(EvolvableLinkInterface::class, $evolved);
        self::assertNotSame($link, $evolved);
        self::assertSame(['self', 'doc'], $evolved->getRels());
    }

    #[Test]
    public function withRelReturnsWithoutErrorIfRelIsAlreadyDefined(): void
    {
        $link = Link::make('self', 'https://example.com/foo');
        $evolved = $link->withRel('self');

        self::assertInstanceOf(EvolvableLinkInterface::class, $evolved);
        self::assertNotSame($link, $evolved);
        self::assertSame(['self'], $evolved->getRels());
    }

    #[Test]
    public function withoutRelReturnsNewInstanceWithoutRel(): void
    {
        $link = Link::make('self', 'https://example.com/foo')->withRel('docs');
        $evolved = $link->withoutRel('self');

        self::assertInstanceOf(EvolvableLinkInterface::class, $evolved);
        self::assertNotSame($link, $evolved);
        self::assertSame(['docs'], $evolved->getRels());
        self::assertSame([], $evolved->withoutRel('docs')->getRels());
    }

    #[Test]
    public function withoutRelReturnsWithoutErrorIfRelDoesNotExist(): void
    {
        $link = Link::make('self', 'https://example.com/foo');
        $evolved = $link->withoutRel('does_not_exist');

        self::assertInstanceOf(EvolvableLinkInterface::class, $evolved);
        self::assertNotSame($link, $evolved);
        self::assertSame(['self'], $evolved->getRels());
    }
}
