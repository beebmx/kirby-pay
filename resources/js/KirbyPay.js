import axios from '@/utils/axios'
import 'alpinejs'
import payment from '@/connector/payment'
import order from '@/connector/order'
import customer from '@/connector/customer'
import update from '@/connector/customer-update'
import source from '@/connector/source'

export default class KirbyPay {
  constructor(url, method, lang = 'en') {
    this.url = url
    this.method = method
    this.lang = lang
  }

  payment(elements) {
    return payment(this, elements)
  }

  order(elements) {
    return order(this, elements)
  }

  customer(elements) {
    return customer(this, elements)
  }

  update(elements) {
    return update(this, elements)
  }

  source(elements) {
    return source(this, elements)
  }

  request(options) {
    if (options !== undefined) {
      return axios(options)
    }

    return axios
  }
}

window.KirbyPay = KirbyPay
