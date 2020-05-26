<?php if(($uuid ?? null)): ?>
    <?php
    snippet('kirby-pay.customer.source.' . pay('service'), [
        'uuid' => $uuid,
        'card' => $card ?? [],
    ])
    ?>
<?php else: ?>
    <div class="kirby-pay">
        <div class="kp-font-lg kp-text-center kp-mb-4">Error in Kirby Pay</div>
        <ul class="kp-bg-white rounded kp-px-5 kp-py-4 kp-text-gray-dark">
            <?php if (!isset($uuid)): ?><li class="kp-my-1"><span class="kp-bg-gray-light kp-px-1 kp-mr-2 kp-inline-block rounded">$uuid </span> is required</li><?php endif ?>
        </ul>
    </div>
<?php endif ?>
