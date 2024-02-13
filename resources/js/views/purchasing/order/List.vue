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
      <router-link :to="'/purchasing-order/create'">
        <el-button
          class="filter-item"
          style="margin-left: 10px"
          type="primary"
          icon="el-icon-plus"
        >
          {{ $t('table.add') }}
        </el-button>
      </router-link>
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
          <span v-else-if="scope.row.status == 4">
            <el-tag
              :type="scope.row && scope.row.status | statusType"
              @click="showDelivery(scope.row.id)"
            >
              {{ scope.row && scope.row.status | statusFilter }} :
              {{ scope.row.count_delivery }}
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
          <!-- <span v-if="scope.row.status == 1">
            <router-link
              :to="'/purchasing-approval/detailApproval/' + scope.row.id"
            >
              <el-button type="success" size="small">
                <svg-icon icon-class="question-square" />
              </el-button>
            </router-link>
          </span> -->
          <router-link :to="'/purchasing-order/detailOrder/' + scope.row.id">
            <el-button type="info" size="small">
              <svg-icon icon-class="eye-melek" />
            </el-button>
          </router-link>
          <router-link
            target="_blank"
            :to="'/purchasing-order/pdf/download/' + scope.row.id"
          >
            <el-button type="danger" size="small">
              <svg-icon icon-class="file-pdf" />
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

    <el-drawer
      v-loading="loadingQty"
      title="Detail Delivery Order"
      :visible.sync="drawerDelivery"
      direction="rtl"
      size="50%"
    >
      <el-card class="box-card">
        <div slot="header" class="clearfix">
          <span>Done</span>
        </div>
        <div class="text item">
          <el-table v-loading="loadingDelivery" :data="listDeliveryDone">
            <el-table-column prop="nomor" label="Nomor" />
            <el-table-column prop="date" label="Date" />
            <el-table-column
              prop="received_date"
              label="Received Date"
              width="150"
            />
          </el-table>
        </div>
      </el-card>

      <el-card class="box-card">
        <div slot="header" class="clearfix">
          <span>Process</span>
        </div>
        <div class="text item">
          <el-table v-loading="loadingDelivery" :data="listDeliveryInProcess">
            <el-table-column prop="nomor" label="Nomor" />
            <el-table-column prop="date" label="Date" />
            <el-table-column
              prop="received_date"
              label="Received Date"
              width="150"
            />
          </el-table>
        </div>
      </el-card>
    </el-drawer>
  </div>
</template>
<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import { fetchList, DeleteOrder } from '@/api/purchasing/order';
import { showQtyVendor } from '@/api/vendor/request';
import { ShowDeliveryByTransaction } from '@/api/vendor/delivery_order';

export default {
  name: 'SupplierList',
  components: { Pagination },
  filters: {
    statusType(status) {
      const statusMap = {
        0: 'danger',
        1: 'info',
        2: 'warning',
        3: 'warning',
        4: 'primary',
        5: 'primary',
        6: 'success',
        7: 'success',
      };
      return statusMap[status];
    },
    statusFilter(status) {
      const statusMap = {
        0: 'Rejected',
        1: 'Waiting',
        2: 'Confirmation',
        3: 'Partial Confirmation',
        4: 'Delivery',
        5: 'Partial Delivery',
        6: 'Done',
        7: 'Partial Done',
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
      listQtyProduct: null,
      listDeliveryDone: [],
      listDeliveryInProcess: [],
      loadingQty: false,
      listLoading: true,
      drawerQuantity: false,
      drawerDelivery: false,
      loadingDelivery: false,
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
      const { data, meta } = await fetchList(this.query);
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
    handleDelete(id, name) {
      this.$confirm('This will permanently delete  Continue?', 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      })
        .then(() => {
          DeleteOrder(id)
            .then((response) => {
              this.$message({
                type: 'success',
                message: 'Delete completed',
              });
              this.handleFilter();
            })
            .catch((error) => {
              console.log(error);
            });
        })
        .catch(() => {
          this.$message({
            type: 'info',
            message: 'Delete canceled',
          });
        });
    },
    showQTY(row) {
      this.drawerQuantity = true;
      this.loadingQty = true;
      showQtyVendor(row).then((response) => {
        this.listQtyProduct = response.data;
        this.loadingQty = false;
      });
    },
    showDelivery(row) {
      this.drawerDelivery = true;
      this.loadingQty = true;
      this.listDeliveryInProcess = [];
      this.listDeliveryDone = [];
      ShowDeliveryByTransaction(row).then((response) => {
        response.data.forEach((element, index) => {
          console.log(element);
          if (element.status === 4) {
            this.listDeliveryInProcess.push({
              date: element.date,
              nomor: element.nomor,
              received_date: element.received_date,
            });
          } else {
            this.listDeliveryDone.push({
              date: element.date,
              nomor: element.nomor,
              received_date: element.received_date,
            });
          }
          // this.newDelivery.items.push({
          //   transaction_id: element.transaction_id,
          //   product_id: element.product_id,
          //   qty: element.qty,
          //   product_name: element.product_name,
          //   nomor: element.nomor,
          //   product_code: element.product_code,
          // });
        });
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
