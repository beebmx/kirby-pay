import axios from 'axios'

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
    items: elements.items,
    type: elements.type,
    extra_amounts: elements.extra_amounts,
    countries: [],
    process: false,
    errors: {},
    showErrors: [],
    mount() {
      this.shipping = this.setShipping()
      if (this.hasShipping()) {
        this.setCountries()
      }
    },
    send(token) {
      this.process = true;
      this.showErrors = [];
      const data = Object.assign({
        customer: this.customer,
        items: this.items,
        extra_amounts: this.extra_amounts,
        token: token,
        type: this.type,
      }, this.hasShipping() ? {shipping: this.shipping} : null);

      kp.request()({
        url: kp.url,
        method: kp.method,
        data
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
    setCountries() {
      axios.get('https://restcountries.eu/rest/v2/all')
        .then(({data}) => {
          this.countries = data.map(function(country) {
            return {
              value: country.alpha2Code,
              label: country.translations[kp.lang] || country.name,
            };
          })
        })
    },
    setShipping() {
      if (this.hasShipping()) {
        return {
          address: this.elements.shipping['address'] || '',
          state: this.elements.shipping['state'] || '',
          city: this.elements.shipping['city'] || '',
          postal_code: this.elements.shipping['postal_code'] || '',
          country: this.elements.country,
        };
      }
      return null
    },
    hasShipping() {
      return this.elements.hasOwnProperty('shipping')
    }
  }
}
