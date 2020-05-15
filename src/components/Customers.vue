<template>
  <k-view class="k-payments-view">
    <k-header>
      {{ $t('beebmx.kirby-pay.view.customers') }}
      <k-button-group slot="left">
        <k-button v-if="inDevelopment" icon="code" link="/plugins/development">{{ $t('beebmx.kirby-pay.view.development') }}</k-button>
        <k-button icon="money" link="/plugins/payments">{{ $t('beebmx.kirby-pay.view.payments') }}</k-button>
      </k-button-group>
      <k-button-group slot="right">
        <k-button icon="open" :link="serviceUrl" target="_blank" :disabled="serviceUrlUnavailable">{{ service }}</k-button>
      </k-button-group>
    </k-header>
    <k-list>
      <k-list-item
              v-for="customer in customers"
              :key="customer.pay_id"
              :icon="{type: 'user', back: 'black'}"
              :text="title(customer)"
              :info="customer.updated_at"
              :link="`/plugins/customer/${customer.uuid}`"
      />
    </k-list>
    <k-pagination
            align="center"
            :details="true"
            :page="page"
            :total="total"
            :limit="pagination"
            @paginate="fetch"
    />
  </k-view>
</template>

<script>
import config from "../mixins/config";
import resource from "../mixins/resource";
export default {
  mixins: [config, resource],
  components: {},
  data() {
    return {
      customers: []
    }
  },
  computed: {
    serviceUrl() {
      return this.$store.getters['kpResources/getServiceUrl']('customers');
    },
  },
  created() {
    this.fetch()
  },
  methods: {
    fetch(pagination) {
      const page = pagination
              ? pagination.page
              : this.page;

      this.$api
          .get(`beebmx/kirby-pay/customers/${page}`)
          .then(({customers, resource}) => {
            this.customers = customers;
            this.set(resource);
          })
    },
    title(customer) {
      return `${customer.customer.name}`;
    },
  }
};
</script>

<style lang="scss">

</style>
