<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input
        v-model="query.keyword"
        :placeholder="$t('table.keyword')"
        style="width: 200px"
        class="filter-item"
        @keyup.enter.native="handleFilter"
      />
      <el-button class="filter-item" type="primary" icon="el-icon-search">
        {{ $t('table.search') }}
      </el-button>
    </div>

    <el-table
      v-loading="listLoading"
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 100%"
    >
      <el-table-column align="center" label="Date" width="250">
        <template slot-scope="scope">
          <span>{{
            scope.row.created_at | parseTime('{d}-{m}-{y} {h}:{i}')
          }}</span>
        </template>
      </el-table-column>

      <el-table-column
        align="center"
        label="No Purchase"
        width="300px"
        prop="nomor"
      />
      <el-table-column
        align="center"
        label="Supplier"
        width="200px"
        prop="supplier_name"
      />

      <el-table-column
        align="center"
        label="Total Price"
        width="200px"
        prop="total_amount"
      />

      <el-table-column
        align="center"
        label="Created By"
        min-width="200px"
        prop="created_name"
      />

      <el-table-column align="center" label="Status" width="150px">
        <template slot-scope="scope">
          <span v-if="scope.row.status == 3">
            <el-tag
              :type="scope.row && scope.row.status | statusType"
              @click="showQTY(scope.row.id)"
            >
              {{ scope.row && scope.row.status | statusFilter }}
            </el-tag>
          </span>
          <span v-else>
            <el-tag :type="scope.row && scope.row.status | statusType">
              {{ scope.row && scope.row.status | statusFilter }}
            </el-tag>
          </span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Actions" width="150">
        <template #default="scope">
          <span v-if="scope.row.status == 1">
            <router-link
              :to="'/purchasing-approval/detailApproval/' + scope.row.id"
            >
              <el-button type="success" size="small">
                <svg-icon icon-class="question-square" />
              </el-button>
            </router-link>
          </span>
          <router-link :to="'/purchasing-order/detailOrder/' + scope.row.id">
            <el-button type="info" size="small">
              <svg-icon icon-class="eye-melek" />
            </el-button>
          </router-link>
        </template>
      </el-table-column>
    </el-table>
    <pagination
      v-show="total > 0"
      :total="total"
      :page.sync="query.page"
      :limit.sync="query.limit"
      @pagination="getList"
    />
    <el-drawer
      title="Detail Quantity Product"
      :visible.sync="drawerQuantity"
      direction="rtl"
      size="50%"
    >
      <el-table v-loading="loadingQty" :data="listQtyProduct">
        <el-table-column prop="product_code" label="Code" />
        <el-table-column prop="product_name" label="Product" />
        <el-table-column
          prop="qty_request"
          label="Quantity Request"
          width="150"
        />
        <el-table-column prop="qty" label="Fulfilled Quantity" width="150" />
      </el-table>
    </el-drawer>
  </div>
</template>
<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import { fetchListApproval } from '@/api/purchasing/order';
import { showQtyVendor } from '@/api/vendor/request';

export default {
  name: 'SupplierList',
  components: { Pagination },
  filters: {
    statusType(status) {
      const statusMap = {
        0: 'danger',
        1: 'info',
        2: 'success',
        3: 'warning',
        6: 'success',
      };
      return statusMap[status];
    },
    statusFilter(status) {
      const statusMap = {
        0: 'Rejected',
        1: 'Waiting',
        2: 'Approved',
        3: 'Partial Confirmation',
        4: 'Delivery',
        6: 'Done',
      };
      return statusMap[status];
    },
    orderNoFilter(str) {
      return str;
    },
  },
  data() {
    return {
      total: 0,
      list: null,
      listLoading: true,
      loadingQty: true,
      drawerQuantity: false,
      listQtyProduct: null,
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        role: '',
        sort: '-id',
      },
      rules: {
        note: [
          { required: true, message: 'Note is required', trigger: 'blur' },
        ],
      },
    };
  },
  created() {
    this.getList();
  },
  methods: {
    async getList() {
      const { limit, page } = this.query;
      this.listLoading = true;
      const { data, meta } = await fetchListApproval(this.query);
      this.list = data;
      this.list.forEach((element, index) => {
        element['index'] = (page - 1) * limit + index + 1;
      });
      this.total = meta.total;
      this.listLoading = false;
    },
    handleFilter() {
      this.query.page = 1;
      this.getList();
    },
    sortChange(data) {
      const { prop, order } = data;
      if (prop === 'id') {
        this.sortByID(order);
      }
    },
    sortByID(order) {
      if (order != null) {
        if (order === 'ascending') {
          this.query.sort = '+id';
        } else {
          this.query.sort = '-id';
        }
        this.handleFilter();
      }
    },
    showQTY(row) {
      this.drawerQuantity = true;
      this.loadingQty = true;
      showQtyVendor(row).then((response) => {
        this.listQtyProduct = response.data;
        this.loadingQty = false;
      });
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
.el-tag {
  cursor: pointer;
}
</style>
