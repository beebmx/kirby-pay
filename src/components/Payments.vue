<template>
  <k-view class="k-payments-view">
    <k-header>
      {{ $t('beebmx.kirby-pay.view.payments') }}
      <k-button-group slot="left">
        <k-button icon="open">as</k-button>
        <k-button v-if="hasCustomers" icon="users" link="/plugins/customers">{{ $t('beebmx.kirby-pay.view.customers') }}</k-button>
      </k-button-group>

      <k-button-group slot="right">
        <kp-tag-text :text="service"></kp-tag-text>
      </k-button-group>
    </k-header>
    <k-list>
      <k-list-item
              v-for="payment in payments"
              :key="payment.id"
              :icon="{type: 'money', back: 'black'}"
              :text="title(payment)"
              :info="payment.updated_at"
              :flag="{icon: 'preview'}"
              :link="`/plugins/payment/${payment.uuid}`"
              :options="[{icon: 'edit', text: 'Edit'},{icon: 'trash', text: 'Delete'}]"
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
import TagText from "./TagText.vue"
import resource from "../mixins/resource";
export default {
  mixins: [resource],
  components: {
    'kp-tag-text': TagText,
  },
  data() {
    return {
      payments: []
    }
  },
  computed: {
    hasCustomers() {
      return this.$store.getters['kpResources/hasResource']('customers');
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
          .get(`beebmx/kirby-pay/payments/${page}`)
          .then(({payments, resource, exists, service}) => {
            this.payments = payments
            this.set(resource, exists, service)
          })
    },
    title(payment) {
      return `${payment.customer.name} (${payment.amount})`;
    },
  }
};
</script>

<style lang="scss">
/** put your css here **/
</style>
