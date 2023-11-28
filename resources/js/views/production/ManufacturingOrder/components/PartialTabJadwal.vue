<template>
  <div>
    <div class="app-container">
      <full-calendar ref="calendar" :events="fcEvents" locale="en" />
    </div>
  </div>
</template>
<script>
import fullCalendar from 'vue-fullcalendar';
import { fetchList } from '@/api/production/manufactur_order';

export default {
  name: 'WorkstationList',
  components: { fullCalendar },
  filters: {},
  data() {
    return {
      fcEvents: [],
      selectedEvent: null,
    };
  },
  created() {
    this.getList();
  },
  methods: {
    async getList() {
      await fetchList().then((response) => {
        response.data.forEach((element, index) => {
          this.fcEvents.push({
            title: element.name,
            start: element.production_date,
            end: element.production_date,
          });
        });
        this.loadingEdit = false;
      });
    },
  },
};
</script>
