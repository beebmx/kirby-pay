export default function (kp, elements) {
  return {
    elements: elements,
    id: elements.id,
    data: {
      card_name: elements.card['name'] || '',
      card_number: elements.card['number'] || '',
      card_month: elements.card['month'] || '',
      card_year: elements.card['year'] || '',
      card_cvc: elements.card['cvc'] || '',
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
          id: this.id,
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
