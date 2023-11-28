<template>
  <div class="app-container">
    <div class="filter-container" />

    <el-tabs type="border-card" class="demo-tabs">
      <el-tab-pane>
        <template #label>
          <span class="custom-tabs-label">
            <el-tooltip content="I make this product" placement="top">
              <span>Stock IN </span>
            </el-tooltip>
          </span>
        </template>
        <el-table
          v-loading="listLoading"
          :data="listStockIN"
          border
          fit
          highlight-current-row
          style="width: 100%"
        >
          <el-table-column
            align="center"
            label="Date"
            width="180"
            prop="date"
          />
          <el-table-column align="center" label="Control ID">
            <template slot-scope="scope">
              <span>{{ scope.row.control_id }} </span>
            </template>
          </el-table-column>
          <el-table-column align="center" label="QR Code" width="120">
            <template slot-scope="scope">
              <qr-code :text="scope.row.control_id" :size="100" />
            </template>
          </el-table-column>
          <el-table-column
            align="right"
            label="Harga Jual"
            width="200px"
            prop="harga_jual"
          />
          <el-table-column
            align="right"
            label="Harga Beli"
            width="200px"
            prop="harga_beli"
          />
          <el-table-column
            align="center"
            label="Description"
            prop="description"
          />
        </el-table>
        <pagination
          v-show="totalIN > 0"
          :total="totalIN"
          :page.sync="queryIN.page"
          :limit.sync="queryIN.limit"
          @pagination="getListStockIn"
        />
      </el-tab-pane>
      <el-tab-pane>
        <template #label>
          <span class="custom-tabs-label">
            <el-tooltip content="I make this product" placement="top">
              <span>Stock OUT</span>
            </el-tooltip>
          </span>
        </template>
        <el-table
          v-loading="listLoading"
          :data="listStockOUT"
          border
          fit
          highlight-current-row
          style="width: 100%"
        >
          <el-table-column
            align="center"
            label="Date"
            width="150"
            prop="date"
          />
          <el-table-column
            align="center"
            label="Control ID"
            prop="control_id"
          />
          <el-table-column
            align="right"
            label="Harga Jual"
            width="200px"
            prop="harga_jual"
          />
          <el-table-column
            align="right"
            label="Harga Beli"
            width="200px"
            prop="harga_beli"
          />
          <el-table-column
            align="center"
            label="Description"
            prop="description"
          />
        </el-table>
        <pagination
          v-show="totalOUT > 0"
          :total="totalOUT"
          :page.sync="queryOUT.page"
          :limit.sync="queryOUT.limit"
          @pagination="getListStockIn"
        />
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import { showMutasi as showStock } from '@/api/production/stock';

export default {
  name: 'WorkstationList',
  components: { Pagination },
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
    return {
      totalIN: 0,
      totalOUT: 0,
      list: null,
      listStockIN: null,
      listStockOUT: null,
      listLoading: true,
      dialogViewVisible: false,
      queryIN: {},
      queryOUT: {},
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        role: '',
        sort: '-id',
      },
    };
  },
  computed: {},

  created() {
    const id = this.$route.params && this.$route.params.id;
    this.query.product_id = id;
    this.getListStockIn();
    this.getListStockOut();
  },
  methods: {
    async getListStockIn() {
      this.queryIN = this.query;
      this.queryIN.type = 1;
      const { limit, page } = this.queryIN;
      this.listLoading = true;
      const { data, meta } = await showStock(this.queryIN);
      this.listStockIN = data;
      this.listStockIN.forEach((element, index) => {
        element['index'] = (page - 1) * limit + index + 1;
      });
      this.totalIN = meta.total;
      this.listLoading = false;
    },
    async getListStockOut() {
      this.queryOUT = this.query;
      this.queryOUT.type = 2;
      const { limit, page } = this.queryOUT;
      this.listLoading = true;
      const { data, meta } = await showStock(this.queryOUT);
      this.listStockOUT = data;
      this.listStockOUT.forEach((element, index) => {
        element['index'] = (page - 1) * limit + index + 1;
      });
      this.totalOUT = meta.total;
      this.listLoading = false;
    },
  },
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
