<template>
    <div class="kp-table-data">
        <h3 class="kp-table-data-headline" v-text="title"></h3>
        <table class="kp-table-data-element">
            <thead>
                <tr>
                    <th v-if="isAllow(key)" v-for="(key, id) in heads" :key="id" class="kp-table-data-td kp-table-data-head" v-text="$t(`beebmx.kirby-pay.table.${key}`)"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, id) in data" :key="id">
                    <td v-if="isAllow(key)" v-for="(value, key) in item" :key="key" class="kp-table-data-td" v-text="value"></td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
  export default {
    props: {
      title: String,
      data: Object,
      only: Array,
    },
    computed: {
      first() {
        return this.data
          ? this.data[0]
          : {}
      },
      heads() {
        return Object.keys(this.first)
      },
      hasOnly() {
        return !!this.only
      }
    },
    methods: {
      isAllow(key) {
        if (this.hasOnly) {
            return this.only.includes(key)
        }
        return true
      }
    },
  }
</script>

<style scoped>
    .kp-table-data {
        width: 100%;
    }
    .kp-table-data-headline {
        color: var(--color-text);
        font-family: var(--font-family-sans);
        font-size: var(--font-size-medium);
        padding: 0 0 0.75rem;
        font-weight: 600;
    }
    .kp-table-data-element {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        table-layout: fixed;
        background-color: #ffffff;
        box-shadow: var(--box-shadow-item);
    }
    .kp-table-data-td {
        border-bottom: 1px solid var(--color-border);
        border-right: 1px solid var(--color-border);
        line-height: 1.25em;
        padding: 0.75rem;
        font-size: var(--font-size-small);
    }
    .kp-table-data-element tr:last-child td {
        border-bottom: 0;
    }
    .kp-table-data-td:last-child {
        border-right: 0;
    }
    .kp-table-data-head {
        font-weight: 600;
        background-color: #ffffff;
    }
</style>
