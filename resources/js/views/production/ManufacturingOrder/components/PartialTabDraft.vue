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
        v-loading="listLoadingStock"
        :data="list"
        border
        fit
        highlight-current-row
        style="width: 100%"
        @sort-change="sortChange"
      >
        <el-table-column align="center" label="Date">
          <template slot-scope="scope">
            <span>{{
              scope.row.created_at | parseTime('{y}-{m}-{d} {h}:{i}')
            }}</span>
          </template>
        </el-table-column>
        <el-table-column align="center" label="Name">
          <template slot-scope="scope">
            <span>{{ scope.row.name }}</span>
          </template>
        </el-table-column>
        <el-table-column align="center" label="Code" prop="code" />
        <el-table-column align="center" width="150">
          <template #header>
            <span style="margin-left: 10px">Safety Stock </span>
          </template>
          <template #default="scope">
            <template v-if="scope.row.edit">
              <el-input-number
                v-model="newSafety.safety_stock"
                class="edit-input"
                size="small"
                :min="0"
              />
            </template>
            <span v-else style="margin-left: 10px">
              {{ scope.row.safety_stock }}
            </span>
          </template>
        </el-table-column>
        <el-table-column
          align="center"
          label="Quantity"
          width="100px"
          prop="qty"
        />
        <el-table-column align="center" label="Actions" width="100">
          <template slot-scope="scope">
            <el-tooltip
              class="item"
              effect="dark"
              content="Submission"
              placement="top-start"
            >
              <el-button
                type="warning"
                size="small"
                @click="handleNewOrder(scope.row)"
              >
                <svg-icon icon-class="paper-plane" />
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
        :title="'Manufacturing Order'"
        :visible.sync="dialogForm"
        width="30%"
      >
        <div v-loading="LoadingForm" class="form-container">
          <el-form
            ref="ManufaturingOrderForm"
            :rules="rulesOut"
            :model="newManufaturingOrder"
            label-position="left"
            label-width="150px"
            style="max-width: 100%"
          >
            <el-col>
              <el-form-item
                :label="$t('Quantity')"
                prop="qty"
                style="width: 100%"
              >
                <el-input-number
                  v-model="newManufaturingOrder.qty"
                  placeholder="type here"
                  style="width: 100%"
                  :min="1"
                />
              </el-form-item>
            </el-col>
            <el-col>
              <el-form-item :label="$t('Date')" prop="date" style="width: 100%">
                <el-date-picker
                  v-model="newManufaturingOrder.date"
                  type="datetime"
                  placeholder="Select date and time"
                  format="dd-MM-yyyy HH:mm:ss"
                  value-format="yyyy-MM-dd HH:mm:ss"
                  style="width: 100%"
                />
              </el-form-item>
            </el-col>
          </el-form>
          <br>
          <div slot="footer" class="dialog-footer">
            <el-button @click="dialogForm = false">
              {{ $t('table.cancel') }}
            </el-button>
            <el-button type="primary" @click="createNewOrder()">
              {{ $t('table.confirm') }}
            </el-button>
          </div>
        </div>
      </el-dialog>
    </div>
  </div>
</template>
<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import { fetchList } from '@/api/production/stock';
import { createManufacture } from '@/api/production/manufactur_order';

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
      listStockIN: null,
      newManufaturingOrder: {},
      newSafety: { safety_stock: 0 },
      listLoading: true,
      dialogForm: false,
      LoadingForm: false,
      listLoadingStockIn: false,
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        role: '',
        sort: '-id',
        type: 1,
      },
      rulesOut: {
        harga_jual: [
          {
            required: true,
            message: 'Harga Jual is required',
            trigger: 'blur',
          },
        ],
        description: [
          {
            required: true,
            message: 'Description is required',
            trigger: 'blur',
          },
        ],
      },
    };
  },
  created() {
    this.getList();
  },
  methods: {
    getList() {
      this.listLoadingStock = true;
      const { limit, page } = this.query;
      fetchList(this.query).then((response) => {
        this.list = response.data.map((v) => {
          this.$set(v, 'edit', false);
          return v;
        });
        this.list.forEach((element, index) => {
          element['index'] = (page - 1) * limit + index + 1;
        });
        this.total = response.total;
        this.listLoadingStock = false;
      });
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
    async handleNewOrder(data) {
      this.listLoadingStockIn = true;
      this.resetFormStockOut();
      this.newManufaturingOrder.product_id = data.id;
      this.newManufaturingOrder.total_timing = data.total_timing;
      this.newManufaturingOrder.qty = 1;
      this.dialogForm = true;
    },
    resetFormStockOut() {
      this.newManufaturingOrder = {};
    },
    createNewOrder() {
      this.$refs['ManufaturingOrderForm'].validate((valid) => {
        if (valid) {
          this.newManufaturingOrder.roles = [this.newManufaturingOrder.role];
          this.LoadingStockIN = true;
          createManufacture(this.newManufaturingOrder)
            .then((response) => {
              this.$message({
                message: ' This product has been updated successfully.',
                type: 'success',
                duration: 5 * 1000,
              });
              this.dialogForm = false;
              this.handleFilter();
            })
            .catch((error) => {
              console.log(error);
            })
            .finally(() => {
              this.LoadingStockIN = false;
            });
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
  },
};
</script>
