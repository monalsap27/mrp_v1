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
        <el-table-column align="center" label="Submission Date">
          <template slot-scope="scope">
            <span>{{
              scope.row.created_at | parseTime('{d}-{m}-{y} {h}:{i}')
            }}</span>
          </template>
        </el-table-column>
        <el-table-column align="center" label="Production Date">
          <template slot-scope="scope">
            <span>{{
              scope.row.production_date | parseTime('{d}-{m}-{y} {h}:{i}')
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
        <el-table-column align="center" label="Ingredients" width="150">
          <template #default="scope">
            <span v-if="scope.row.is_available == 2">
              <el-tag
                class="mx-1"
                type="success"
                effect="plain"
                @click="showDetail(scope.row.id)"
              >Available</el-tag>
            </span>
            <span v-else>
              <el-tag
                class="mx-1"
                type="danger"
                effect="plain"
                @click="showDetail(scope.row.id)"
              >Not Available</el-tag>
            </span>
          </template>
        </el-table-column>
        <el-table-column align="center" label="Status" width="150">
          <template #default="scope">
            <el-tag :type="scope.row && scope.row.status | statusType">
              {{ scope.row && scope.row.status | statusFilter }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column align="center" label="Actions" width="100">
          <template slot-scope="scope">
            <span v-if="scope.row.status == 2">
              <el-button
                type="success"
                size="small"
                @click="handleStartOrder(scope.row)"
              >
                <svg-icon icon-class="play" />
              </el-button>
            </span>
            <span v-if="scope.row.status == 3">
              <router-link
                :to="'/production-management/detailProgress/' + scope.row.id"
              >
                <el-button type="info" size="small">
                  <svg-icon icon-class="eye-melek" />
                </el-button>
              </router-link>
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
      <el-dialog
        title="Material Details"
        :visible.sync="dialogTableVisible"
        width="60%"
      >
        <el-table v-loading="listDetailLoading" :data="listDetail">
          <el-table-column prop="name_material" label="Material" width="300" />
          <el-table-column prop="qty_stock" label="Stock" width="100" />
          <el-table-column
            prop="qty_produksi"
            label="Production Quantity"
            width="100"
          />
          <el-table-column prop="claim_stock" label="Claim Stock" width="110" />
          <el-table-column property="status" label="Status">
            <template #default="scope">
              <span v-if="scope.row.status">
                <el-tag class="mx-1" type="success" effect="plain">Available</el-tag>
              </span>
              <span v-else>
                <el-tag class="mx-1" type="danger" effect="plain">Not Available</el-tag>
              </span>
            </template>
          </el-table-column>
        </el-table>
      </el-dialog>
    </div>
  </div>
</template>
<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import {
  fetchList,
  detailListAvailable,
  startProduction,
} from '@/api/production/manufactur_order';

export default {
  name: 'WorkstationList',
  components: { Pagination },
  filters: {
    statusType(status) {
      const statusMap = { 1: 'primary', 2: 'info', 3: 'warning', 4: 'success' };
      return statusMap[status];
    },
    statusFilter(status) {
      const statusMap = {
        1: 'Waiting',
        2: 'Draft',
        3: 'On Progress',
        4: 'Done',
      };
      return statusMap[status];
    },
  },
  data() {
    return {
      total: 0,
      list: null,
      listDetail: null,
      dataManufaturingOrder: {},
      listLoading: true,
      listDetailLoading: true,
      dialogTableVisible: false,
      LoadingForm: false,
      listLoadingStockIn: false,
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
    showDetail(id) {
      this.listDetailLoading = true;
      this.dialogTableVisible = true;
      detailListAvailable(id).then((response) => {
        this.listDetail = response.data.map((v) => {
          const stock =
            parseInt(v.qty_stock) -
            (parseInt(v.claim_stock) + parseInt(v.qty_produksi));
          const status = parseFloat(stock) > 0;
          this.$set(v, 'status', status);
          return v;
        });
        this.listDetail = response.data;
        this.listDetailLoading = false;
      });
    },
    handleStartOrder(data) {
      this.$confirm('Are you sure you want to start production ? ', {
        confirmButtonText: 'Yes ',
        cancelButtonText: 'No, cancel',
        type: 'warning',
      })
        .then(() => {
          startProduction(data)
            .then((response) => {
              this.$message({
                type: 'success',
                message: 'This product is starting production',
              });
              this.handleFilter();
              this.LoadingForm = false;
            })
            .catch((error) => {
              console.log(error);
            });
        })
        .catch(() => {
          this.$message({
            type: 'info',
            message: 'Production has been canceled',
          });
          this.handleFilter();
          this.listDetailLoading = false;
        });
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
