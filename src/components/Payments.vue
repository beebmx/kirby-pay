<template>
  <k-view class="k-payments-view">
    <k-header>
      {{ $t('beebmx.kirby-pay.view.payments') }}
      <k-button-group slot="left">
        <k-button v-if="hasCustomers" icon="users" link="/plugins/customers">{{ $t('beebmx.kirby-pay.view.customers') }}</k-button>
      </k-button-group>
      <k-button-group slot="right">
        <k-button icon="open" :link="serviceUrl" target="_blank" :disabled="serviceUrlUnavailable">{{ service }}</k-button>
      </k-button-group>
    </k-header>
    <k-list>
      <k-list-item
              v-for="payment in payments"
              :key="payment.id"
              :class="payment.status"
              :icon="{type: 'money', back: 'black'}"
              :text="title(payment)"
              :info="payment.updated_at"
              :flag="{icon: 'circle'}"
              :link="`/plugins/payment/${payment.uuid}`"
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
      payments: []
    }
  },
  computed: {
    hasCustomers() {
      return this.$store.getters['kpResources/hasResource']('customers');
    },
    serviceUrl() {
      return this.$store.getters['kpResources/getServiceUrl']('payments');
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
  .k-list-item.pending_payment .k-list-item-options .k-icon,
  .k-icon.pending_payment {
    color: var(--color-focus-light);
  }
  .k-list-item.declined .k-list-item-options .k-icon,
  .k-icon.declined {
    color: var(--color-negative-light);
  }
  .k-list-item.expired .k-list-item-options .k-icon,
  .k-icon.expired {
    color: var(--color-negative-light);
  }
  .k-list-item.paid .k-list-item-options .k-icon,
  .k-icon.paid {
    color: var(--color-positive-light);
  }
  .k-list-item.refunded .k-list-item-options .k-icon,
  .k-icon.refunded {
    color: var(--color-positive-light);
  }
  .k-list-item.partially_refunded .k-list-item-options .k-icon,
  .k-icon.partially_refunded {
    color: var(--color-positive-light);
  }
  .k-list-item.charged_back .k-list-item-options .k-icon,
  .k-icon.charged_back {
    color: var(--color-notice-light);
  }
  .k-list-item.pre_authorized .k-list-item-options .k-icon,
  .k-icon.pre_authorized {
    color: var(--color-notice-light);
  }
  .k-list-item.voided .k-list-item-options .k-icon,
  .k-icon.voided {
    color: var(--color-notice-light);
  }
  .k-list-item.created .k-list-item-options .k-icon,
  .k-icon.created {
    color: var(--color-focus-light);
  }
  .k-list-item.fulfilled .k-list-item-options .k-icon,
  .k-icon.fulfilled {
    color: var(--color-positive-light);
  }
</style>
