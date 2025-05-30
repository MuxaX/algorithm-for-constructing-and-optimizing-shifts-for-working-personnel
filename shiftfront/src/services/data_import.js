//data_import.js
import http from "../http-common";

class orderStatistic {
  getOrderStat(date, type) {
    return http.get(`/orderstat/${date}/${type}`);
  }
  getAllFormul() {
    return http.get("/allformul");
  }
  getOrderType() {
    return http.get("/datatypeorder");
  }
  getProfession() {
    return http.get("/dataprofession");
  }
  getIdProfession(name) {
    return http.get(`/getidprof/${name}`);
  }
  insertFormule(prof, formuls) {
    return http.post("/createformule", {
      profession_id: prof,
      formula: formuls,
    });
  }
  getAverTime(id, count_day) {
    return http.get(`/avertime/${id}/${count_day}`);
  }
  getShiftList(selectedDate) {
    return http.get(`/shiftlist/${selectedDate}`);
  }

  createShifts(profession_id, count_day, date_start, count_day_end) {
    return http.post("/createshift", {
      profession_id: profession_id,
      count_day: count_day,
      date_start: date_start,
      count_day_end: count_day_end,
    });
  }

  getCountDays(profession_id) {
    return http.get(`/countday/${profession_id}`);
  }
}

export default new orderStatistic();
