# Getting Started

[[toc]]

## Introduction


**Kirby Pay** offers an easy way to make payments with Kirby, providing several options to customize  according to your needs.

## Requirements

In addition to meeting [Kirby's requirements](https://getkirby.com/docs/guide/quickstart#requirements), to use **Kirby Pay** in your project, the server needs to run at least:

- PHP >= 7.3

This package comes by default with some [snippets](/guide/snippets) ready to process your payments. Under the hood it's build with:

- [Alpine.js](https://github.com/alpinejs/alpine) <small>v2.3.5</small>
- [axios](https://github.com/axios/axios) <small>v0.19.2</small>

Therefore, you must also meet their own requirements.

## Drivers

**Kirby Pay** calls drivers the integrations made for payments. The current driver supports are:

- Sandbox
- Stripe
- Conekta 

## Payment methods

**Kirby Pay** comes with two different type of payment methods: `charge` and `order`, each one process a payment but with two differents approches.

### Charge

The `charge` payment method, creates a transaction with the driver and only process the payment with the requested information. That is, it creates a payment with the customer information (like name, email and phone), purchased items and the shipping information, if this option is enabled; and doesn't create any other information more than the payment requested.

### Order

The `order` payment method, unlike charge, creates a transaction with the driver and stores a customer object in **Kirby Pay** and in the driver to facilitate future payments. This way, in the future the customer will not need to fill in their information or the card fields, just set the order with the items to process, and the transaction will be in place.  

## Pricing

**Kirby Pay** is open source, but you need a [Kirby license](https://getkirby.com/buy) to use it in your production server.

Right now the best way to support us is to star the repository. Hope soon we will be in Github sponsors if you want to further support this project. 

## Credits

- Fernando Gutierrez [@beebmx](https://github.com/beebmx)
- [jonatanjonas](https://github.com/jonatanjonas) (logo)
- [All Contributors](https://github.com/beebmx/kirby-pay/contributors)

## Disclaimer

This package is provided "as is" with no guarantee. Use it at your own risk and always test it for yourself before using it in a production environment.

## License

Licensed under the MIT license, see [License File](https://github.com/beebmx/kirby-pay/blob/master/LICENSE.md) for details.
