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
        <el-table-column
          align="right"
          label="Harga Beli"
          width="100px"
          prop="harga_beli"
        />
        <el-table-column
          align="right"
          label="Description"
          width="100px"
          prop="harga_jual"
        />
        <el-table-column align="center" label="Actions" width="200">
          <template slot-scope="scope">
            <el-button
              v-if="scope.row.edit"
              type="success"
              size="small"
              @click="confirmEdit(scope.row)"
            ><svg-icon icon-class="disk" />
            </el-button>
            <el-button
              v-if="!scope.row.edit"
              type="warning"
              size="small"
              icon="el-icon-edit"
              @click="handleEdit(scope.row)"
            />
            <el-tooltip
              class="item"
              effect="dark"
              content="Stock OUT"
              placement="top-start"
            >
              <el-button
                :disabled="scope.row.qty == 0"
                type="warning"
                size="small"
                icon="exit"
                @click="handleStockOut(scope.row.id)"
              >
                <svg-icon icon-class="exit" />
              </el-button>
            </el-tooltip>

            <el-tooltip
              class="item"
              effect="dark"
              content="Detail"
              placement="top-start"
            >
              <router-link
                :to="'/production-product/mutasiStock/' + scope.row.id"
              >
                <el-button type="primary" size="small">
                  <svg-icon icon-class="eye-melek" />
                </el-button>
              </router-link>
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
        :title="'Form Stock OUT'"
        :visible.sync="dialogFormStockOutVisible"
        width="70%"
      >
        <div v-loading="LoadingStockOUT" class="form-container">
          <el-form
            ref="stockFormOut"
            :rules="rulesOut"
            :model="newStockOUT"
            label-position="left"
            label-width="150px"
            style="max-width: 100%"
          >
            <el-row :gutter="20">
              <el-col :span="10">
                <el-form-item
                  :label="$t('Harga Jual')"
                  prop="harga_jual"
                  style="width: 100%"
                >
                  <el-input
                    v-model="newStockOUT.harga_jual"
                    placeholder="type here"
                    style="width: 100%"
                  />
                </el-form-item>
              </el-col>
              <el-col :span="14">
                <el-form-item
                  :label="$t('Discription')"
                  prop="description"
                  style="width: 100%"
                >
                  <el-input
                    v-model="newStockOUT.description"
                    type="textarea"
                    :rows="1"
                    placeholder="type description here"
                    style="width: 100%"
                  />
                </el-form-item>
              </el-col>
            </el-row>
            <el-table
              ref="multipleTable"
              v-loading="listLoadingStockIn"
              border
              height="350"
              :data="listStockIN"
              style="width: 100%"
              @selection-change="handleSelectionChange"
            >
              <el-table-column type="selection" width="55" />
              <el-table-column label="Date" width="200">
                <template slot-scope="scope">
                  <span>{{
                    scope.row.created_at | parseTime('{y}-{m}-{d} {h}:{i}')
                  }}</span>
                </template>
              </el-table-column>
              <el-table-column
                prop="control_id"
                label="Control ID"
                width="180"
              />
              <el-table-column
                prop="harga_beli"
                label="Harga Beli"
                width="120"
              />
              <el-table-column
                prop="description"
                label="Description"
                show-overflow-tooltip
              />
            </el-table>
          </el-form>
          <br>
          <div slot="footer" class="dialog-footer">
            <el-button @click="dialogFormStockOutVisible = false">
              {{ $t('table.cancel') }}
            </el-button>
            <el-button type="primary" @click="createStockOut()">
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
import {
  comboData as listProduct,
  updateSafetyStock,
} from '@/api/production/product';
import {
  fetchListStokIn,
  fetchList,
  createStokOut,
} from '@/api/production/stock';

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
      newStockOUT: {},
      newSafety: { safety_stock: 0 },
      listLoading: true,
      dialogFormStockOutVisible: false,
      LoadingStockOUT: false,
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
    this.loadOptions();
  },
  methods: {
    loadOptions() {
      listProduct().then((response) => {
        this.productOptions = response.data;
      });
    },
    handleSelectionChange(val) {
      this.newStockOUT.items = val;
    },
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
    handleEdit(row) {
      this.newSafety.safety_stock = row.safety_stock;
      row.edit = true;
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
    async handleStockOut(product_id) {
      this.listLoadingStockIn = true;
      this.resetFormStockOut();
      this.dialogFormStockOutVisible = true;
      fetchListStokIn(product_id).then((response) => {
        this.listStockIN = response;
        this.listLoadingStockIn = false;
      });
    },
    confirmEdit(row) {
      this.listLoadingStock = true;
      this.newSafety.product_id = row.id;
      console.log(this.newSafety);
      updateSafetyStock(this.newSafety)
        .then((response) => {
          this.$message({
            type: 'success',
            message: 'The safety stock has been edited',
          });
          this.handleFilter();
        })
        .catch((error) => {
          console.log(error);
        });
      row.edit = false;
      this.newSafety = {
        product_id: '',
        safety_stock: '',
      };
    },
    resetFormStockOut() {
      this.newStock = { id: '', description: '', role: 'stock' };
    },
    createStockOut() {
      this.$refs['stockFormOut'].validate((valid) => {
        if (valid) {
          this.newStockOUT.roles = [this.newStockOUT.role];
          this.LoadingStockIN = true;
          createStokOut(this.newStockOUT)
            .then((response) => {
              this.$message({
                message: ' This product has been updated successfully.',
                type: 'success',
                duration: 5 * 1000,
              });
              this.resetFormStockIN();
              this.dialogFormStockInVisible = false;
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
