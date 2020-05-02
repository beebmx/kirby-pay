<template>
    <div class="kp-table-key-pair">
        <h3 class="kp-table-key-pair-headline" v-text="title"></h3>
        <table class="kp-table-key-pair-element">
            <tbody>
                <tr v-if="canDisplay(id, value)" v-for="(value, id) in data" :key="id">
                    <td class="kp-table-key-pair-td kp-table-key-pair-id" v-text="$t(`beebmx.kirby-pay.table.${id}`)"></td>
                    <td v-if="isNotImage(id)" class="kp-table-key-pair-td" v-text="value"></td>
                    <td v-else class="kp-table-key-pair-td"><img :src="value" /></td>
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
      exclude: Array,
      images: Array,
    },
    methods: {
      canDisplay(key, value) {
        if (this.exclude) {
          return !this.exclude.includes(key) && !!value
        }

        return !!value;
      },
      isNotImage(key) {
        if (this.images) {
          return !this.images.includes(key)
        }

        return true;
      },
    }
  }
</script>

<style scoped>
    .kp-table-key-pair {
        width: 100%;
    }
    .kp-table-key-pair-headline {
        color: var(--color-text);
        font-family: var(--font-family-sans);
        font-size: var(--font-size-medium);
        padding: 0 0 0.75rem;
        font-weight: 600;
    }
    .kp-table-key-pair-element {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        background-color: #ffffff;
        box-shadow: var(--box-shadow-item);
    }
    .kp-table-key-pair-td {
        border-bottom: 1px solid var(--color-border);
        border-right: 1px solid var(--color-border);
        line-height: 1.25em;
        padding: 0.75rem;
        font-size: var(--font-size-small);
    }
    .kp-table-key-pair-element tr:last-child td {
        border-bottom: 0;
    }
    .kp-table-key-pair-td:last-child {
        border-right: 0;
    }
    .kp-table-key-pair-id {
        font-weight: 600;
        width: 25%;
    }
</style>