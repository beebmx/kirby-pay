<div class="kirby-pay">
    <form class="<?= kpStyle('form', 'kp-form') ?>"
          x-data='{...source(), ...(new KirbyPay("<?= kpUrl('source.update') ?>","<?= kpMethod('source.update') ?>", "<?= substr(kirby()->language() ? kirby()->language()->code() : pay('locale_code', 'en'), 0, 2) ?>")).source({id:"<?= $uuid ?? null ?>", card:<?= json_encode($card ?? []) ?>})}'
          x-on:submit.prevent="prepare"
    >
        <div>
            <div class="<?= kpStyle('title', 'kp-title') ?>"><?= kpT('payment-information') ?>:</div>
            <div class="<?= kpStyle('fieldset', 'kp-fieldset') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>">
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
            </div>
        </div>
        <?php snippet('kirby-pay.form.errors') ?>
        <?php snippet('kirby-pay.form.button', ['label' => 'source-update']) ?>
    </form>
</div>
<?= js('media/plugins/beebmx/kirby-pay/app.js') ?>
<script type="text/javascript">
  function source() {
    return {
      prepare: function() {
        this.send('sandbox-token')
      }
    }
  }
</script>
