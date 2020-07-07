<template>
  <k-view class="k-payments-view">
    <k-header>
      {{ $t('beebmx.kirby-pay.view.development') }}
      <k-button-group slot="left">
        <k-button icon="money" link="/plugins/payments">{{ $t('beebmx.kirby-pay.view.payments') }}</k-button>
        <k-button v-if="hasCustomers" icon="users" link="/plugins/customers">{{ $t('beebmx.kirby-pay.view.customers') }}</k-button>
      </k-button-group>
      <k-button-group slot="right">
        <k-button icon="open" :link="serviceUrl" target="_blank" :disabled="serviceUrlUnavailable">{{ service }}</k-button>
      </k-button-group>
    </k-header>

    <k-grid gutter="medium">
      <k-column width="1/1">
        <kp-table-key-pair
                :title="$t('beebmx.kirby-pay.view.webhook')"
                :data="webhook"
        />
      </k-column>


      <k-column width="1/1">
        <k-list>
          <k-list-item
                  v-for="log in logs"
                  :key="log.pay_id"
                  :class="status(log)"
                  :icon="{type: 'lock', back: 'black'}"
                  :text="log.type"
                  :info="log.updated_at"
                  :flag="{icon: 'circle'}"
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
      </k-column>
    </k-grid>
  </k-view>
</template>

<script>
import config from "../mixins/config";
import resource from "../mixins/resource";
import TableKeyPair from "./TableKeyPair.vue";
import TableData from "./TableData";
export default {
  mixins: [config, resource],
  components: {
    'kp-table-key-pair': TableKeyPair,
  },
  data() {
    return {
      logs: [],
      webhook: {},
    }
  },
  computed: {
    hasCustomers() {
      return this.$store.getters['kpResources/hasResource']('customers');
    },
    serviceUrl() {
      return this.$store.getters['kpResources/getServiceUrl']('logs');
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
          .get(`beebmx/kirby-pay/development/${page}`)
          .then(({logs, resource, webhook}) => {
            this.logs = logs
            this.webhook = {
              url: webhook,
            }
            this.set(resource)
          })
    },
    status(log) {
      try {
        return log.data.object.payment_status || log.data.object.status
      } catch(e) {
        try {
          return log.data.status;
        } catch (e) {
          try {
            return log.status;
          } catch (e) {
            return '';
          }
        }
      }
    }
  }
};
</script>

<style lang="scss">
</style>
