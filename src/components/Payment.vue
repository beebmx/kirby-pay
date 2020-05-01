<template>
    <k-view class="k-payment-view">
        <k-header>
            {{ $t('beebmx.kirby-pay.view.purchase') }}
            <k-button-group slot="left">
                <k-button icon="money" link="/plugins/payments">{{ $t('beebmx.kirby-pay.view.payments') }}</k-button>
                <div class="k-button kp-cursor-default">
                    <k-icon class="k-button-icon" :class="payment.status" type="circle" />
                    <span class="k-button-text" v-text="status"></span>
                </div>
            </k-button-group>
            <k-button-group slot="right">
                <k-button :disabled="!next" icon="angle-left" :link="`/plugins/payment/${next}`"></k-button>
                <k-button :disabled="!prev" icon="angle-right" :link="`/plugins/payment/${prev}`"></k-button>
                <k-button icon="open" :link="serviceUrl" target="_blank" :disabled="serviceUrlUnavailable">{{ service }}</k-button>
            </k-button-group>
        </k-header>

        <k-grid gutter="medium">
            <k-column width="1/1">
                <kp-table-key-pair
                    :title="$t('beebmx.kirby-pay.view.summary')"
                    :data="summary"
                />
            </k-column>

            <k-column width="1/1">
                <kp-table-key-pair
                    :title="$t('beebmx.kirby-pay.view.customer')"
                    :data="payment.customer"
                />
            </k-column>

            <k-column width="1/1">
                <kp-table-data
                    :title="$t('beebmx.kirby-pay.view.purchase')"
                    :data="payment.items"
                    :only="['item','amount', 'quantity']"
                />
            </k-column>

            <k-column v-if="hasShipping" width="1/1">
                <kp-table-key-pair
                        :title="$t('beebmx.kirby-pay.view.shipping')"
                        :data="shipping"
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
      payment: {},
      summary: {},
      shipping: {},
      next: false,
      prev: false,
    }),
    computed: {
      id() {
        return this.$route.params.id || false;
      },
      hasShipping() {
        return this.payment.shipping
            ? !!this.payment.shipping.length
            : false
      },
      serviceUrl() {
        return this.$store.getters['kpResources/getServiceUrl']('payments');
      },
    },
    created() {
      this.load()
    },
    methods: {
      load(id) {
        const uuid = id || this.id
        return this.$api
          .get(`beebmx/kirby-pay/payment/${uuid}`)
          .then(({payment, next, prev}) => {
            this.payment = payment
            this.next = next
            this.prev = prev
            this.summary = {
              id: payment.id,
              status: this.$t(`beebmx.kirby-pay.status.${payment.status}`),
              amount: payment.amount,
              updated_at: this.$library.dayjs(payment.updated_at).format("YYYY-MM-DD H:m:s"),
              currency: payment.currency,
            };
            this.status = this.$t(`beebmx.kirby-pay.status.${payment.status}`);
            if (payment.shipping) {
              this.shipping = payment.shipping[0]
            }
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