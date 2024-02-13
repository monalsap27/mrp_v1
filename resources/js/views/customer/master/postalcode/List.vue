<template>
  <div class="app-container">
    <el-tabs type="border-card" class="demo-tabs">
      <el-tab-pane>
        <template #label>
          <span class="custom-tabs-label">
            <el-tooltip content="Provinces" placement="top">
              <span>Provinces</span>
            </el-tooltip>
          </span>
        </template>
        <partial-tab-provinces />
      </el-tab-pane>
      <el-tab-pane>
        <template #label>
          <span class="custom-tabs-label">
            <el-tooltip content="Cities" placement="top">
              <span>Cities</span>
            </el-tooltip>
          </span>
        </template>
        <partial-tab-cities />
      </el-tab-pane>
      <el-tab-pane>
        <template #label>
          <span class="custom-tabs-label">
            <el-tooltip content="Districts" placement="top">
              <span>Districts</span>
            </el-tooltip>
          </span>
        </template>
        <partial-tab-districts />
      </el-tab-pane>
      <el-tab-pane>
        <template #label>
          <span class="custom-tabs-label">
            <el-tooltip content="Sub Districts" placement="top">
              <span>Sub Districts</span>
            </el-tooltip>
          </span>
        </template>
        <partial-tab-sub-districts />
      </el-tab-pane>
      <el-tab-pane>
        <template #label>
          <span class="custom-tabs-label">
            <el-tooltip content="Postal Code" placement="top">
              <span>Postal Code</span>
            </el-tooltip>
          </span>
        </template>
        <partial-tab-postal-code />
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
import PartialTabProvinces from './components/PartialTabProvinces';
import PartialTabCities from './components/PartialTabCities';
import PartialTabDistricts from './components/PartialTabDistricts';
import PartialTabSubDistricts from './components/PartialTabSubDistricts';
import PartialTabPostalCode from './components/PartialTabPostalCode';

export default {
  name: 'PostalCodeList',
  components: {
    PartialTabProvinces,
    PartialTabCities,
    PartialTabDistricts,
    PartialTabSubDistricts,
    PartialTabPostalCode,
  },
  filters: {},
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
