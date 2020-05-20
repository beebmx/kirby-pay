handleSuccess: function(data) {
  if (!data.errors) {
    window.location = data.redirect;
  } else {
    this.process = false;
    this.setErrors(data.errors)
  }
},
handleErrors: function(data) {
  this.process = false;
  this.errors = data.errors
  this.setErrors(data.errors)
},
error: function(key) {
  return this.errors.hasOwnProperty(key)
},
setErrors: function(errors) {
  if (typeof errors === 'string') {
    this.showErrors = [errors]
  } else {
    this.showErrors = Object.keys(this.errors).map(function(key) {
        return this.errors[key]
    }.bind(this))
  }
},