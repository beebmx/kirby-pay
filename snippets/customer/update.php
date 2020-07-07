<?php if(($uuid ?? null)): ?>
<div class="kirby-pay">
    <form class="<?= kpStyle('form', 'kp-form') ?>"
          x-data='{...update(), ...(new KirbyPay("<?= kpUrl('customer.update') ?>","<?= kpMethod('customer.update') ?>", "<?= substr(kirby()->language() ? kirby()->language()->code() : pay('locale_code', 'en'), 0, 2) ?>")).update({id:"<?= $uuid ?? null ?>",customer:<?= json_encode($customer ?? []) ?>})}'
          x-on:submit.prevent="prepare"
    >
        <?php snippet('kirby-pay.form.customer') ?>
        <?php snippet('kirby-pay.form.errors') ?>
        <?php snippet('kirby-pay.form.button', ['label' => 'customer-update']) ?>
    </form>
</div>
<?= js('media/plugins/beebmx/kirby-pay/app.js') ?>
<script type="text/javascript" >
  function update() {
    return {
      prepare: function() {
        this.send()
      }
    }
  }
</script>
<?php else: ?>
    <div class="kirby-pay">
        <div class="kp-font-lg kp-text-center kp-mb-4">Error in Kirby Pay</div>
        <ul class="kp-bg-white rounded kp-px-5 kp-py-4 kp-text-gray-dark">
            <?php if (!isset($uuid)): ?><li class="kp-my-1"><span class="kp-bg-gray-light kp-px-1 kp-mr-2 kp-inline-block rounded">$uuid </span> is required</li><?php endif ?>
        </ul>
    </div>
<?php endif ?>
