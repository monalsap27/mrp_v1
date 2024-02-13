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
      <el-table-column align="center" label="Supplier">
        <template slot-scope="scope">
          <span>{{ scope.row.supplier_name }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Date">
        <template slot-scope="scope">
          <span>{{ scope.row.date }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Nomor" prop="nomor" />

      <el-table-column
        align="center"
        label="Received Date"
        width="200px"
        prop="received_date"
      />

      <el-table-column align="center" label="Status">
        <template slot-scope="scope">
          <el-tag :type="scope.row && scope.row.status | statusType">
            {{ scope.row && scope.row.status | statusFilter }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Actions" width="150">
        <template #default="scope">
          <span v-if="scope.row.status == 4">
            <el-button
              type="success"
              size="small"
              @click="showInboundDetail(scope.row.id)"
            >
              <svg-icon icon-class="stop" />
            </el-button>
          </span>
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
      ref="drawer"
      title="Detail"
      :before-close="handleClose"
      :visible.sync="drawerDelivery"
      direction="btt"
      size="60%"
      custom-class="demo-drawer"
    >
      <div class="demo-drawer__content">
        <el-form :model="newInbound">
          <el-table
            v-loading="loadingInbound"
            :data="newInbound.items"
            height="300"
          >
            <el-table-column prop="nomor" label="Nomor PO" />
            <el-table-column prop="date" label="Date">
              <template slot-scope="scope">
                <span>{{
                  scope.row.date | parseTime('{d}-{m}-{y} {h}:{i}')
                }}</span>
              </template>
            </el-table-column>
            <el-table-column prop="product_code" label="Code" />
            <el-table-column prop="product_name" label="Product" />
            <el-table-column prop="qty" label="Quantity" />
            <el-table-column label="Quantity Received">
              <template slot-scope="scope">
                <el-form-item prop="qty">
                  <el-input-number
                    v-model="scope.row.qty_confirm"
                    placeholder="type qty here"
                    :min="1"
                  />
                </el-form-item>
              </template>
            </el-table-column>
          </el-table>
        </el-form>
        <br>
        <br>
        <div class="demo-drawer__footer">
          <el-button @click="cancelForm">Cancel</el-button>
          <el-button
            type="primary"
            :loading="loading"
            @click="$refs.drawer.closeDrawer()"
          >{{ loading ? 'Submitting ...' : 'Submit' }}</el-button>
        </div>
      </div>
    </el-drawer>
  </div>
</template>
<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import { fetchDeliveryBySupplier } from '@/api/vendor/delivery_order';
import {
  showDataDeliveryInbound,
  confirmInboundPO,
} from '@/api/purchasing/order';

export default {
  name: 'SupplierList',
  components: { Pagination },
  filters: {
    statusType(status) {
      const statusMap = { 1: 'info', 0: 'danger', 2: 'success', 6: 'success' };
      return statusMap[status];
    },
    statusFilter(status) {
      const statusMap = {
        1: 'Waiting',
        2: 'Approved',
        4: 'Delivery',
        6: 'Done',
        0: 'Rejected',
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
      loadingInbound: false,
      listLoading: true,
      drawerDelivery: false,
      dialog: false,
      formLabelWidth: '80px',
      loading: false,
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
      newInbound: { items: [] },
    };
  },
  created() {
    this.getList();
  },
  methods: {
    async getList() {
      const { limit, page } = this.query;
      this.listLoading = true;
      const { data, meta } = await fetchDeliveryBySupplier(this.query);
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
    showInboundDetail(row) {
      this.drawerDelivery = true;
      this.loadingInbound = true;
      showDataDeliveryInbound(row).then((response) => {
        response.data.forEach((element, index) => {
          this.newInbound.items.push({
            qty: element.qty,
            date: element.date,
            nomor: element.nomor,
            qty_confirm: element.qty,
            unit_price: element.unit_price,
            product_id: element.product_id,
            product_name: element.product_name,
            product_code: element.product_code,
            order_detail_id: element.order_detail_id,
            delivery_order_id: element.delivery_order_id,
          });
        });
        this.loadingInbound = false;
      });
    },
    handleClose(done) {
      if (this.loading) {
        return;
      }
      this.$confirm('Do you want to submit?')
        .then((_) => {
          this.loading = true;
          confirmInboundPO(this.newInbound)
            .then((response) => {
              this.$message({
                type: 'success',
                message: 'This PO has been been well received',
              });
              this.handleFilter();
              this.$router.push({ path: '/purchasing-inbound/list' });
              this.loading = false;
              this.drawerDelivery = false;
            })
            .catch((error) => {
              console.log(error);
            });
        })
        .catch((_) => {});
    },
    cancelForm() {
      this.loading = false;
      this.dialog = false;
      clearTimeout(this.timer);
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
.el-button--small {
  border-radius: 10px;
}
</style>
