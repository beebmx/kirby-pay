# Kirby Pay

*Kirby Pay* offers a painless and easy way to create payments for Kirby, with many things to customize if you want.

See the docs for everything: [https://beebmx.github.io/kirby-pay](https://beebmx.github.io/kirby-pay)

## Installation

### Composer

```ssh
composer required beebmx/kirby-pay
```

## Usage

After you install the package just include the snippets and set the items (products or services) to sell:

```php

<header>
...
<?php snippet('kirby-pay.styles') ?>
...
</header>
<body>
...

<!-- Begin Kirby Pay -->
<?php
    snippet('kirby-pay.payment', [
        'items' => [
            ['name' => 'Product 001', 'amount' => 20.00, 'quantity' => 1],
            ['name' => 'Product 002', 'amount' => 10.00, 'quantity' => 2],
        ],
    ]);
?>
<!-- Ends Kirby Pay -->

</body>
```

## Disclaimer

This plugin is provided "as is" with no guarantee. Use it at your own risk and always test it yourself before using it in a production environment.

## Credits

- Fernando Gutierrez [@beebmx](https://github.com/beebmx) and contributors
- [All Contributors](../../contributors)

## License


Licensed under the MIT license, see [LICENSE.md](LICENSE.md) for details.
