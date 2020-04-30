<template>
    <k-view class="k-payment-view">
        <k-header>
            {{ $t('beebmx.kirby-pay.view.purchase') }}
            <k-button-group slot="left">
                <k-button icon="money" link="/plugins/payments">{{ $t('beebmx.kirby-pay.view.payments') }}</k-button>
                <<div class="k-button kp-cursor-default">
                    <k-icon class="k-button-icon" :class="statusColor" type="circle" />
                    <span class="k-button-text" v-text="status"></span>
                </div>>
            </k-button-group>
            <k-button-group slot="right">
                <k-button :disabled="!next" icon="angle-left" :link="`/plugins/payment/${next}`"></k-button>
                <k-button :disabled="!prev" icon="angle-right" :link="`/plugins/payment/${prev}`"></k-button>
                <kp-tag-text :text="service"></kp-tag-text>
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
  import TableKeyPair from "./TableKeyPair.vue";
  import TableData from "./TableData.vue"
  import TagText from "./TagText.vue"

  export default {
    components: {
      'kp-table-key-pair': TableKeyPair,
      'kp-table-data': TableData,
      'kp-tag-text': TagText,
    },
    data: () => ({
      payment: {},
      summary: {},
      shipping: {},
      next: false,
      prev: false,
      status: '',
      statusColor: 'kp-wait',
      statusColors: {
        'pending_payment': 'kp-info',
        'declined': 'kp-danger',
        'expired': 'kp-danger',
        'paid': 'kp-success',
        'refunded': 'kp-success',
        'partially_refunded': 'kp-success',
        'charged_back': 'kp-warning',
        'pre_authorized': 'kp-warning',
        'voided': 'kp-warning',
        'created': 'kp-info',
        'fulfilled': 'kp-success',
      }
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
      service() {
        return this.$store.getters['kpResources/getService'];
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
            this.statusColor = this.statusColors[payment.status];
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
    .kp-wait {
        color: var(--color-text);
    }
    .kp-success {
        color: var(--color-positive-light);
    }
    .kp-danger {
        color: var(--color-negative-light);
    }
    .kp-warning {
        color: var(--color-notice-light);
    }
    .kp-info {
        color: var(--color-focus-light);
    }
</style>