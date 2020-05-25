import axios from '@/utils/axios'
import 'alpinejs'
import payment from '@/connector/payment'
import customer from '@/connector/customer'

export default class KirbyPay {
  constructor(url, method, lang = 'en') {
    this.url = url
    this.method = method
    this.lang = lang
  }

  payment(elements) {
    return payment(this, elements)
  }

  customer(elements) {
    return customer(this, elements)
  }

  request(options) {
    if (options !== undefined) {
      return axios(options)
    }

    return axios
  }
}

window.KirbyPay = KirbyPay
