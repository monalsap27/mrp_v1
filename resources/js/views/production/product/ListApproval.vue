<template>
  <div class="app-container">
    <div class="filter-container">
      <!-- <el-input
        v-model="query.keyword"
        :placeholder="$t('table.keyword')"
        style="width: 200px"
        class="filter-item"
        @keyup.enter.native="handleFilter"
      /> -->
      <!-- <el-button
        class="filter-item"
        type="primary"
        icon="el-icon-search"
        @click="handleFilter"
      >
        {{ $t('table.search') }}
      </el-button> -->
    </div>
    <div>
      <el-table
        v-loading="loadingProduct"
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
          width="300"
          prop="name"
        />

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
        <el-table-column fixed="right" class-name="status-col" width="200">
          <template #header>
            <el-input
              v-model="query.keyword"
              size="mini"
              placeholder="Type to search"
            />
          </template>

          <template #default="scope">
            <span v-if="scope.row.status == 1">
              <router-link
                :to="'/production-product/detailApproval/' + scope.row.id"
              >
                <el-button type="success" size="small">
                  <svg-icon icon-class="question-square" />
                </el-button>
              </router-link>
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
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import { fetchListApproval as listProductApproval } from '@/api/production/product';

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
  computed: { ...mapGetters(['roles']) },

  created() {
    this.getList();
    if (this.roles.includes('manager')) {
      this.currentRole = 'editorDashboard';
    }
  },
  methods: {
    async getList() {
      const { limit, page } = this.query;
      this.loadingProduct = true;
      const { data, meta } = await listProductApproval(this.query);
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
