<template>
  <div class="app-container">
    <div class="filter-container" />
    <el-tabs type="border-card" class="demo-tabs">
      <el-tab-pane>
        <template #label>
          <span class="custom-tabs-label">
            <span>Jadwal Production</span>
          </span>
        </template>
        <partial-tab-jadwal />
      </el-tab-pane>
      <el-tab-pane>
        <template #label>
          <span class="custom-tabs-label">
            <span>Draft Production</span>
          </span>
        </template>
        <partial-tab-draft />
      </el-tab-pane>
      <el-tab-pane>
        <template #label>
          <span class="custom-tabs-label">
            <span>On Going</span>
          </span>
        </template>
        <partial-tab-on-going />
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
import PartialTabDraft from './components/PartialTabDraft';
import PartialTabOnGoing from './components/PartialTabOnGoing';
import PartialTabJadwal from './components/PartialTabJadwal';

export default {
  name: 'UnitList',
  components: {
    PartialTabDraft,
    PartialTabOnGoing,
    PartialTabJadwal,
  },
  filters: {
    statusFilter(status) {
      const statusMap = {
        published: 'success',
        draft: 'info',
        deleted: 'danger',
      };
      return statusMap[status];
    },
  },
  data() {
    return {};
  },
  created() {},
  methods: {},
};
</script>

<style lang="scss" scoped>
.edit-input {
  padding-right: 100px;
}
.cancel-btn {
  position: absolute;
  right: 15px;
  top: 10px;
}
.dialog-footer {
  text-align: left;
  padding-top: 0;
  margin-left: 150px;
}
.app-container {
  flex: 1;
  justify-content: space-between;
  font-size: 14px;
  padding-right: 8px;
  .block {
    float: left;
    min-width: 250px;
  }
  .clear-left {
    clear: left;
  }
}
</style>
