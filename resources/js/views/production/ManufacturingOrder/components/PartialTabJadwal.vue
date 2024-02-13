<template>
  <div class="app-container">
    <el-row>
      <el-col :span="16">
        <div class="calendar-holder">
          <full-calendar
            ref="fullCalendar"
            :events="fcEvents"
            locale="id_ID"
            :header="header"
          />
        </div>
      </el-col>
      <el-col :span="8">
        <el-card class="box-card">
          <div slot="header" class="clearfix">
            <span>Production this month</span>
          </div>
          <manufacturing-order-table />
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>
<script>
import fullCalendar from 'vue-fullcalendar';
import { fetchListSchedule } from '@/api/production/manufactur_order';
import ManufacturingOrderTable from './ManufacturingOrderTable';

export default {
  name: 'WorkstationList',
  components: { fullCalendar, ManufacturingOrderTable },
  filters: {},
  data() {
    return {
      listProduction: {},
      fcEvents: [],
      listLoading: false,
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
      },
    };
  },
  created() {
    this.getList();
  },
  methods: {
    getList() {
      this.listLoading = true;
      fetchListSchedule().then((response) => {
        response.data.forEach((element, index) => {
          var classStatus = ['calendar-primary'];
          if (element.status === 2) {
            classStatus = ['calendar-info'];
          } else if (element.status === 3) {
            classStatus = ['calendar-warning'];
          } else if (element.status === 4) {
            classStatus = ['calendar-success'];
          }
          this.fcEvents.push({
            title: element.name,
            start: element.production_date,
            end: element.finish_at,
            cssClass: classStatus,
          });
          this.listLoading = false;
        });
      });
    },
  },
};
</script>
<style>
.comp-full-calendar {
  max-width: 95% !important;
}

.calendar-primary {
  background-color: #ecf5ff !important;
  color: #409eff !important;
  border-radius: 4px;
}

.calendar-success {
  background-color: #f0f9eb !important;
  color: #67c23a !important;
  border-radius: 4px;
}

.calendar-info {
  background-color: #f4f4f5 !important;
  color: #909399 !important;
  border-radius: 4px;
}

.calendar-warning {
  background-color: #fdf6ec !important;
  color: #e6a23c !important;
  border-radius: 4px;
}

.calendar-danger {
  background-color: #fef0f0 !important;
  color: #f56c6c !important;
  border-radius: 4px;
}

.el-carousel__item h3 {
  color: #475669;
  font-size: 14px;
  opacity: 0.75;
  line-height: 200px;
  margin: 0;
}

.el-carousel__item:nth-child(2n) {
  background-color: #99a9bf;
}

.el-carousel__item:nth-child(2n + 1) {
  background-color: #d3dce6;
}
.calendar-holder {
  width: 100%;
  height: 600px;
}
</style>
