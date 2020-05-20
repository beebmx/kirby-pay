<div>
    <div class="<?= kpStyle('title', 'kp-title') ?>"><?= kpT('general') ?>:</div>
    <div class="<?= kpStyle('fieldset', 'kp-fieldset') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>">
        <div class="<?= kpStyle('field', 'kp-field') ?>" :class="{'<?= kpStyle('error', 'kp-text-red') ?>':error('name')}">
            <label for="kp-name" class="<?= kpStyle('label', 'kp-label') ?>"><?= kpT('name') ?></label>
            <input id="kp-name" name="kp-name"  type="text" class="<?= kpStyle('input', 'kp-input') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>" required placeholder="<?= kpT('name') ?>" aria-label="Name" x-model="customer.name">
        </div>
        <div class="<?= kpStyle('field', 'kp-field') ?>" :class="{'<?= kpStyle('error', 'kp-text-red') ?>':error('email')}">
            <label for="kp-email" class="<?= kpStyle('label', 'kp-label') ?>"><?= kpT('email') ?></label>
            <input id="kp-email" name="kp-email" type="email" class="<?= kpStyle('input', 'kp-input') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>" required placeholder="<?= kpT('email') ?>" aria-label="Email" x-model="customer.email" >
        </div>
        <div class="<?= kpStyle('field', 'kp-field') ?>" :class="{'<?= kpStyle('error', 'kp-text-red') ?>':error('phone')}">
            <label for="kp-phone" class="<?= kpStyle('label', 'kp-label') ?>"><?= kpT('phone') ?></label>
            <input id="kp-phone" name="kp-phone" type="text" class="<?= kpStyle('input', 'kp-input') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>" required placeholder="<?= kpT('phone') ?>" aria-label="Phone number" x-model="customer.phone" >
        </div>
    </div>
</div>
