import Vue from "vue";
import DbBrowserComponent from "./DbBrowserComponent.vue";
import Toasted from "vue-toasted";
const options = {
  theme: "toasted-primary",
  position: "top-right",
  duration: 3000
};
Vue.use(Toasted, options);
new Vue({
  el: "#dbBrowser",
  render: h => h(DbBrowserComponent)
});
