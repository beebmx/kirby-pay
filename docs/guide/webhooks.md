# Webhooks

**Kirby Pay** has many webhooks to hear for every service, for connect it, you need to know which URL you need to set, and for that reason is the `env` option.
Set the `env` to `test` or `local` to enable the development option in the panel:

````php
return [
    'beebmx.kirby-pay.env' => 'local'
];
````

Now you can see the development mode and check the Webhook URL.
