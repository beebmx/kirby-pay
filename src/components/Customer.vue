<template>
    <k-view class="k-payment-view">
        <k-header>
            {{ $t('beebmx.kirby-pay.view.purchase') }}
            <k-button-group slot="left">
                <k-button icon="money" link="/plugins/customers">{{ $t('beebmx.kirby-pay.view.customers') }}</k-button>
            </k-button-group>
            <k-button-group slot="right">
                <k-button :disabled="!next" icon="angle-left" :link="`/plugins/customer/${next}`"></k-button>
                <k-button :disabled="!prev" icon="angle-right" :link="`/plugins/customer/${prev}`"></k-button>
                <k-button icon="open" :link="serviceUrl" target="_blank" :disabled="serviceUrlUnavailable">{{ service }}</k-button>
            </k-button-group>
        </k-header>

        <k-grid gutter="medium">

            <k-column width="1/1">
                <kp-table-key-pair
                    :title="$t('beebmx.kirby-pay.view.customer')"
                    :data="summary"
                />
            </k-column>

            <k-column width="1/1">
                <kp-table-key-pair
                    :title="$t('beebmx.kirby-pay.view.payment_method')"
                    :data="customer.source"
                />
            </k-column>
        </k-grid>
    </k-view>
</template>

<script>
  import config from "../mixins/config";
  import TableKeyPair from "./TableKeyPair.vue";
  import TableData from "./TableData.vue"
  export default {
    mixins: [config],
    components: {
      'kp-table-key-pair': TableKeyPair,
      'kp-table-data': TableData,
    },
    data: () => ({
      customer: {},
      summary: {},
      methods: {},
      card: {},
      next: false,
      prev: false,
    }),
    computed: {
      id() {
        return this.$route.params.id || false;
      },
      serviceUrl() {
        return this.$store.getters['kpResources/getServiceUrl']('customers');
      },
      hasSource() {
        return this.customer.source
          ? !!Object.keys(this.customer.source).length
          : false
      },
    },
    created() {
      this.load()
    },
    methods: {
      load(id) {
        const uuid = id || this.id
        return this.$api
          .get(`beebmx/kirby-pay/customer/${uuid}`)
          .then(({customer, next, prev}) => {
            this.customer = customer
            this.summary = {
              id: customer.id,
              name: customer.customer.name,
              email: customer.email,
              phone: customer.customer.phone,
              updated_at: this.$library.dayjs(customer.updated_at).format("YYYY-MM-DD H:m:s"),
            }
            this.next = next
            this.prev = prev
          })
      },
      title(payment) {
        return `${payment.customer.name} (${payment.amount})`
      },
    },
    beforeRouteUpdate (to, from, next) {
      this.load(to.params.id).then(() => {
        next()
      })
    },
  }
</script>

<style scoped>
    .kp-cursor-default {
        cursor: default;
    }
</style>