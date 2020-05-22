<div class="kirby-pay">
    <form class="<?= kpStyle('form', 'kp-form') ?>" x-data="kirbyPay()" x-init="mount" @submit.prevent="send">
        <?php snippet('kirby-pay.form.customer') ?>
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
        <?php snippet('kirby-pay.form.button', ['label' => 'customer-create']) ?>
    </form>
</div>
<script src="https://js.stripe.com/v3/"></script>
<script type="text/javascript" >
  var stripe = Stripe('<?= pay('service_key') ?>');
  var elements = stripe.elements();

  var cardNumber = elements.create('cardNumber', {style:<?= json_encode(kpStripe()) ?>, classes: {invalid: 'invalid'},});
  var cardCvc = elements.create('cardCvc', {style:<?= json_encode(kpStripe()) ?>, classes: {invalid: 'invalid'},});
  var cardExpiry = elements.create('cardExpiry', {style:<?= json_encode(kpStripe()) ?>, classes: {invalid: 'invalid'},});

  cardNumber.mount('#kp-card-number');
  cardExpiry.mount('#kp-card-expiry');
  cardCvc.mount('#kp-card-cvc');

  function kirbyPay() {
    return {
      <?php snippet('kirby-pay.js.customer-data', ['customer' => $customer ?? []]) ?>
      mount: function(){
        var token = document.head.querySelector('meta[name="csrf-token"]');
        if (token) {
          window.axios.defaults.headers.common['x-csrf'] = token.content;
        } else {
          console.error('CSRF token not found');
        }
      },
      send: function() {
        this.process = true;
        this.showErrors = [];
        var response = function(response) {
          !response.data.errors
            ? this.handleSuccess(response.data)
            : this.handleErrors(response.data)
        }.bind(this)

        stripe
          .createToken(cardNumber, {
            name: this.data.card_name
          })
          .then(function(result) {
            axios({
              url: '<?= kpUrl("customer.create") ?>',
              method: '<?= kpMethod("customer.create") ?>',
              data: {
                customer: this.customer,
                token: result.token.id,
              }
            }).then(response)
          }.bind(this));

      },
<?php snippet('kirby-pay.js.handlers') ?>
    }
  }
</script>
