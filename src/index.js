import Payments from "./components/Payments.vue";
import Payment from "./components/Payment.vue";
import Customers from "./components/Customers.vue";
import Resources from "./store/resources"

panel.plugin("beebmx/kirby-pay", {
  created(Vue) {
    Vue.$router.addRoutes([
      {
        path: "/plugins/payment/:id",
        name: "Payment",
        meta: {
          view: "payments"
        },
        component: Payment
      },
    ])
    Vue.$store.registerModule("kpResources", Resources);
  },
  views: {
    payments: {
      component: Payments,
      icon: "money",
      menu: true,
    },
    payment: {
      component: Payment,
      icon: "money",
      menu: false,
    },
    customers: {
      component: Customers,
      icon: "users",
      menu: false,
    },
  },
  icons: {
    'money': '<path d="M2 0C1.46957 0 0.960859 0.210714 0.585786 0.585786C0.210714 0.960859 0 1.46957 0 2V6C0 6.53043 0.210714 7.03914 0.585786 7.41421C0.960859 7.78929 1.46957 8 2 8V2H12C12 1.46957 11.7893 0.960859 11.4142 0.585786C11.0391 0.210714 10.5304 0 10 0H2ZM4 6C4 5.46957 4.21071 4.96086 4.58579 4.58579C4.96086 4.21071 5.46957 4 6 4H14C14.5304 4 15.0391 4.21071 15.4142 4.58579C15.7893 4.96086 16 5.46957 16 6V10C16 10.5304 15.7893 11.0391 15.4142 11.4142C15.0391 11.7893 14.5304 12 14 12H6C5.46957 12 4.96086 11.7893 4.58579 11.4142C4.21071 11.0391 4 10.5304 4 10V6ZM10 10C10.5304 10 11.0391 9.78929 11.4142 9.41421C11.7893 9.03914 12 8.53043 12 8C12 7.46957 11.7893 6.96086 11.4142 6.58579C11.0391 6.21071 10.5304 6 10 6C9.46957 6 8.96086 6.21071 8.58579 6.58579C8.21071 6.96086 8 7.46957 8 8C8 8.53043 8.21071 9.03914 8.58579 9.41421C8.96086 9.78929 9.46957 10 10 10Z"/>',
  }
});
