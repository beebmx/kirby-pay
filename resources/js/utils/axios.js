import axios from 'axios';

const instance = axios.create();

try {
  instance.defaults.headers.common['x-csrf'] = document.head.querySelector(
    'meta[name="csrf-token"]'
  ).content;
} catch (e) {
  console.error('CSRF token not found')
}

export default instance;
