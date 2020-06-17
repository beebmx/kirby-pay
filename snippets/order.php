<?php if(($uuid ?? null) && ($items ?? null)): ?>
<div class="kirby-pay">
    <form class="<?= kpStyle('form', 'kp-form') ?>"
          x-data='{...order(), ...(new KirbyPay("<?= kpUrl("order.create") ?>","<?= kpMethod("order.create") ?>","<?= substr(kirby()->language() ? kirby()->language()->code() : pay('locale_code', 'en'), 0, 2) ?>")).order({id:"<?= $uuid ?? null ?>",items:<?= json_encode($items ?? []) ?>,extra_amounts:<?= json_encode($extra_amounts ?? []) ?>,<?php if(kpHasShipping()): ?>shipping:<?= json_encode($shipping ?? []) ?>,<?php endif ?>country:"<?= pay('default_country') ?>"})}'
          x-init="mount"
          @submit.prevent="prepare"
    >
        <?php snippet('kirby-pay.form.shipping') ?>
        <?php snippet('kirby-pay.form.errors') ?>
        <?php snippet('kirby-pay.form.button') ?>
    </form>
</div>
<?= js('media/plugins/beebmx/kirby-pay/app.js') ?>
<script type="text/javascript" >
  function order() {
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
        <?php if (!isset($items)): ?><li class="kp-my-1"><span class="kp-bg-gray-light kp-px-1 kp-mr-2 kp-inline-block rounded">$items </span> is required</li><?php endif ?>
    </ul>
</div>
<?php endif ?>
