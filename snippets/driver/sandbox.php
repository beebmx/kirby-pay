<div class="kirby-pay">
    <form class="<?= kpStyle('form', 'kp-form') ?>" x-data="kirbyPay()" x-init="mount" @submit.prevent="send">
        <input type="hidden" x-model="type">

        <?php snippet('kirby-pay.form.customer') ?>
        <?php snippet('kirby-pay.form.shipping') ?>

        <fieldset class="<?= kpStyle('fieldset', 'kp-fieldset') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>">
            <legend class="<?= kpStyle('legend', 'kp-legend') ?>"><?= kpT('payment-information') ?>:</legend>
            <div class="<?= kpStyle('field', 'kp-field') ?>">
                <label for="kp-card-name" class="<?= kpStyle('label', 'kp-label') ?>"><?= kpT('card-name') ?></label>
                <input id="kp-card-name" name="kp-card-name" type="text" class="<?= kpStyle('input', 'kp-input') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>" aria-label="Card name" required placeholder="<?= kpT('card-name') ?>" x-model="data.card_name">
            </div>
            <div class="<?= kpStyle('field', 'kp-field') ?>">
                <label for="kp-card-number" class="<?= kpStyle('label', 'kp-label') ?>"><?= kpT('card-number') ?></label>
                <input id="kp-card-number" name="kp-card-number" type="text" class="<?= kpStyle('input', 'kp-input') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>" aria-label="Card number" required max="16" placeholder="<?= kpT('card-number') ?>" x-model="data.card_number">
            </div>
            <div class="<?= kpStyle('field', 'kp-field') ?>">
                <div class="kp-flex kp-items-center kp-w-1/2">
                    <input id="kp-card-month" name="kp-card-month" type="text" class="kp-input-month <?= kpStyle('input', 'kp-input') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>" aria-label="Card expiration month" maxlength="2" size="2" required placeholder="<?= kpT('card-month') ?>" x-model="data.card_month">
                    <span>/</span>
                    <input id="kp-card-year" name="kp-card-year" type="text" class="kp-input-year <?= kpStyle('input', 'kp-input') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>" aria-label="Card expiration year" maxlength="4" size="4" required placeholder="<?= kpT('card-year') ?>" x-model="data.card_year">
                </div>
                <div class="kp-w-1/2 kp">
                    <input id="kp-card-cvc" name="kp-card-cvc" class="<?= kpStyle('input', 'kp-input') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>" aria-label="Card CVC" maxlength="4" size="4" required placeholder="<?= kpT('card-cvc') ?>" x-model="data.card_cvc">
                </div>
            </div>
        </fieldset>

        <?php snippet('kirby-pay.form.errors') ?>
        <?php snippet('kirby-pay.form.button') ?>


    </form>
</div>
<script type="text/javascript" >
  function kirbyPay() {
    return {
      <?php snippet('kirby-pay.js.data') ?>
      mount: function(){
<?php if((bool) pay('shipping')): ?>
        axios.get('https://restcountries.eu/rest/v2/all')
          .then(function (response) {
            this.countries = response.data.map(function(country) {
              return {
                value: country.alpha2Code,
                label: country.translations['<?= substr(kirby()->language()->code(), 0, 2) ?>'] || country.name,
              };
            })
          }.bind(this))
<?php endif ?>
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
        axios({
          url: '<?= kpUrl("payment.create") ?>',
          method: '<?= kpMethod("payment.create") ?>',
          data: {
            name: this.data.name,
            email: this.data.email,
            phone: this.data.phone,
<?php if((bool) pay('shipping')): ?>
            address: this.data.address,
            state: this.data.state,
            city: this.data.city,
            postal_code: this.data.postal_code,
            country: this.data.country,
<?php endif ?>
            items: <?= json_encode($items) ?>,
            token: 'sandbox-token',
            type: this.type,
            trash: 'trash-value',
          }
        }).then(response)
      },
      handleSuccess: function(data) {
        if (!data.errors) {
          window.location = data.redirect;
        } else {
          this.process = false;
          this.setErrors(data.errors)
        }
      },
      handleErrors: function(data) {
        this.process = false;
        this.errors = data.errors
        this.setErrors(data.errors)
      },
      error: function(key) {
        return this.errors.hasOwnProperty(key)
      },
      setErrors: function(errors) {
        if (typeof errors === 'string') {
          this.showErrors = [errors]
        } else {
          this.showErrors = Object.keys(this.errors).map(function(key) {
            return this.errors[key]
          }.bind(this))
        }
      },
    }
  }
</script>
