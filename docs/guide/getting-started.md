# Installation

## Composer

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

And that's it, if you use all the default values, it's the only thing you will need.
