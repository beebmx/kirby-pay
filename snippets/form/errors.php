<div class="mb-8 <?= kpStyle('alert', 'kp-alert') ?> <?= kpStyle('errors', 'kp-errors') ?>" x-show="showErrors.length" style="display: none">
    <div class="kp-alert-content">
        <div class="kp-flex-shrink">
            <?= kpStyle('alert-icon') ?>
        </div>
        <div class="kp-ml-4">
            <div class="kp-alert-title"><?= kpT('error') ?></div>
            <ul class="">
                <template x-for="(message, index) in showErrors" :key="index">
                    <li x-text="message"></li>
                </template>
            </ul>
        </div>
    </div>
    <div class="kp-alert-background"></div>
</div>