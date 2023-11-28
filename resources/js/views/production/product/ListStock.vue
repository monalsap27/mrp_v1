<template>
  <div class="app-container">
    <el-tabs type="border-card" class="demo-tabs">
      <el-tab-pane>
        <template #label>
          <span class="custom-tabs-label">
            <el-tooltip content="I make this product" placement="top">
              <span>Product</span>
            </el-tooltip>
          </span>
        </template>
        <partial-tab-stock-product />
      </el-tab-pane>
      <el-tab-pane>
        <template #label>
          <span class="custom-tabs-label">
            <el-tooltip content="I make this product" placement="top">
              <span>Material</span>
            </el-tooltip>
          </span>
        </template>
        <partial-tab-stock-material />
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
import PartialTabStockProduct from './components/PartialTabStockProduct';
import PartialTabStockMaterial from './components/PartialTabStockMaterial';

export default {
  name: 'UnitList',
  components: {
    PartialTabStockProduct,
    PartialTabStockMaterial,
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
