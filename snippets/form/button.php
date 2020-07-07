<div class="kp-flex">
    <button class="<?= kpStyle('button', 'kp-button') ?>" type="submit" x-bind:class="{'<?= kpStyle('button-disabled', 'kp-button-disabled') ?>':process}" x-bind:disabled="process">
        <span x-show="!process" class="<?= kpStyle('text-inverse', 'kp-text-white') ?>"><?= kpT($label ?? 'pay') ?></span>
        <span x-show="process" class="<?= kpStyle('text-inverse', 'kp-text-white') ?>" style="display:none;"><?= kpT(($label ?? 'pay') . '-process') ?></span>
    </button>
</div>