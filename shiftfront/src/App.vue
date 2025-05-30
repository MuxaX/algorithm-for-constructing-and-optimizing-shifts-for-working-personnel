<!--App.vue-->
<template>
  <div>
    <OrderStatistic
      v-if="
        $route.name !== 'formuls' &&
        $route.name !== 'viewformuls' &&
        $route.name !== 'shiftlist' &&
        $route.name !== 'createshift'
      "
    />
    <TheFormuls v-else-if="$route.name == 'formuls'" />
    <ViewFormuls v-else-if="$route.name == 'viewformuls'" />
    <ShiftList v-else-if="$route.name == 'shiftlist'" />
    <CreateShift v-else-if="$route.name == 'createshift'" />
  </div>
</template>

<script>
import OrderStatistic from "./components/OrderStatistic.vue";
import TheFormuls from "./components/Formuls.vue";
import ViewFormuls from "./components/ViewFormuls.vue";
import ShiftList from "./components/ShiftList.vue";
import CreateShift from "./components/CreateShift.vue";

export default {
  name: "App",
  components: {
    OrderStatistic,
    TheFormuls,
    ViewFormuls,
    ShiftList,
    CreateShift,
  },
};
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
