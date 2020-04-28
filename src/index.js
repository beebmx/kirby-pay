import View from "./components/View.vue";

panel.plugin("beebmx/kirby-pay", {
  views: {
    payments: {
      component: View,
      icon: "preview",
      label: "Payments"
    }
  }
});
