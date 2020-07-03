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
                    :exclude="['object', 'customer_id']"
                />
            </k-column>

            <k-column width="1/1">
                <kp-table-data
                    :title="$t('beebmx.kirby-pay.view.purchase')"
                    :data="payment.items"
                    :only="['id', 'name','amount', 'quantity']"
                />
            </k-column>

            <k-column v-if="hasExtras" width="1/1">
                <kp-table-key-pair
                        :title="$t('beebmx.kirby-pay.view.extras')"
                        :data="payment.extra_amounts"
                />
            </k-column>

            <k-column v-if="hasShipping" width="1/1">
                <kp-table-key-pair
                        :title="$t('beebmx.kirby-pay.view.shipping')"
                        :data="shipping"
                />
            </k-column>

            <k-column v-if="hasExtra" width="1/1">
                <kp-table-key-pair
                        :title="$t('beebmx.kirby-pay.view.extra')"
                        :data="extra"
                        :images="['barcode_url']"
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
      extra: {},
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
            ? !!Object.keys(this.payment.shipping).length
            : false
      },
      hasExtra() {
        return this.payment.extra
            ? !!Object.keys(this.payment.extra).length
            : false
      },
      hasExtras() {
        return this.payment.extra_amounts
            ? !!Object.keys(this.payment.extra_amounts).length
            : false
      },
      serviceUrl() {
        return this.$store.getters['kpResources/getServiceUrl']('payments')
            ? `${this.$store.getters['kpResources/getServiceUrl']('payments')}/${this.payment.id}`
            : null;
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
              pay_id: '#' + String(payment.pay_id).padStart(this.payIdLength, '0'),
              id: payment.id,
              payment_id: payment.payment_id,
              status: this.$t(`beebmx.kirby-pay.status.${payment.status}`),
              amount: payment.amount,
              updated_at: this.$library.dayjs(payment.updated_at).format("YYYY-MM-DD H:mm:ss"),
              currency: payment.currency,
            };
            if (this.hasExtra) {
                this.extra = {
                  amount: payment.extra[0].amount,
                  fee: payment.extra[0].fee,
                  reference: payment.extra[0].reference,
                  barcode_url: payment.extra[0].barcode_url,
                  expires_at: payment.extra[0].expires_at ? this.$library.dayjs.unix(payment.extra[0].expires_at).format("YYYY-MM-DD H:mm:ss") : null,
                  payment_method: payment.extra[0].payment_method,
                }
            }
            this.status = this.$t(`beebmx.kirby-pay.status.${payment.status}`);
            if (this.hasShipping) {
              this.shipping = payment.shipping
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
