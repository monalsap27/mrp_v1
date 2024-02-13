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
      <el-button
        class="filter-item"
        type="primary"
        icon="el-icon-search"
        @click="handleFilter"
      >
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
      <el-table-column align="center" label="Request Date" width="250">
        <template slot-scope="scope">
          <span>{{
            scope.row.created_at | parseTime('{d}-{m}-{y} {h}:{i}')
          }}</span>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="Name"
        width="300px"
        prop="product_name"
      />
      <el-table-column
        align="center"
        label="Quantity"
        width="100px"
        prop="qty"
      />

      <el-table-column
        align="center"
        label="Supplier"
        width="200px"
        prop="supplier_name"
      />

      <el-table-column
        align="center"
        label="Request By"
        min-width="200px"
        prop="request_by"
      />

      <el-table-column align="center" label="Status" width="150px">
        <template slot-scope="scope">
          <el-tag :type="scope.row && scope.row.status | statusType">
            {{ scope.row && scope.row.status | statusFilter }}
          </el-tag>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Actions" width="100">
        <template slot-scope="scope">
          <el-tooltip
            class="item"
            effect="dark"
            content="Reject"
            placement="top-start"
          >
            <el-button
              :disabled="scope.row.status != 1"
              type="danger"
              size="small"
              @click="handleReject(scope.row)"
            >
              <svg-icon icon-class="vote-nay" />
            </el-button>
          </el-tooltip>
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
    <el-dialog
      :title="'Reject PO '"
      :visible.sync="dialogRejectVisible"
      width="30%"
    >
      <div v-loading="loadingReject" class="form-container">
        <el-form
          ref="supplierFormEdit"
          :rules="rules"
          :model="dataReject"
          label-position="left"
          label-width="150px"
          style="max-width: 100%"
        >
          <el-form-item :label="$t('Note')" prop="name">
            <el-input
              v-model="dataReject.note"
              :rows="2"
              type="textarea"
              placeholder="Please input"
            />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogRejectVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="saveReject()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import { fetchList, storeReject } from '@/api/purchasing/submit';

export default {
  name: 'SupplierList',
  components: { Pagination },
  filters: {
    statusType(status) {
      const statusMap = { 1: 'info', 3: 'danger', 2: 'success' };
      return statusMap[status];
    },
    statusFilter(status) {
      const statusMap = {
        1: 'Waiting',
        2: 'Approved',
        3: 'Rejected',
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
      loadingReject: false,
      dialogRejectVisible: false,
      dataReject: {},
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

    saveReject() {
      this.loadingReject = true;
      storeReject(this.dataReject)
        .then((response) => {
          this.$message({
            message: 'This request has been rejected successfully',
            type: 'success',
            duration: 5 * 1000,
          });
          this.dialogRejectVisible = false;
          this.handleFilter();
        })
        .catch((error) => {
          console.log(error);
        })
        .finally(() => {
          this.loadingReject = false;
        });
    },
    async handleReject(data) {
      this.dataReject.id = data.id;
      this.dialogRejectVisible = true;
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
</style>
