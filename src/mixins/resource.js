export default {
  computed: {
    current() {
      return this.$route.params.id;
    },
    resource() {
      return this.$store.getters['kpResources/values'];
    },
    service() {
      return this.$store.getters['kpResources/getService'];
    },
    total() {
      return this.resource
        ? this.resource.total
        : null
    },
    page() {
      return this.resource
        ? this.resource.page
        : 1
    },
    pagination() {
      return this.resource
        ? this.resource.pagination
        : null
    },
  },
  created() {
    this.$store.dispatch('kpResources/init', this.current)
  },
  methods: {
    set(resource) {
      this.$store.dispatch('kpResources/set', resource)
    },
  }
}