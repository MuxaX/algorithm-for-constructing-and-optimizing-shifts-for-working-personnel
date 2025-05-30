<template>
  <div>
    <h1>Все формулы:</h1>
    <div v-for="(profession, index) in profession_list" :key="index">
      {{ profession.profession_name }} : {{ profession.formula }}
    </div>
    <div>
      <button
        @click="
          $router.push({
            name: 'formuls',
          })
        "
      >
        Новая формула
      </button>
    </div>
  </div>
</template>

<script>
import orderStatistic from "@/services/data_import";
export default {
  name: "ViewFormuls",
  props: {
    msg: String,
  },
  data() {
    return {
      profession_list: [],
    };
  },

  methods: {
    listFormuls() {
      orderStatistic
        .getAllFormul()
        .then((response) => {
          this.profession_list = response.data;
          //this.renderChart();
          console.log(response.data);
        })
        .catch((e) => {
          console.log(e);
        });
    },
  },
  mounted() {
    this.listFormuls();
  },
};
</script>
