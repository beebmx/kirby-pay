export default function (kp, elements) {
  return {
    elements: elements,
    customer: {
      name: elements.customer['name'] || '',
      email: elements.customer['email'] || '',
      phone: elements.customer['phone'] || '',
    },
    data: {
      card_name: elements.card['name'] || '',
      card_number: elements.card['number'] || '',
      card_month: elements.card['month'] || '',
      card_year: elements.card['year'] || '',
      card_cvc: elements.card['cvc'] || '',
    },
    shipping: {
      address: null,
      state: null,
      city: null,
      postal_code: null,
      country: null,
    },
    process: false,
    errors: {},
    showErrors: [],
    send(token) {
      this.process = true;
      this.showErrors = [];
      kp.request()({
        url: kp.url,
        method: kp.method,
        data: {
          customer: this.customer,
          token: token,
        }
      }).then(({data}) => {
        if (!data.errors) {
          window.location = data.redirect;
        } else {
          this.process = false;
          this.errors = data.errors
          this.setErrors(data.errors)
        }
      })
    },
    error (key) {
      return this.errors.hasOwnProperty(key)
    },
    setErrors (errors) {
      if (typeof errors === 'string') {
        this.showErrors = [errors]
      } else {
        this.showErrors = Object.keys(this.errors).map(function (key) {
          return this.errors[key]
        }.bind(this))
      }
    },
  }
}
