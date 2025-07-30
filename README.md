# Link Tortilla - PSR-13 Hypermedia Links Wrapper

Provides a simple set of traits to allow wrapping user classes as [PSR-13 Link]
(https://www.php-fig.org/psr/psr-13/) implementations. Wrapping the class allows
easy addition of convenience methods, while maintaining compatibility with code
that relies on the underlying PSR interfaces.

For convenience, this project also includes `\PhoneBurner\LinkTortilla\Link`, a
simple implementation of the PSR-13 `Psr\Link\EvolvableLinkInterface` that can
be used as the instance passed into the wrapper class.

## Installation

The preferred method of installation is to use [Composer](https://getcomposer.org/):

```bash
composer require phoneburner/link-tortilla
```

This library currently requires PHP >= 8.2 and [`psr/link`](https://github.com/php-fig/link) ^2.0.

## Usage

To add PSR-13 Link behavior to an arbitrary object, add the `LinkWrapper` trait,
and set the wrapped Link with `setWrapped()`. Now if you add "implements Psr\Link\EvolvableLinkInterface"
to the class definition, you will see that all the required methods are defined
and proxy to the wrapped Link.

```php
<?php

declare(strict_types=1);

use PhoneBurner\LinkTortilla\LinkWrapper;
use Psr\Link\LinkInterface;

class MyWrappedLink implements LinkInterface
{
    use LinkWrapper;

    public function __construct(LinkInterface $link)
    {
        $this->setWrapped($link);
    }
}
```

## Contributing

Contributions are welcome, please see [CONTRIBUTING.md](CONTRIBUTING.md) for more
information, including reporting bug and creating pull requests.

## Coordinated Disclosure

Keeping user information safe and secure is a top priority, and we welcome the
contribution of external security researchers. If you believe you've found a
security issue, please read [SECURITY.md](SECURITY.md) for instructions on
submitting a vulnerability report.
