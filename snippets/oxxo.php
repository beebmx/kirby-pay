<?php if(isset($payment->extra[0]['type']) && strtolower($payment->extra[0]['type']) === 'oxxo'): ?>
<div class="kirby-pay-oxxo">
    <div class="oxxopay-header">
        <div class="oxxopay-reminder text-center"><?= kpT('oxxo.reminder') ?></div>
        <div class="oxxopay-info">
            <div class="oxxopay-brand"><img src="<?= url('media/plugins/beebmx/kirby-pay/oxxopay_brand.png') ?>" alt="<?= kpT('oxxo.pay') ?>"></div>
            <div class="oxxopay-amount">
                <h3 class="text-center"><?= kpT('oxxo.amount') ?></h3>
                <h2><?= $payment->amount ?> <sup><?= $payment->currency ?></sup></h2>
                <p><?= kpT('oxxo.hint') ?></p>
            </div>
        </div>
        <div class="oxxopay-reference">
            <h3><?= kpT('oxxo.reference') ?></h3>
            <h1><?= $payment->extra[0]['reference'] ?></h1>
        </div>
    </div>
    <div class="oxxopay-instructions">
        <h3><?= kpT('oxxo.instructions') ?></h3>
        <ol>
            <li><?= kpT('oxxo.step.1.1') ?> <a href="https://www.google.com.mx/maps/search/oxxo/" target="_blank"><?= kpT('oxxo.step.1.2') ?></a>.</li>
            <li><?= kpT('oxxo.step.2.1') ?> <strong><?= kpT('oxxo.pay') ?></strong><?= kpT('oxxo.step.2.2') ?></li>
            <li><?= kpT('oxxo.step.3') ?></li>
            <li><?= kpT('oxxo.step.4') ?></li>
            <li><?= kpT('oxxo.step.5.1') ?> <strong><?= kpT('oxxo.step.5.2') ?></strong> </li>
        </ol>
        <div class="oxxopay-footnote"><?= kpT('oxxo.alert.1') ?> <strong><?= pay('name') ?></strong><?= kpT('oxxo.alert.2') ?></div>
    </div>
</div>
<?php endif ?>
