# Getting Started

[[toc]]

## Introduction


**Kirby Pay** offers a painless and easy way to create payments for Kirby, with many things to customize if you want.

## Requeriments

The server needs to run at least:

- PHP >= 7.3

This package comes by default with some [snippets](/guide/snippets) ready to process your payments. Under the hood it's build with:

- [Alpine.js](https://github.com/alpinejs/alpine) <small>v2.3.5</small>
- [axios](https://github.com/axios/axios) <small>v0.19.2</small>

If you want to implement **Kirby Pay** in your next project, make sure you are ok with the requirements too.

## Drivers

**Kirby Pay** calls drivers the integrations made for payments. The current driver supports are:

- Sandbox
- Stripe
- Conekta 

## Payment methods

**Kirby Pay** comes with 2 different type of payment methods: `charge` and `order`, each one process a payment but with 2 differents approches.

### Charge

The `charge` payment method, creates a transaction with the driver and only process the payment with the requested information, in other words, create a payment with the buyer, items and if it's enable with the shipping information and doesn't create any other information more than the payment requested.

### Order

The `order` payment method, unlike charge, creates a transaction with the driver and creates a customer objetc in **Kirby Pay** and in the driver, this is created for future payments. In the future the customer does not need to set their information, or the card fields, just set the order with the items to process, and the transaction will be in place.  

## Pricing

**Kirby Pay** is open source, but you need a [Kirby license](https://getkirby.com/buy) to use it in your production server.

Right now the best way to support us is to star the repository. Hope soon we will be in Github sponsors if you want to support this project. 

## Credits

- Fernando Gutierrez [@beebmx](https://github.com/beebmx)
- [All Contributors](https://github.com/beebmx/kirby-pay/contributors)

## Disclaimer

This package is provided "as is" with no guarantee. Use it at your own risk and always test it yourself before using it in a production environment.

## License

Licensed under the MIT license, see [License File](https://github.com/beebmx/kirby-pay/blob/master/LICENSE.md) for details.
