<?php if((bool) pay('shipping')): ?>
    <fieldset class="<?= kpStyle('fieldset', 'kp-fieldset') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>">
        <legend class="<?= kpStyle('legend', 'kp-legend') ?>"><?= kpT('address-send') ?>:</legend>
        <div class="<?= kpStyle('field', 'kp-field') ?>" :class="{'<?= kpStyle('error', 'kp-text-red') ?>':error('address')}">
            <label for="kp-address" class="<?= kpStyle('label', 'kp-label') ?>"><?= kpT('address') ?></label>
            <input id="kp-address" name="kp-address"  type="text" class="<?= kpStyle('input', 'kp-input') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>" required placeholder="<?= kpT('address') ?>" aria-label="Name" x-model="data.address">
        </div>
        <div class="<?= kpStyle('field', 'kp-field') ?>" :class="{'<?= kpStyle('error', 'kp-text-red') ?>':error('state')}">
            <label for="kp-state" class="<?= kpStyle('label', 'kp-label') ?>"><?= kpT('state') ?></label>
            <input id="kp-state" name="kp-state" type="text" class="<?= kpStyle('input', 'kp-input') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>" required placeholder="<?= kpT('state') ?>" aria-label="Email" x-model="data.state" >
        </div>
        <div class="<?= kpStyle('field', 'kp-field') ?>" :class="{'<?= kpStyle('error', 'kp-text-red') ?>':error('city')}">
            <label for="kp-city" class="<?= kpStyle('label', 'kp-label') ?>"><?= kpT('city') ?></label>
            <input id="kp-city" name="kp-city" type="text" class="<?= kpStyle('input', 'kp-input') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>" placeholder="<?= kpT('city') ?>" aria-label="Email" x-model="data.city" >
        </div>
        <div class="<?= kpStyle('field', 'kp-field') ?>" :class="{'<?= kpStyle('error', 'kp-text-red') ?>':error('postal_code')}">
            <label for="kp-postal-code" class="<?= kpStyle('label', 'kp-label') ?>"><?= kpT('postal-code') ?></label>
            <input id="kp-postal-code" name="kp-postal-code" type="text" class="<?= kpStyle('input', 'kp-input') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>" maxlength="6" placeholder="<?= kpT('postal-code') ?>" aria-label="Phone number" x-model="data.postal_code" >
        </div>
        <div class="<?= kpStyle('field', 'kp-field') ?>" :class="{'<?= kpStyle('error', 'kp-text-red') ?>':error('country')}">
            <label for="kp-country" class="<?= kpStyle('label', 'kp-label') ?>"><?= kpT('country') ?></label>
            <select id="kp-country" name="kp-country" class="<?= kpStyle('select', 'kp-select') ?> <?= kpStyle('background', 'kp-bg-transparent') ?>">
                <option disabled selected><?= kpT('country-select') ?></option>
                <template x-for="(country, index) in countries" :key="index">
                    <option :value="country.value" x-text="country.label" :selected="country.value === data.country"></option>
                </template>
            </select>
        </div>
    </fieldset>
<?php endif ?>