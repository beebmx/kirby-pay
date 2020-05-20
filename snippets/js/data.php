customer: {
name: '<?= $customer['name'] ?? ''?>',
email: '<?= $customer['email'] ?? ''?>',
phone: '<?= $customer['phone'] ?? ''?>',
},
<?php if((bool) pay('shipping')): ?>
shipping: {
    address: '<?= $shipping['address'] ?? ''?>',
    state: '<?= $shipping['state'] ?? ''?>',
    city: '<?= $shipping['city'] ?? ''?>',
    postal_code: '<?= $shipping['postal_code'] ?? ''?>',
    country: '<?= pay('default_country') ?>',
},
<?php endif ?>
data: {
card_name: '<?= $card['name'] ?? ''?>',
card_number: '<?= $card['number'] ?? ''?>',
card_month: '<?= $card['month'] ?? ''?>',
card_year: '<?= $card['year'] ?? ''?>',
card_cvc: '<?= $card['cvc'] ?? ''?>',
},
type: '<?= kpGetFirstPaymentMethod() ?>',
countries: [],
process: false,
errors: {},
showErrors: [],