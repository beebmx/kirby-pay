export default {
  computed: {
    service() {
      return this.$store.getters['kpResources/getService'];
    },
    serviceUrlUnavailable() {
      return !this.serviceUrl
    }
  },
  created() {
    this.config()
  },
  methods: {
    config() {
      this.$api
        .get(`beebmx/kirby-pay/config`)
        .then(({service, resources}) => {
          this.$store.dispatch('kpResources/config', {service, resources})
        })
    },
    set(resource, exists, service) {
      this.$store.dispatch('kpResources/setService', service)
    },
  }
}