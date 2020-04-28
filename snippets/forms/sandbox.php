<div class="kirby-pay">
    <form class="<?= kpStyle('form', 'kp-form') ?>" x-data="kirbyPay()" x-init="mount" @submit.prevent="send">
        <fieldset class="<?= kpStyle('fieldset', 'kp-fieldset') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>">
            <legend class="kp-legend"><?= kpT('general') ?>:</legend>
            <div class="<?= kpStyle('field', 'kp-field') ?>" :class="{'<?= kpStyle('error', 'kp-text-red') ?>':error('name')}">
                <label for="kp-name" class="<?= kpStyle('label', 'kp-label') ?>"><?= kpT('name') ?></label>
                <input id="kp-name" name="kp-name"  type="text" class="<?= kpStyle('input', 'kp-input') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>" required placeholder="<?= kpT('name') ?>" aria-label="Name" x-model="data.name">
            </div>
            <div class="<?= kpStyle('field', 'kp-field') ?>" :class="{'<?= kpStyle('error', 'kp-text-red') ?>':error('email')}">
                <label for="kp-email" class="<?= kpStyle('label', 'kp-label') ?>"><?= kpT('email') ?></label>
                <input id="kp-email" name="kp-email" type="email" class="<?= kpStyle('input', 'kp-input') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>" required placeholder="<?= kpT('email') ?>" aria-label="Email" x-model="data.email" >
            </div>
            <div class="<?= kpStyle('field', 'kp-field') ?>" :class="{'<?= kpStyle('error', 'kp-text-red') ?>':error('phone')}">
                <label for="kp-phone" class="<?= kpStyle('label', 'kp-label') ?>"><?= kpT('phone') ?></label>
                <input id="kp-phone" name="kp-phone" type="text" class="<?= kpStyle('input', 'kp-input') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>" required placeholder="<?= kpT('phone') ?>" aria-label="Phone number" x-model="data.phone" >
            </div>
        </fieldset>

        <?php if((bool) pay('shipping')): ?>
        <fieldset class="<?= kpStyle('fieldset', 'kp-fieldset') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>">
            <legend class="kp-legend"><?= kpT('address-send') ?>:</legend>
            <div class="<?= kpStyle('field', 'kp-field') ?>" :class="{'<?= kpStyle('error', 'kp-text-red') ?>':error('address')}">
                <label for="kp-address" class="<?= kpStyle('label', 'kp-label') ?>"><?= kpT('address') ?></label>
                <input id="kp-address" name="kp-address"  type="text" class="<?= kpStyle('input', 'kp-input') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>" required placeholder="<?= kpT('address') ?>" aria-label="Name" x-model="data.address">
            </div>
            <div class="<?= kpStyle('field', 'kp-field') ?>" :class="{'<?= kpStyle('error', 'kp-text-red') ?>':error('state')}">
                <label for="kp-state" class="<?= kpStyle('label', 'kp-label') ?>"><?= kpT('state') ?></label>
                <input id="kp-state" name="kp-state" type="text" class="<?= kpStyle('input', 'kp-input') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>" required placeholder="<?= kpT('state') ?>" aria-label="Email" x-model="data.state" >
            </div>
            <div class="<?= kpStyle('field', 'kp-field') ?>" :class="{'<?= kpStyle('error', 'kp-text-red') ?>':error('city')}">
                <label for="kp-city" class="<?= kpStyle('label', 'kp-label') ?>"><?= kpT('city') ?></label>
                <input id="kp-city" name="kp-city" type="text" class="<?= kpStyle('input', 'kp-input') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>" placeholder="<?= kpT('city') ?>" aria-label="Email" x-model="data.city" >
            </div>
            <div class="<?= kpStyle('field', 'kp-field') ?>" :class="{'<?= kpStyle('error', 'kp-text-red') ?>':error('postal_code')}">
                <label for="kp-postal-code" class="<?= kpStyle('label', 'kp-label') ?>"><?= kpT('postal-code') ?></label>
                <input id="kp-postal-code" name="kp-postal-code" type="text" class="<?= kpStyle('input', 'kp-input') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>" maxlength="6" placeholder="<?= kpT('postal_code') ?>" aria-label="Phone number" x-model="data.postal_code" >
            </div>
            <div class="<?= kpStyle('field', 'kp-field') ?>" :class="{'<?= kpStyle('error', 'kp-text-red') ?>':error('country')}">
                <label for="kp-country" class="<?= kpStyle('label', 'kp-label') ?>"><?= kpT('country') ?></label>
                    <select id="kp-country" name="kp-country" class="<?= kpStyle('select', 'kp-select') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>">
                        <option disabled selected><?= kpT('country-select') ?></option>
                        <template x-for="(country, index) in countries" :key="index">
                            <option :value="country.value" x-text="country.label" :selected="country.value === data.country"></option>
                        </template>
                    </select>
            </div>
        </fieldset>
        <?php endif ?>

        <fieldset class="<?= kpStyle('fieldset', 'kp-fieldset') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>">
            <legend class="kp-legend"><?= kpT('payment-information') ?>:</legend>
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

        <div class="kp--mt-10 mb-10 <?= kpStyle('alert', 'kp-alert') ?> <?= kpStyle('errors', 'kp-errors') ?>" x-show="showErrors.length" style="display: none">
            <div class="kp-alert-content">
                <div class="kp-flex-shrink">
                    <?= kpStyle('alert-icon') ?>
                </div>
                <div class="kp-ml-4">
                    <div class="kp-alert-title"><?= kpT('error') ?></div>
                    <ul class="">
                        <template x-for="(message, index) in showErrors" :key="index">
                            <li x-text="message"></li>
                        </template>
                    </ul>
                </div>
            </div>
            <div class="kp-alert-background"></div>
        </div>

        <div class="kp-flex kp--mt-5">
            <button class="<?= kpStyle('button', 'kp-button') ?>" type="submit" :class="{'<?= kpStyle('button-disabled', 'kp-button-disabled') ?>':process}" :disabled="process">
                <span x-show="!process" class="<?= kpStyle('text-inverse', 'kp-text-white') ?>"><?= kpT('pay') ?></span>
                <span x-show="process" class="<?= kpStyle('text-inverse', 'kp-text-white') ?>" style="display:none;"><?= kpT('pay-process') ?></span>
            </button>
        </div>
    </form>
</div>
<script type="text/javascript" >
  function kirbyPay() {
    return {
      data: {
        name: 'John Doe',
        email: 'john@doe.com',
        phone: '3311223344',
<?php if((bool) pay('shipping')): ?>
        address: 'Dirección conocida #123',
        state: 'Jalisco',
        city: 'Guadalajara',
        postal_code: '44500',
        country: '<?= pay('default_country') ?>',
<?php endif ?>
        card_name: 'John Doe',
        card_number: '4242424242424242',
        card_month: '12',
        card_year: '23',
        card_cvc: '123',
      },
      countries: [],
      process: false,
      errors: {},
      showErrors: [],
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
          url: '<?= kpUrl("payment.{$type}.create") ?>',
          method: '<?= kpMethod("payment.{$type}.create") ?>',
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
            type: '<?= $type ?>',
            trash: 'trash-value',
          }
        }).then(response)
      },
      handleSuccess: function(data) {
        this.process = false;
        if (!data.errors) {
          window.location = data.redirect;
        } else {
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
