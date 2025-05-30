<!--OrderStatistic-->
<template>
  <div class="container">
    <h1 class="title">Все заказы</h1>
    <div class="all-users-section">
      <div v-for="(order_unit, index) in order_count_list" :key="index">
        <!-- Display order details here -->
      </div>
    </div>
    <div id="chart" :style="{ height: '300px', width: '50%' }"></div>
    <div class="button-section">
      <select name="combo_order" v-model="selectedType" class="select-input">
        <option
          v-for="order_type in listTypeOrder"
          :key="order_type.order_type_id"
          :value="order_type.order_type_id"
        >
          {{ order_type.order_type_name }}
        </option>
      </select>
      <input
        type="date"
        v-model="selectedDate"
        class="date-input"
        placeholder="Select Date"
      />
      <button @click="orderStatlist" class="green-button">
        Показать статистику
      </button>
      <button
        @click="$router.push({ name: 'viewformuls' })"
        class="green-button"
      >
        Просмотр формул
      </button>
      <button @click="$router.push({ name: 'shiftlist' })" class="green-button">
        Просмотр смен
      </button>
    </div>
  </div>
</template>

<script>
import orderStatistic from "@/services/data_import.js";
import ApexCharts from "apexcharts";

export default {
  name: "TheOrderStatistic",
  props: {
    msg: String,
  },
  data() {
    return {
      order_count_list: [],
      chart: null,
      selectedDate: "", // Initial date value
      listTypeOrder: null,
      selectedType: null,
    };
  },

  methods: {
    orderStatlist() {
      if (!this.selectedDate) {
        console.error("Please select a date");
        return;
      }

      if (!this.selectedType) {
        this.selectedType = 0;
      }

      orderStatistic
        .getOrderStat(this.selectedDate, this.selectedType)
        .then((response) => {
          if (response.data && response.data.length > 0) {
            this.order_count_list = response.data;
            this.renderChart();
            console.log(response.data);
          } else {
            console.log("No data received for the selected date.");
          }
        })
        .catch((e) => {
          console.log(e);
        });
    },

    renderChart() {
      if (this.chart) {
        this.chart.destroy();
      }

      if (this.order_count_list.length === 0) {
        console.log("No data to render chart.");
        return;
      }

      const options = {
        chart: {
          type: "bar",
          id: "vuechart-example",
        },
        series: [
          {
            name: "sales",
            data: this.order_count_list.map((item) => item.order_count),
          },
        ],
        xaxis: {
          categories: this.order_count_list.map((item) => item.order_hour),
        },
      };

      this.chart = new ApexCharts(document.querySelector("#chart"), options);
      this.chart.render();
    },
    getOrderTypes() {
      orderStatistic
        .getOrderType()
        .then((response) => {
          this.listTypeOrder = response.data;
          console.log(response.data);
        })
        .catch((e) => {
          console.log(e);
        });
    },
  },
  mounted() {
    this.getOrderTypes();
    // Optionally set a default date and fetch data
    // this.selectedDate = '2024-07-29';
    // this.orderStatlist();
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.container {
  max-width: 800px;
  margin: 40px auto;
  padding: 20px;
  background-color: #f9f9f9;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.title {
  text-align: center;
  margin-bottom: 20px;
}

.all-users-section {
  margin-bottom: 20px;
}

.select-input,
.date-input {
  padding: 10px;
  margin-bottom: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.button-section {
  margin-top: 20px;
}

.green-button {
  background-color: #34c759;
  color: #fff;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  margin: 10px 0;
  display: block; /* Ensure buttons are on separate lines */
}

.green-button:hover {
  background-color: #2ecc71;
}
</style>
