<!--Formuls-->
<template>
  <div class="container">
    <h1 class="title">Составить формулу</h1>
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
      <p class="label">Выберите тип зказа для профессии</p>
      <select name="combo_order" v-model="selectedType" class="select-input">
        <option
          v-for="order_type in listTypeOrder"
          :key="order_type.order_type_id"
          :value="order_type.order_type_id"
        >
          {{ order_type.order_type_name }}
        </option>
      </select>
      <input type="number" v-model="CountDay" class="input-field" />
      <button @click="learnAverrageTime" class="green-button">
        Среднее время на заказ
      </button>
    </div>
    <div v-if="averageTime" class="result-section">
      <p>Среднее время на заказ: {{ averageTime.average_time }}</p>
      <p>Количество заказов: {{ averageTime.order_count }}</p>
    </div>
    <div class="form-section">
      <p class="label">
        X = количество заказов с данным типом, Y = количество минут, затраченное
        на заказ
      </p>
      <p class="label">Введите формулу</p>
      <input type="text" v-model="formulValue" class="input-field" />
    </div>
    <button @click="insertFormul" class="green-button">Создать формулу</button>
    <div v-if="errors" class="error-section">
      <ul>
        <li v-for="(error, key) in errors" :key="key">{{ error }}</li>
      </ul>
    </div>
  </div>
</template>

<script>
import orderStatistic from "@/services/data_import";
export default {
  name: "TheFormuls",
  data() {
    return {
      listTypeOrder: null,
      listProf: null,
      selectedProf: null,
      selectedType: null,
      formulValue: "",
      errors: null,
      averageTime: null,
      CountDay: 0,
    };
  },
  mounted() {
    this.getOrderTypes();
    this.getProfessions();
  },
  methods: {
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
    insertFormul() {
      if (!this.selectedProf || !this.formulValue) {
        alert("Please select a profession and enter a formula.");
        return;
      }
      orderStatistic
        .getIdProfession(this.selectedProf)
        .then((response) => {
          let id_prof = response.data;
          id_prof + 1;
          orderStatistic
            .insertFormule(this.selectedProf, this.formulValue)
            .then((response) => {
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
        })
        .catch((error) => {
          console.error("Error getting profession ID:", error);
        });
    },
    learnAverrageTime() {
      orderStatistic
        .getAverTime(this.selectedType, this.CountDay)
        .then((response) => {
          this.averageTime = response.data;
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

.result-section {
  margin-bottom: 20px;
}

.error-section {
  color: red;
}
</style>
