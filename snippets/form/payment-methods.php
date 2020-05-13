<?php if (count(kpPaymentMethods()) > 1): ?>
    <div class="<?= kpStyle('payment-selector', 'kp-payment-selector') ?>">
        <h3 class="<?= kpStyle('title', 'kp-title') ?>"><?= kpT('payment-methods-title') ?></h3>
        <div class="<?= kpStyle('methods', 'kp-payment-methods') ?>">
            <?php foreach (kpPaymentMethods() as $method): ?>
                <div class="<?= kpStyle('method-column', 'kp-method-column') ?>">
                    <input class="<?= kpStyle('radio', 'kp-radio') ?>" type="radio" id="<?= $method ?>" name="<?= $method ?>" value="<?= $method ?>" x-model="type">
                    <label class="<?= kpStyle('radio-label', 'kp-radio-label') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>" for="<?= $method ?>">
                        <div class="kp-radio-label-header">
                            <?= kpT('payment.' . $method) ?>
                        </div>
                        <div class="kp-radio-label-body">
                            <?= kpT('payment.' . $method . '.description') ?>
                        </div>
                    </label>
                </div>
            <?php endforeach ?>
        </div>
    </div>
<?php endif ?>
