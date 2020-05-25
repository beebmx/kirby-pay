<div class="kirby-pay">
    <form class="<?= kpStyle('form', 'kp-form') ?>"
          x-data="{...payment(), ...kp}" x-init="mount" @submit.prevent="prepare">
        <input type="hidden" x-model="type">
        <?php snippet('kirby-pay.form.customer') ?>
        <?php snippet('kirby-pay.form.shipping') ?>
        <div>
            <div class="<?= kpStyle('title', 'kp-title') ?>"><?= kpT('payment-information') ?>:</div>
            <div class="<?= kpStyle('fieldset', 'kp-fieldset') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>">
                <div class="<?= kpStyle('field', 'kp-field') ?>">
                    <label for="kp-card-name" class="<?= kpStyle('label', 'kp-label') ?>"><?= kpT('card-name') ?></label>
                    <input id="kp-card-name" name="kp-card-name" type="text" class="<?= kpStyle('input', 'kp-input') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>" aria-label="Card name" required placeholder="<?= kpT('card-name') ?>" x-model="data.card_name">
                </div>
                <div class="<?= kpStyle('field', 'kp-field') ?>">
                    <label for="kp-card-number" class="<?= kpStyle('label', 'kp-label') ?>"><?= kpT('card-number') ?></label>
                    <div class="kp-input">
                        <div id="kp-card-number" class="input empty"></div>
                    </div>
                </div>
                <div class="<?= kpStyle('field', 'kp-field') ?>">
                    <div class="kp-flex kp-items-center kp-w-1/2">
                        <div class="kp-input">
                            <div id="kp-card-expiry" class="input empty"></div>
                        </div>
                    </div>
                    <div class="kp-w-1/2 kp">
                        <div class="kp-input">
                            <div id="kp-card-cvc" class=""></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php snippet('kirby-pay.form.errors') ?>
        <?php snippet('kirby-pay.form.button') ?>
    </form>
</div>
<?= js('media/plugins/beebmx/kirby-pay/app.js') ?>
<script src="https://js.stripe.com/v3/"></script>
<script type="text/javascript" >
  var kp = (new KirbyPay(
    '<?= kpUrl('payment.create') ?>','<?= kpMethod('payment.create') ?>', '<?= substr(kirby()->language()->code(), 0, 2) ?>'
  )).payment({
    type:'<?= kpGetFirstPaymentMethod() ?>', items:<?= json_encode($items ?? []) ?>, customer:<?= json_encode($customer ?? []) ?>, <?php if (kpHasShipping()): ?>shipping:<?= json_encode($shipping ?? []) ?>,<?php endif ?>card:<?= json_encode($card ?? []) ?>,country:'<?= pay('default_country') ?>',
  })

  var stripe = Stripe('<?= pay('service_key') ?>');
  var elements = stripe.elements();

  var cardNumber = elements.create('cardNumber', {style:<?= json_encode(kpStripe()) ?>, classes: {invalid: 'invalid'},});
  var cardCvc = elements.create('cardCvc', {style:<?= json_encode(kpStripe()) ?>, classes: {invalid: 'invalid'},});
  var cardExpiry = elements.create('cardExpiry', {style:<?= json_encode(kpStripe()) ?>, classes: {invalid: 'invalid'},});

  cardNumber.mount('#kp-card-number');
  cardExpiry.mount('#kp-card-expiry');
  cardCvc.mount('#kp-card-cvc');

  function payment() {
    return {
      prepare: function () {
        this.process = true;
        this.showErrors = [];
        this.requestToken()
      },
      requestToken: function() {
        stripe.createToken(cardNumber, {
          name: this.data.card_name
        }).then(function(result) {
          this.send(result.token.id || result.token)
        }.bind(this))
      },
    }
  }
</script>
