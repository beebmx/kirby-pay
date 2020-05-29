# Webhooks

[[toc]]

## Configuration

**Kirby Pay** can listen many webhooks for every service, to set in, you just need to know which URL you need to set, and for that reason is exists the `env` option.
Set the `env` to `test` or `local` to enable the development option in the panel:

````php
return [
    'beebmx.kirby-pay.env' => 'local'
];
````

Now you can see the development mode and check the Webhook URL.

::: tip
All the `webhooks` fires a [hook](hooks) just in case you need to do something with it.
:::
