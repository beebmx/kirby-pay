<div class="kp-flex">
    <button class="<?= kpStyle('button', 'kp-button') ?>" type="submit" :class="{'<?= kpStyle('button-disabled', 'kp-button-disabled') ?>':process}" :disabled="process">
        <span x-show="!process" class="<?= kpStyle('text-inverse', 'kp-text-white') ?>"><?= kpT('pay') ?></span>
        <span x-show="process" class="<?= kpStyle('text-inverse', 'kp-text-white') ?>" style="display:none;"><?= kpT('pay-process') ?></span>
    </button>
</div>