customer: {
name: '<?= $customer['name'] ?? ''?>',
email: '<?= $customer['email'] ?? ''?>',
phone: '<?= $customer['phone'] ?? ''?>',
},
data: {
card_name: '<?= $card['name'] ?? ''?>',
card_number: '<?= $card['number'] ?? ''?>',
card_month: '<?= $card['month'] ?? ''?>',
card_year: '<?= $card['year'] ?? ''?>',
card_cvc: '<?= $card['cvc'] ?? ''?>',
},
process: false,
errors: {},
showErrors: [],