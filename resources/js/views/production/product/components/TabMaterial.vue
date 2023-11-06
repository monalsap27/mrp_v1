<template>
  <div>
    <el-table
      v-loading="listLoading"
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 120%"
      :default-sort="{ prop: 'date', order: 'descending' }"
    >
      <el-table-column
        sortable
        align="center"
        label="Name"
        style="width: 20%"
        prop="name"
      />

      <el-table-column
        style="width: 15%"
        prop="code"
        label="Code"
        align="center"
      />

      <el-table-column
        style="width: 5%"
        align="center"
        label="Category"
        prop="category_name"
      />
      <el-table-column
        align="center"
        label="Sales Price"
        style="width: 5%"
        prop="sales_price"
      />

      <el-table-column
        align="center"
        label="Unit"
        width="100"
        prop="unit_name"
      />
      <el-table-column
        align="center"
        label="Unit Cost"
        width="100"
        prop="unit_cost"
      />
      <el-table-column align="center" label="Stock" width="100" />
      <el-table-column class-name="status-col" label="Action" width="150">
        <template #default="scope">
          <router-link :to="'/production-product/edit/' + scope.row.id">
            <el-button type="warning" size="small" icon="el-icon-edit" />
          </router-link>
          <el-button
            type="danger"
            size="small"
            icon="el-icon-delete"
            @click="handleDelete(scope.row.id, scope.row.name)"
          />
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
import {
  fetchList as listProduct,
  DeleteProduct,
} from '@/api/production/product';

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
      total: 0,
      list: null,
      listLoading: true,
      dialogViewVisible: false,
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        role: '',
        sort: '-id',
        type: 2,
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
      const { data, meta } = await listProduct(this.query);
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
      this.$confirm(
        'This will permanently delete product ' + name + '. Continue?',
        'Warning',
        {
          confirmButtonText: 'OK',
          cancelButtonText: 'Cancel',
          type: 'warning',
        }
      )
        .then(() => {
          DeleteProduct(id)
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
  },
};
</script>
