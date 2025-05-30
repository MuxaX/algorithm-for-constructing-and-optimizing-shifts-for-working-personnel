import { createApp } from "vue";
import App from "./App.vue";
import { createRouter, createWebHistory } from "vue-router";
import Formuls from "./components/Formuls.vue";
import ViewFormuls from "./components/ViewFormuls.vue";
import ShiftList from "./components/ShiftList.vue";
import CreateShift from "./components/CreateShift.vue";
import EasyDataTable from "vue3-easy-data-table"; // Import the component
import "vue3-easy-data-table/dist/style.css"; // Import the CSS
//import "vuetify/styles";
//import { createVuetify } from "vuetify";
//import * as components from "vuetify/components";
//import * as directives from "vuetify/directives";

const router = createRouter({
  routes: [
    {
      path: "/formuls",
      name: "formuls",
      component: Formuls,
    },
    {
      path: "/viewformuls",
      name: "viewformuls",
      component: ViewFormuls,
    },
    {
      path: "/shiftlist",
      name: "shiftlist",
      component: ShiftList,
    },
    {
      path: "/createshift",
      name: "createshift",
      component: CreateShift,
    },
  ],
  history: createWebHistory(),
});

/*const vuetify = createVuetify({
  components,
  directives,
});*/

const app = createApp(App);
app.component("EasyDataTable", EasyDataTable); // Register the component
app.use(router);
//app.use(vuetify);
app.mount("#app");
