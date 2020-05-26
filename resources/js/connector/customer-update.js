export default function (kp, elements) {
  return {
    elements: elements,
    id: elements.id,
    customer: {
      name: elements.customer['name'] || '',
      email: elements.customer['email'] || '',
      phone: elements.customer['phone'] || '',
    },
    process: false,
    errors: {},
    showErrors: [],
    send() {
      this.process = true;
      this.showErrors = [];
      kp.request()({
        url: kp.url,
        method: kp.method,
        data: {
          customer: this.customer,
          id: this.id,
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
