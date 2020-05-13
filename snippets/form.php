<?php if(($items ?? null)): ?>
<?php
    snippet('kirby-pay.driver.' . pay('service'), [
        'items' => $items,
        'process' => $process ?? pay('default_payment_process', 'charge'),
    ])
?>
<?php else: ?>
<div class="kirby-pay">
    <div class="kp-font-lg kp-text-center kp-mb-4">Error in Kirby Pay</div>
    <ul class="kp-bg-white rounded kp-px-5 kp-py-4 kp-text-gray-dark">
        <?php if (!isset($items)): ?><li class="kp-mb-3"><span class="kp-bg-gray-light kp-px-1 kp-mr-4 kp-inline-block rounded">$items </span> is required</li><?php endif ?>
    </ul>
</div>
<?php endif ?>
