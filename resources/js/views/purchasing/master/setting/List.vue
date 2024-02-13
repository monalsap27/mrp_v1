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
      <el-button
        class="filter-item"
        style="margin-left: 10px"
        type="primary"
        icon="el-icon-plus"
        @click="handleCreate"
      >
        {{ $t('table.add') }}
      </el-button>
    </div>

    <el-table
      v-loading="listLoading"
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 100%"
      @sort-change="sortChange"
    >
      <el-table-column
        align="center"
        label="ID"
        width="80"
        sortable="custom"
        prop="id"
      />
      <el-table-column align="center" label="Product" prop="product_name" />
      <el-table-column align="center" label="Supplier" prop="supplier_name" />
      <el-table-column align="center" label="Unit" prop="unit_name" />
      <el-table-column align="center" label="Unit Price" prop="unit_price" />
      <el-table-column align="center" label="Actions" width="200">
        <template slot-scope="scope">
          <el-tooltip
            class="item"
            effect="dark"
            content="Edit"
            placement="top-start"
          >
            <el-button
              type="warning"
              size="small"
              icon="el-icon-edit"
              @click="handleEditSetting(scope.row.id)"
            />
          </el-tooltip>

          <el-tooltip
            class="item"
            effect="dark"
            content="Delete"
            placement="top-start"
          >
            <el-button
              type="danger"
              size="small"
              icon="el-icon-delete"
              @click="handleDelete(scope.row.id, scope.row.name)"
            />
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
      :title="'Create new setting'"
      :visible.sync="dialogFormVisible"
      width="50%"
    >
      <div v-loading="settingCreating" class="form-container">
        <el-form
          ref="settingForm"
          :rules="rules"
          :model="newSetting"
          label-position="left"
          label-width="150px"
          style="max-width: 100%"
        >
          <el-form-item :label="$t('Product')" prop="product">
            <el-select
              v-model="newSetting.product"
              class="filter-item w-200"
              placeholder="Please select"
              style="width: 100%"
            >
              <el-option
                v-for="item_product in productOptions"
                :key="item_product.id"
                :label="item_product.name"
                :value="item_product.id"
              >
                <span style="float: left">{{ item_product.code }}</span>
                <span style="float: right; color: #8492a6; font-size: 13px">{{
                  item_product.name
                }}</span>
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item :label="$t('Supplier')" prop="supplier">
            <el-select
              v-model="newSetting.supplier"
              class="filter-item w-200"
              placeholder="Please select"
              style="width: 100%"
            >
              <el-option
                v-for="item_supplier in supplierOptions"
                :key="item_supplier.id"
                :label="item_supplier.name"
                :value="item_supplier.id"
              />
            </el-select>
          </el-form-item>
          <el-form-item :label="$t('Unit')" prop="unit">
            <el-select
              v-model="newSetting.unit"
              class="filter-item"
              placeholder="Please select"
              style="width: 100%"
            >
              <el-option
                v-for="item_unit in unitOptions"
                :key="item_unit.id"
                :label="item_unit.name"
                :value="item_unit.id"
              />
            </el-select>
          </el-form-item>
          <el-form-item :label="$t('Unit Price')" prop="unit_price">
            <el-input
              v-model="newSetting.unit_price"
              placeholder="type Unit Price here"
            />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="createSetting()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>

    <el-dialog
      :title="'Edit setting'"
      :visible.sync="dialogEditVisible"
      width="50%"
    >
      <div v-loading="settingCreating" class="form-container">
        <el-form
          ref="settingForm"
          :rules="rules"
          :model="currentSetting"
          label-position="left"
          label-width="150px"
          style="max-width: 100%"
        >
          <el-form-item :label="$t('Product')" prop="product">
            <el-select
              v-model="currentSetting.product"
              class="filter-item w-200"
              placeholder="Please select"
              style="width: 100%"
            >
              <el-option
                v-for="item_product in productOptions"
                :key="item_product.id"
                :label="item_product.name"
                :value="item_product.id"
              >
                <span style="float: left">{{ item_product.code }}</span>
                <span style="float: right; color: #8492a6; font-size: 13px">{{
                  item_product.name
                }}</span>
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item :label="$t('Supplier')" prop="supplier">
            <el-select
              v-model="currentSetting.supplier"
              class="filter-item w-200"
              placeholder="Please select"
              style="width: 100%"
            >
              <el-option
                v-for="item_supplier in supplierOptions"
                :key="item_supplier.id"
                :label="item_supplier.name"
                :value="item_supplier.id"
              />
            </el-select>
          </el-form-item>
          <el-form-item :label="$t('Unit')" prop="unit">
            <el-select
              v-model="currentSetting.unit"
              class="filter-item"
              placeholder="Please select"
              style="width: 100%"
            >
              <el-option
                v-for="item_unit in unitOptions"
                :key="item_unit.id"
                :label="item_unit.name"
                :value="item_unit.id"
              />
            </el-select>
          </el-form-item>
          <el-form-item :label="$t('Unit Price')" prop="unit_price">
            <el-input
              v-model="currentSetting.unit_price"
              placeholder="type Unit Price here"
            />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogEditVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="createSetting()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import {
  fetchList,
  createOrUpdateSetting,
  DeleteSetting,
} from '@/api/purchasing/master/setting';
import { comboData as listProduct } from '@/api/production/product';
import { fetchList as listUnit } from '@/api/purchasing/master/unit';
import { fetchList as listSupplier } from '@/api/production/master/supplier';

