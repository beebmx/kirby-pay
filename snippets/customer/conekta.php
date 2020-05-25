<div class="kirby-pay">
    <form class="<?= kpStyle('form', 'kp-form') ?>" x-data="{...customer(), ...kp}" @submit.prevent="prepare">
        <?php snippet('kirby-pay.form.customer') ?>
        <div>
            <div class="<?= kpStyle('title', 'kp-title') ?>"><?= kpT('payment-information') ?>:</div>
            <div class="<?= kpStyle('fieldset', 'kp-fieldset') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>">
                <div class="<?= kpStyle('field', 'kp-field') ?>">
                    <label for="kp-card-name" class="<?= kpStyle('label', 'kp-label') ?>"><?= kpT('card-name') ?></label>
                    <input id="kp-card-name" name="kp-card-name" type="text" class="<?= kpStyle('input', 'kp-input') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>" aria-label="Card name" required placeholder="<?= kpT('card-name') ?>" size="20" x-model="data.card_name" data-conekta="card[name]">
                </div>
                <div class="<?= kpStyle('field', 'kp-field') ?>">
                    <label for="kp-card-number" class="<?= kpStyle('label', 'kp-label') ?>"><?= kpT('card-number') ?></label>
                    <input id="kp-card-number" name="kp-card-number" type="text" class="<?= kpStyle('input', 'kp-input') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>" aria-label="Card number" required max="16" placeholder="<?= kpT('card-number') ?>" size="20" x-model="data.card_number" data-conekta="card[number]">
                </div>
                <div class="<?= kpStyle('field', 'kp-field') ?>">
                    <div class="kp-flex kp-items-center kp-w-1/2">
                        <input type="text" class="kp-input-month <?= kpStyle('input', 'kp-input') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>" aria-label="Card expiration month" maxlength="2" size="2" required placeholder="<?= kpT('card-month') ?>" x-model="data.card_month" data-conekta="card[exp_month]">
                        <span>/</span>
                        <input type="text" class="kp-input-year <?= kpStyle('input', 'kp-input') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>" aria-label="Card expiration year" maxlength="3" size="4" required placeholder="<?= kpT('card-year') ?>" x-model="data.card_year" data-conekta="card[exp_year]">
                    </div>
                    <div class="kp-w-1/2 kp">
                        <input class="<?= kpStyle('input', 'kp-input') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>" aria-label="Card CVC" maxlength="4" size="4" required placeholder="<?= kpT('card-cvc') ?>" x-model="data.card_cvc" data-conekta="card[cvc]">
                    </div>
                </div>
            </div>
        </div>
        <?php snippet('kirby-pay.form.errors') ?>
        <?php snippet('kirby-pay.form.button', ['label' => 'customer-create']) ?>
    </form>
</div>
<?= js('media/plugins/beebmx/kirby-pay/app.js') ?>
<script type="text/javascript" src="https://cdn.conekta.io/js/latest/conekta.js"></script>
<script type="text/javascript" >
  Conekta.setPublicKey('<?= pay('service_key') ?>');
  var kp = (new KirbyPay(
    '<?= kpUrl("customer.create") ?>','<?= kpMethod("customer.create") ?>', '<?= substr(kirby()->language()->code(), 0, 2) ?>'
  )).customer({
    customer:<?= json_encode($customer ?? []) ?>, card:<?= json_encode($card ?? []) ?>,
  })
  function customer() {
    return {
      prepare: function() {
        this.process = true;
        this.showErrors = [];
        this.requestToken();
      },
      requestToken: function() {
        Conekta.Token.create(this.$el, this.setToken.bind(this), this.conektaErrorResponseHandler.bind(this));
      },
      setToken: function(token) {
        this.send(token.id || token)
      },
      conektaErrorResponseHandler: function(response) {
        this.process = false;
        this.showErrors = [
          response.message_to_purchaser
        ]
      },
    }
  }
</script>
