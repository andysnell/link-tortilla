<?php

declare(strict_types=1);

namespace PhoneBurner\LinkTortilla;

use Psr\Link\EvolvableLinkInterface;
use Stringable;

class Link implements EvolvableLinkInterface
{
    final private function __construct(
        public readonly string $href,
        public readonly array $rels = [],
        public readonly array $attributes = [],
        public readonly bool $is_array = false,
    ) {
        $this->href ?: throw new \InvalidArgumentException('href cannot be empty string');
    }

    /**
     * The target $href string must be one of:
     * - An absolute URI as defined by RFC 5988
     * - A relative URI as defined by RFC 5988
     * - A URI template as defined by RFC 6570
     */
    public static function make(string|Stringable|null $rel, string|Stringable $href): self
    {
        return new self((string)$href, \array_fill_keys(\array_filter([(string)$rel]), true), []);
    }

    public function getHref(): string
    {
        return $this->href;
    }

    public function isTemplated(): bool
    {
        return \str_contains($this->href, '{') || \str_contains($this->href, '}');
    }

    public function getRels(): array
    {
        return \array_keys($this->rels);
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function withHref(Stringable|string $href): static
    {
        return new static((string)$href, $this->rels, $this->attributes);
    }

    public function withRel(string $rel): static
    {
        $rels = $this->rels;
        $rels[$rel] = true;

        return new static($this->href, $rels, $this->attributes);
    }

    public function withoutRel(string $rel): static
    {
        $rels = $this->rels;
        unset($rels[$rel]);

        return new static($this->href, $rels, $this->attributes);
    }

    public function withAttribute(string $attribute, float|int|Stringable|bool|array|string $value): static
    {
        $attributes = $this->attributes;
        $attributes[$attribute] = $value;

        return new static($this->href, $this->rels, $attributes);
    }

    public function withoutAttribute(string $attribute): static
    {
        $attributes = $this->attributes;
        unset($attributes[$attribute]);

        return new static($this->href, $this->rels, $attributes);
    }

    public function asArray(): static
    {
        return new static($this->href, $this->rels, $this->attributes, true);
    }
}
