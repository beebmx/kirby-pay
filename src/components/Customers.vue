<template>
  <k-view class="k-payments-view">
    <k-header>
      {{ $t('beebmx.kirby-pay.view.customers') }}
      <k-button-group slot="left">
        <k-button icon="open">as</k-button>
        <k-button icon="money" link="/plugins/payments">{{ $t('beebmx.kirby-pay.view.payments') }}</k-button>
      </k-button-group>

      <k-button-group slot="right">
        <kp-tag-text :text="service"></kp-tag-text>
      </k-button-group>
    </k-header>
    <k-list>
      <k-list-item
              v-for="customer in customers"
              :key="customer.id"
              :icon="{type: 'money', back: 'black'}"
              :text="title(customer)"
              :info="customer.updated_at"
              :flag="{icon: 'preview',click: someClickHandler}"
              :link="`/plugins/customer/${customer.uuid}`"
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
      customers: []
    }
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
          .then(({customers, resource, exists, service}) => {
            this.customers = customers
            this.set(resource, exists, service)
          })
    },
    title(customer) {
      return `${customer.name}`;
    },
  }
};
</script>

<style lang="scss">
/** put your css here **/
</style>
