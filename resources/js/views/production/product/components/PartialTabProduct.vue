<template>
  <div>
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
      v-loading="loadingProduct"
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 120%"
      :default-sort="{ prop: 'date', order: 'descending' }"
    >
      <el-table-column sortable align="center" label="Name" prop="name" />

      <el-table-column width="200" prop="code" label="Code" align="center" />

      <el-table-column
        width="200px"
        align="center"
        label="Category"
        prop="category_name"
      />
      <el-table-column
        align="center"
        label="Sales Price"
        width="100"
        prop="sales_price"
      />

      <el-table-column
        align="center"
        label="Cost"
        width="100"
        prop="unit_cost"
      />
      <el-table-column align="center" label="Profit" width="100" prop="/" />
      <el-table-column align="center" label="Prod. Time" width="100" />
      <el-table-column class-name="status-col" label="Status" width="150">
        <template #default="scope">
          <span v-if="scope.row.status == 1">
            <el-tag class="mx-1" type="warning" effect="plain">Waiting confirmation</el-tag>
          </span>
          <span v-else-if="scope.row.status == 2">
            <el-tag class="mx-1" type="success" effect="plain">Approved</el-tag>
          </span>
          <span v-else>
            <el-tag class="mx-1" type="danger" effect="plain">Rejected</el-tag>
          </span>
        </template>
      </el-table-column>
      label="Cost"
      <el-table-column class-name="status-col" width="200" label="Action">
        <template #default="scope">
          <span v-if="scope.row.status == 1">
            <router-link :to="'/production-product/edit/' + scope.row.id">
              <el-button type="warning" size="small" icon="el-icon-edit" />
            </router-link>
            <el-button
              type="danger"
              size="small"
              icon="el-icon-delete"
              @click="handleDelete(scope.row.id, scope.row.name)"
            />
          </span>
          <router-link
            :to="'/production-product/detailProduct/' + scope.row.id"
          >
            <el-button type="primary" size="small">
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
      loadingProduct: true,
      dialogViewVisible: false,
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        role: '',
        sort: '-id',
        type: 1,
      },
    };
  },
  created() {
    this.getList();
  },
  methods: {
    async getList() {
      const { limit, page } = this.query;
      this.loadingProduct = true;
      const { data, meta } = await listProduct(this.query);
      this.list = data;
      this.list.forEach((element, index) => {
        element['index'] = (page - 1) * limit + index + 1;
      });
      this.total = meta.total;
      this.loadingProduct = false;
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
