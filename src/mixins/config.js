export default {
  computed: {
    service() {
      return this.$store.getters['kpResources/getService'];
    },
    payIdLength() {
      return this.$store.getters['kpResources/getPayIdLength'];
    },
    serviceUrlUnavailable() {
      return !this.serviceUrl
    },
    inDevelopment() {
      return this.$store.getters['kpResources/inDevelopment'];
    },
  },
  created() {
    this.config()
  },
  methods: {
    config() {
      this.$api
        .get(`beebmx/kirby-pay/config`)
        .then(({service, resources, development}) => {
          this.$store.dispatch('kpResources/config', {service, resources, development})
        })
    },
    set(resource, exists, service) {
      this.$store.dispatch('kpResources/setService', service)
    },
  }
}