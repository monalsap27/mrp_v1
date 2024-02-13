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
      <router-link :to="'/vendor-shipping/create'">
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
          <span v-if="scope.row.date == null">
            <el-button
              type="success"
              size="small"
              @click="handleStartShippment(scope.row)"
            >
              <svg-icon icon-class="play" />
            </el-button>
          </span>
          <router-link :to="'/vendor-shipping/detailShipment/' + scope.row.id">
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
  </div>
</template>
<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import { fetchDeliveryOrder, StartShipment } from '@/api/vendor/delivery_order';

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
      listLoading: true,
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
      const { data, meta } = await fetchDeliveryOrder(this.query);
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
    handleStartShippment(data) {
      this.$confirm('Will you proceed with the shipment ? ', {
        confirmButtonText: 'Yes, confirm it!',
        cancelButtonText: 'No, cancel',
        type: 'warning',
      })
        .then(() => {
          StartShipment(data)
            .then((response) => {
              this.$message({
                type: 'success',
                message: 'This product has been approved successfully',
              });
              this.$router.push({ path: '/vendor-request/list' });
            })
            .catch((error) => {
              console.log(error);
            });
        })
        .catch(() => {
          this.$message({
            type: 'info',
            message: 'Approval has been canceled',
          });
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
.el-button--small {
  border-radius: 10px;
}
</style>
