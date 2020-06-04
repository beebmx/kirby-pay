# Installation

[[toc]]

## Composer

```ssh
composer require beebmx/kirby-pay
```

## Usage

The easiest way to use **Kirby Pay** is with the [snippets](snippets) build-in. After you install the package just include the snippets and set the items (products or services) to sell:

```php
<header>

<!-- Begin Kirby Pay styles -->
<?php snippet('kirby-pay.styles') ?>
<!-- Ends Kirby Pay styles -->

</header>
<body>

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

And that's it, if you use all the default values, it's the only thing you will need.

::: tip
If you need to create your own payment implementation with **Kirby Pay**, you can create it with the [API](../api)
:::