export default {
  name: 'SettingList',
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
      settingEdit: false,
      settingCreating: false,
      dialogFormVisible: false,
      dialogEditVisible: false,
      newSetting: {},
      unitOptions: {},
      currentSetting: {},
      productOptions: {},
      supplierOptions: {},
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        role: '',
        sort: '-id',
      },
      rules: {
        product: [
          { required: true, message: 'Product is required', trigger: 'blur' },
        ],
        supplier: [
          { required: true, message: 'Supplier is required', trigger: 'blur' },
        ],
        unit: [
          { required: true, message: 'Unit is required', trigger: 'blur' },
        ],
        unit_price: [
          {
            required: true,
            message: 'Unit Price is required',
            trigger: 'blur',
          },
        ],
      },
    };
  },
  created() {
    this.resetNewForm();
    this.getList();
  },
  methods: {
    loadOptions() {
      listProduct().then((response) => {
        this.productOptions = response.data;
      });
      listUnit().then((response) => {
        this.unitOptions = response.data;
      });
      listSupplier().then((response) => {
        this.supplierOptions = response.data;
      });
    },
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
    createSetting() {
      this.$refs['settingForm'].validate((valid) => {
        if (valid) {
          this.newSetting.roles = [this.newSetting.role];
          this.settingCreating = true;
          createOrUpdateSetting(this.newSetting)
            .then((response) => {
              this.$message({
                message:
                  'New setting ' +
                  this.newSetting.name +
                  ' has been created successfully.',
                type: 'success',
                duration: 5 * 1000,
              });
              this.resetNewForm();
              this.dialogFormVisible = false;
              this.handleFilter();
            })
            .catch((error) => {
              console.log(error);
            })
            .finally(() => {
              this.settingCreating = false;
            });
        } else {
          console.log('error submit!!');
          return false;
        }
      });
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
    resetNewForm() {
      this.newSetting = {
        id: '',
        name: '',
        description: '',
        role: 'setting',
      };
    },
    handleCreate() {
      this.resetNewForm();
      this.loadOptions();
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['settingForm'].clearValidate();
      });
    },
    async handleEditSetting(id) {
      this.currentId = id;
      this.settingCreating = true;
      this.dialogEditVisible = true;
      this.loadOptions();
      const foundSetting = this.list.find((setting) => setting.id === id);
      this.currentSetting = {
        id: foundSetting.id,
        product: foundSetting.product,
        supplier: foundSetting.supplier,
        unit: foundSetting.unit,
        unit_price: foundSetting.unit_price,
      };
      this.settingCreating = false;
    },
    updateSetting() {
      this.settingEdit = true;
      createOrUpdateSetting(this.currentSetting)
        .then((response) => {
          this.$message({
            message:
              'Setting ' +
              this.currentSetting.name +
              ' has been updated successfully.',
            type: 'success',
            duration: 5 * 1000,
          });
          this.resetNewForm();
          this.dialogEditVisible = false;
          this.handleFilter();
        })
        .catch((error) => {
          console.log(error);
        })
        .finally(() => {
          this.settingEdit = false;
        });
    },

    handleDelete(id, name) {
      this.$confirm(
        'This will permanently delete Setting ' + name + '. Continue?',
        'Warning',
        {
          confirmButtonText: 'OK',
          cancelButtonText: 'Cancel',
          type: 'warning',
        }
      )
        .then(() => {
          DeleteSetting(id)
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
