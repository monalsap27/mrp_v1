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
    >
      <el-table-column align="center" label="ID" width="80" prop="id" />

      <el-table-column align="center" label="Name" prop="product_name" />

      <el-table-column
        align="center"
        label="Code"
        prop="product_code"
        width="200"
      />

      <el-table-column align="center" label="Price" width="150">
        <template slot-scope="scope">
          <span>{{ scope.row.price | toThousandFilter }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Status" width="100">
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
            content="Edit"
            placement="top-start"
          >
            <el-button
              type="warning"
              size="small"
              icon="el-icon-edit"
              @click="handleEditPrice(scope.row.id)"
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
      :title="'Create new price'"
      :visible.sync="dialogFormVisible"
      width="50%"
    >
      <div v-loading="priceCreating" class="form-container">
        <el-form
          ref="priceForm"
          :rules="rules"
          :model="newPrice"
          label-position="left"
          label-width="150px"
          style="max-width: 100%"
        >
          <el-form-item :label="$t('Product')" prop="product">
            <el-select
              v-model="newPrice.product"
              filterable
              class="filter-item"
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
          <el-form-item :label="$t('Price')" prop="price">
            <el-input v-model="newPrice.price" placeholder="type price here" />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="createPrice()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>

    <el-dialog
      :title="'Edit Price '"
      :visible.sync="dialogEditVisible"
      width="50%"
    >
      <div v-loading="priceEdit" class="form-container">
        <el-form
          ref="priceForm"
          :rules="rules"
          :model="currentPrice"
          label-position="left"
          label-width="150px"
          style="max-width: 100%"
        >
          <el-form-item :label="$t('Product')" prop="product">
            <el-select
              v-model="currentPrice.product"
              filterable
              class="filter-item"
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
          <el-form-item :label="$t('Price')" prop="price">
            <el-input
              v-model="currentPrice.price"
              placeholder="type price here"
            />
          </el-form-item>
          <el-form-item :label="$t('Status')" prop="status">
            <el-tag :type="currentPrice.status | statusType">
              {{ currentPrice.status | statusFilter }}
            </el-tag>
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogEditVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="updatePrice()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import { fetchList, createOrUpdatePrice } from '@/api/sales/master/price';
import { comboData as listProduct } from '@/api/production/product';

export default {
  name: 'PriceList',
  components: { Pagination },
  filters: {
    statusType(status) {
      const statusMap = { 1: 'success', 0: 'danger' };
      return statusMap[status];
    },
    statusFilter(status) {
      const statusMap = {
        1: 'Active',
        0: 'Non-Active',
      };
      return statusMap[status];
    },
  },
  data() {
    return {
      total: 0,
      list: null,
      listLoading: true,
      priceEdit: false,
      priceCreating: false,
      dialogFormVisible: false,
      dialogEditVisible: false,
      newPrice: {},
      currentPrice: {},
      productOptions: {},
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
        price: [
          { required: true, message: 'Price is required', trigger: 'blur' },
        ],
      },
    };
  },
  created() {
    this.resetNewForm();
    this.getList();
    this.loadOptions();
  },
  methods: {
    loadOptions() {
      listProduct().then((response) => {
        this.productOptions = response.data;
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
    createPrice() {
      this.$refs['priceForm'].validate((valid) => {
        if (valid) {
          this.newPrice.roles = [this.newPrice.role];
          this.priceCreating = true;
          createOrUpdatePrice(this.newPrice)
            .then((response) => {
              this.$message({
                message:
                  'New Price ' +
                  this.newPrice.name +
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
              this.priceCreating = false;
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
      this.newPrice = {
        id: '',
        name: '',
        description: '',
        role: 'price',
      };
    },
    handleCreate() {
      this.resetNewForm();
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['priceForm'].clearValidate();
      });
    },
    async handleEditPrice(id) {
      this.currentId = id;
      this.priceEdit = true;
      this.dialogEditVisible = true;
      const foundPrice = this.list.find((price) => price.id === id);
      console.log(foundPrice);
      this.currentPrice = {
        id: foundPrice.id,
        product: foundPrice.product_id,
        price: foundPrice.price,
        status: foundPrice.status,
      };
      this.priceEdit = false;
    },
    updatePrice() {
      this.priceEdit = true;
      createOrUpdatePrice(this.currentPrice)
        .then((response) => {
          this.$message({
            message: 'Price ' + ' has been updated successfully.',
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
          this.priceEdit = false;
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
