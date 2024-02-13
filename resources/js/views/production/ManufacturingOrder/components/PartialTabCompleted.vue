<template>
  <div>
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
        <el-table-column align="center" label="Nomor Produksi" width="140">
          <template slot-scope="scope">
            <span>{{ scope.row.code }}</span>
          </template>
        </el-table-column>
        <el-table-column align="center" label="Submission Date" width="140">
          <template slot-scope="scope">
            <span>{{
              scope.row.created_at | parseTime('{d}-{m}-{y} {h}:{i}')
            }}</span>
          </template>
        </el-table-column>
        <el-table-column align="center" label="Production Date" width="140">
          <template slot-scope="scope">
            <span>{{
              scope.row.production_date | parseTime('{d}-{m}-{y} {h}:{i}')
            }}</span>
          </template>
        </el-table-column>
        <el-table-column align="center" label="Completion Date" width="140">
          <template slot-scope="scope">
            <span>{{
              scope.row.finish_at | parseTime('{d}-{m}-{y} {h}:{i}')
            }}</span>
          </template>
        </el-table-column>
        <el-table-column align="center" label="Name">
          <template slot-scope="scope">
            <span>{{ scope.row.name }}</span>
          </template>
        </el-table-column>
        <el-table-column align="center" label="Production Quantity" width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.qty }}</span>
          </template>
        </el-table-column>
        <el-table-column align="center" label="Actions" width="100">
          <template slot-scope="scope">
            <router-link
              :to="'/production-management/detailProgress/' + scope.row.id"
            >
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
  </div>
</template>
<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import { fetchListCompleted } from '@/api/production/manufactur_order';

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
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        role: '',
        sort: '-id',
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
      const { data, meta } = await fetchListCompleted(this.query);
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
  },
};
</script>

<style>
.el-tag {
  cursor: pointer;
}
.el-button--small {
  border-radius: 10px;
}
</style>
