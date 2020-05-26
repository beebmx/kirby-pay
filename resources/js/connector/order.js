import axios from 'axios'

export default function (kp, elements) {
  return {
    elements: elements,
    id: elements.id,
    shipping: {
      address: null,
      state: null,
      city: null,
      postal_code: null,
      country: null,
    },
    items: elements.items,
    type: elements.type,
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
    send() {
      this.process = true;
      this.showErrors = [];
      const data = Object.assign({
        id: this.id,
        items: this.items,
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
