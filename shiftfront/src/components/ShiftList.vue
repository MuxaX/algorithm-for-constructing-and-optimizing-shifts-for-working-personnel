<template>
  <div>
    <div class="date-input-container">
      <input type="date" v-model="selectedDate" placeholder="Select Date" />
      <button @click="fetchShiftList">Показать список смен</button>
    </div>
    <EasyDataTable
      :headers="headers"
      :items="items"
      table-class-name="custom-table"
    />
    <button
      class="generate-shifts-button"
      @click="
        $router.push({
          name: 'createshift',
        })
      "
    >
      Создать смены
    </button>
  </div>
</template>

<script>
import orderStatistic from "@/services/data_import";
import "vue3-easy-data-table/dist/style.css";

export default {
  name: "ShiftList",
  data() {
    return {
      headers: [
        { text: "Name", value: "name" },
        { text: "Date Start", value: "date_start" },
        { text: "Date End", value: "date_end" },
        { text: "Assignment Date", value: "assignment_date" },
        { text: "Profession Name", value: "profession_name" },
      ],
      items: [],
      selectedDate: "2024-07-29", // Initial date value
    };
  },
  mounted() {
    this.fetchShiftList();
  },
  methods: {
    async fetchShiftList() {
      const response = await orderStatistic.getShiftList(this.selectedDate);
      this.items = response.data.map((item) => {
        return {
          name: item.name,
          date_start: item.date_start,
          date_end: item.date_end,
          assignment_date: item.assignment_date,
          profession_name: item.profession_name,
        };
      });
    },
    generateShifts() {
      // Add your logic here to generate shifts
      console.log("Generate Shifts button clicked");
    },
  },
};
</script>

<style>
.custom-table {
  --easy-table-border: 1px solid #dadada;
  --easy-table-row-border: 1px solid #dadada;
  --easy-table-header-font-size: 14px;
  --easy-table-header-height: 50px;
  --easy-table-header-font-color: #fff;
  --easy-table-header-background-color: #00b661;
  --easy-table-header-item-padding: 10px 15px;
  --easy-table-body-even-row-font-color: #2b2b2b;
  --easy-table-body-even-row-background-color: #dadada;
  --easy-table-body-row-font-color: #363636;
  --easy-table-body-row-background-color: #dadada;
  --easy-table-body-row-height: 50px;
  --easy-table-body-row-font-size: 14px;
  --easy-table-body-row-hover-font-color: #2d3a4f;
  --easy-table-body-row-hover-background-color: #eee;
  --easy-table-body-item-padding: 10px 15px;
  --easy-table-footer-background-color: #dadada;
  --easy-table-footer-font-color: #373737;
  --easy-table-footer-font-size: 14px;
  --easy-table-footer-padding: 0px 10px;
  --easy-table-footer-height: 50px;
  --easy-table-rows-per-page-selector-width: 70px;
  --easy-table-rows-per-page-selector-option-padding: 10px;
  --easy-table-rows-per-page-selector-z-index: 1;
  --easy-table-scrollbar-track-color: #dadada;
  --easy-table-scrollbar-color: #dadada;
  --easy-table-scrollbar-thumb-color: #4c5d7a;
  --easy-table-scrollbar-corner-color: #dadada;
  --easy-table-loading-mask-background-color: #dadada;
}

.date-input-container {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
}

.date-input-container input[type="date"] {
  padding: 10px;
  margin-right: 10px;
  border: 1px solid #dadada;
  border-radius: 5px;
}

.date-input-container button {
  padding: 10px 20px;
  background-color: #00b661;
  color: #fff;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.date-input-container button:hover {
  background-color: #00a560;
}

.generate-shifts-button {
  padding: 10px 20px;
  background-color: #00b661;
  color: #fff;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  margin-top: 20px;
}

.generate-shifts-button:hover {
  background-color: #00a560;
}
</style>
