<!--CreateShift.vue-->
<template>
  <div class="container">
    <h1 class="title">Создание смены</h1>
    <div class="form-section">
      <p class="label">Выберите профессию</p>
      <select
        name="combo_profession"
        v-model="selectedProf"
        class="select-input"
      >
        <option value="" disabled selected>Выберите профессию</option>
        <option
          v-for="profes in listProf"
          :key="profes.profession_id"
          :value="profes.profession_id"
        >
          {{ profes.profession_name }}
        </option>
      </select>
    </div>
    <!-- <div class="form-section">
      <p class="label">Количество рабочих часов за смену</p>
      <input
        type="number"
        v-model="selectedTime"
        class="input-field"
        placeholder="Select Date"
      />
    </div>-->
    <div class="form-section">
      <p class="label">Рабочий период с:</p>
      <input
        type="date"
        v-model="selectedDateStart"
        class="input-field"
        placeholder="Select date start"
      />
      <p class="label">По:</p>
      <input
        type="date"
        v-model="selectedDateEnd"
        class="input-field"
        placeholder="Select Date end"
      />
    </div>
    <!--<div class="form-section">
      <p class="label">Рабочий график (введите через "/")</p>
      <input
        type="text"
        v-model="selectedSchedule"
        class="input-field"
        placeholder="Enter schedule"
      />
    </div>-->
    <button @click="formShift" class="green-button">Сформировать смены</button>
    <!--<div v-if="countday">
      <p>{{ countday.count_day }}</p>
    </div>-->
  </div>
</template>

<script>
import orderStatistic from "@/services/data_import";
export default {
  name: "CreateShift",
  props: {
    msg: String,
  },
  data() {
    return {
      listProf: null,
      selectedProf: null,
      selectedTime: 0,
      selectedDateStart: "",
      selectedDateEnd: "",
      selectedSchedule: "",
      countday: null,
    };
  },
  watch: {
    countday(newValue) {
      if (newValue) {
        console.log("COUNT DAYS:", newValue.count_day);
        // Optionally call formShift here if you want to proceed immediately after data is fetched
        this.formShift();
      }
    },
  },
  methods: {
    getProfessions() {
      orderStatistic
        .getProfession()
        .then((response) => {
          this.listProf = response.data;
          console.log(response.data);
        })
        .catch((e) => {
          console.log(e);
        });
    },

    parsesDays() {
      orderStatistic
        .getCountDays(this.selectedProf)
        .then((response) => {
          this.countday = response.data;
          console.log("Formula created successfully:", response);
          this.errors = null;
        })
        .catch((error) => {
          if (error.response) {
            this.errors = error.response.data.errors;
            console.error("Validation Error:", error.response.data.errors);
          } else {
            console.error("Error:", error);
          }
        });
    },
    formShift() {
      if (!this.countday) {
        this.parsesDays();
        return;
      }
      // Calculate the difference between selectedDateEnd and selectedDateStart
      const startDate = new Date(this.selectedDateStart);
      const endDate = new Date(this.selectedDateEnd);
      const differenceInMs = endDate.getTime() - startDate.getTime();
      const daysDifference = Math.round(differenceInMs / (24 * 60 * 60 * 1000));
      console.log("COUNT TIME:", daysDifference);
      console.log("COUNT DAYS:", this.countday.count_day);
      console.log("prof:", this.selectedProf);
      console.log("dateStart:", this.selectedDateStart);
      orderStatistic
        .createShifts(
          this.selectedProf,
          this.countday.count_day,
          this.selectedDateStart,
          daysDifference
        )
        .then((response) => {
          console.log("Shifts created successfully:", response);
        })
        .catch((error) => {
          console.error("Error creating shifts:", error);
        });
    },
  },

  mounted() {
    this.getProfessions();
  },
};
</script>

<style scoped>
.container {
  max-width: 600px;
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

.form-section {
  margin-bottom: 20px;
}

.label {
  margin-bottom: 10px;
}

.select-input,
.input-field {
  width: 100%;
  padding: 10px;
  margin-bottom: 20px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.green-button {
  background-color: #34c759;
  color: #fff;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.green-button:hover {
  background-color: #2ecc71;
}
</style>
