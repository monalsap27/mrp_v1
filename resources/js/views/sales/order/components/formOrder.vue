<template>
  <div class="createPost-container">
    <el-form
      ref="postForm"
      v-loading.fullscreen.lock="loadingForm"
      element-loading-background="rgba(122, 122, 122, 0.8)"
      element-loading-text="Loading..."
      :model="newOrder"
      :rules="rules"
      class="form-container"
    >
      <sticky :class-name="'sub-navbar ' + newOrder.status">
        <el-button
          v-loading="loading"
          style="margin-left: 10px"
          type="success"
          @click="saveOrder()"
        >
          Submit
        </el-button>
        <router-link :to="'/purchasing-order/list'">
          <el-button
            class="filter-item"
            style="margin-left: 10px"
            type="danger"
          >
            Close
          </el-button>
        </router-link>
      </sticky>
      <div class="createPost-main-container">
        <el-row style="margin-top: 40px">
          <el-col :span="24">
            <div class="postInfo-container">
              <el-row>
                <el-col :span="12">
                  <el-form-item
                    prop="nomor"
                    style="width: 90%"
                    label-width="100px"
                    label="Nomor"
                    class="postInfo-container-item"
                  >
                    <el-input
                      v-model="newOrder.nomor"
                      placeholder="Please input"
                      style="width: 100%"
                      :disabled="true"
                    >
                      <template #append>Nomor </template>
                    </el-input>
                  </el-form-item>
                </el-col>

                <el-col :span="12">
                  <el-form-item
                    prop="Date"
                    label-width="100px"
                    label="Date"
                    class="postInfo-container-item"
                    style="width: 90%"
                  >
                    <el-input
                      v-model="newOrder.date"
                      placeholder="Please input"
                      style="width: 100%"
                      :disabled="true"
                    >
                      <template #append>Date </template>
                    </el-input>
                  </el-form-item>
                </el-col>
              </el-row>

              <el-row>
                <el-col :span="12">
                  <el-form-item
                    prop="customer"
                    style="width: 90%"
                    label-width="100px"
                    label="Customer"
                    class="postInfo-container-item"
                  >
                    <el-select
                      v-model="newOrder.customer"
                      class="filter-item"
                      placeholder="Please select"
                      style="width: 100%"
                      @change="onShowCustomer($event)"
                    >
                      <el-option
                        v-for="item_customer in customerOptions"
                        :key="item_customer.id"
                        :label="item_customer.name"
                        :value="item_customer.id"
                      />
                    </el-select>
                  </el-form-item>
                </el-col>

                <el-col :span="12">
                  <el-form-item
                    prop="npwp"
                    label-width="100px"
                    label="NPWP"
                    class="postInfo-container-item"
                    style="width: 90%"
                  >
                    <el-input
                      v-model="newOrder.npwp"
                      placeholder="Please input"
                      style="width: 100%"
                      :disabled="true"
                    >
                      <template #append>NPWP </template>
                    </el-input>
                  </el-form-item>
                </el-col>
              </el-row>
              <el-row>
                <el-col :span="12">
                  <el-form-item
                    prop="category"
                    style="width: 90%"
                    label-width="100px"
                    label="Category"
                    class="postInfo-container-item"
                  >
                    <el-select
                      v-model="newOrder.category"
                      class="filter-item"
                      placeholder="Please select"
                      style="width: 100%"
                    >
                      <el-option
                        v-for="item_category in categoryOptions"
                        :key="item_category.id"
                        :label="item_category.label"
                        :value="item_category.id"
                      />
                    </el-select>
                  </el-form-item>
                </el-col>
                <el-col :span="12">
                  <el-form-item
                    prop="tenor"
                    style="width: 90%"
                    label-width="100px"
                    label="Tenor"
                    class="postInfo-container-item"
                  >
                    <el-select
                      v-model="newOrder.tenor"
                      class="filter-item"
                      placeholder="Please select"
                      style="width: 100%"
                    >
                      <el-option
                        v-for="item_tenor in tenorOptions"
                        :key="item_tenor.id"
                        :label="item_tenor.tenor"
                        :value="item_tenor.id"
                      />
                    </el-select>
                  </el-form-item>
                </el-col>
              </el-row>
            </div>
          </el-col>
        </el-row>
        <br>
        <el-card class="box-card">
          <el-button
            style="float: right; margin-left: 10px"
            type="success"
            plain
            @click="addRow"
          >
            Add item
          </el-button>
          <table />
          <el-table
            v-loading="listLoading"
            :data="newOrder.items"
            border
            fit
            highlight-current-row
            style="width: 100%"
            show-summary
            :summary-method="getSummaries"
          >
            <el-table-column align="center" label="Product" width="400">
              <template #default="scope">
                <el-form-item prop="product">
                  <el-select
                    v-model="scope.row.product"
                    class="filter-item"
                    placeholder="Please select"
                    style="width: 100%"
                    @change="onShowProduct(scope.row, $event)"
                  >
                    <el-option
                      v-for="item_product in productOptions"
                      :key="item_product.id"
                      :label="item_product.name"
                      :value="item_product.id"
                    >
                      <span style="float: left">{{ item_product.code }}</span>
                      <span
                        style="float: right; color: #8492a6; font-size: 13px"
                      >{{ item_product.name }}</span>
                    </el-option>
                  </el-select>
                </el-form-item>
              </template>
            </el-table-column>

            <el-table-column align="center" label="Qty" prop="qty" width="130">
              <template #default="scope">
                <el-form-item prop="qty">
                  <el-input
                    v-model.number="scope.row.qty"
                    placeholder="type qty here"
                    @input.native="change(scope.row, $event)"
                  />
                </el-form-item>
              </template>
            </el-table-column>

            <el-table-column
              align="center"
              label="Price"
              prop="price"
              width="200"
            >
              <template #default="scope">
                <el-form-item prop="price">
                  <el-input
                    v-model="scope.row.price"
                    placeholder="type price here"
                    @input.native="change(scope.row, $event)"
                  />
                </el-form-item>
              </template>
            </el-table-column>

            <el-table-column
              align="center"
              label="Discount"
              prop="discount"
              width="200"
            >
              <template #default="scope">
                <el-form-item prop="discount">
                  <el-input
                    v-model="scope.row.discount"
                    placeholder="type discount here"
                    @input.native="change(scope.row, $event)"
                  />
                </el-form-item>
              </template>
            </el-table-column>

            <el-table-column
              label="Status"
              prop="status"
              width="200"
              align="center"
            >
              <template #default="scope">
                <el-form-item prop="status">
                  <el-tag
                    :type="scope.row && scope.row.status | statusType"
                    @click="showQTY(scope.row.id)"
                  >
                    {{ scope.row && scope.row.status | statusFilter }} :
                    {{ scope.row.stock }}
                  </el-tag>
                </el-form-item>
              </template>
            </el-table-column>

            <el-table-column
              align="center"
              label="Sub Total"
              prop="sub_total"
              width="250"
            >
              <template #default="scope">
                <el-form-item prop="sub_total">
                  <el-input
                    v-model="scope.row.sub_total"
                    placeholder="type Sub Total here"
                    :disabled="true"
                  />
                </el-form-item>
              </template>
            </el-table-column>

            <el-table-column fixed="right" label="Action" width="100">
              <template #default="scope">
                <el-form-item prop="action">
                  <el-button
                    type="danger"
                    size="small"
                    @click.prevent="handleDelete(scope.$index)"
                  >Delete</el-button>
                </el-form-item>
              </template>
            </el-table-column>
          </el-table>
        </el-card>
      </div>
    </el-form>
  </div>
</template>
<script>
import Sticky from '@/components/Sticky';
import {
  fetchNoSO,
  createOrder,
  ShowOrder,
  ShowOrderDetail,
} from '@/api/sales/order';
import { comboData as listProduct } from '@/api/production/product';
import { fetchList as ListCustomer } from '@/api/customer/customer';
import { showByProduct as listPrice } from '@/api/sales/master/price';
import { showByProduct as listStock } from '@/api/production/stock';
import { fetchList as ListTenor } from '@/api/sales/master/tenor';

const defaultForm = {
  status: 'draft',
  name: '',
  content: '',
  content_short: '',
  display_time: undefined,
  id: undefined,
  platforms: ['a-platform'],
  comment_disabled: false,
  importance: 0,
};

export default {
  name: 'ArticleDetail',
  components: {
    Sticky,
  },
  filters: {
    statusType(status) {
      const statusMap = { 1: 'success', 0: 'danger' };
      return statusMap[status];
    },
    statusFilter(status) {
      const statusMap = { 1: 'Ready', 0: 'Pre-Order', 2: 'Not found' };
      return statusMap[status];
    },
  },
  props: {
    isEdit: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      loading: false,
      loadingForm: false,
      listLoading: false,
      newOrder: {
        items: [],
      },
      tenorOptions: {},
      productOptions: {},
      customerOptions: {},
      postForm: Object.assign({}, defaultForm),
      categoryOptions: [
        {
          id: '1',
          label: 'Cash',
        },
        {
          id: '2',
          label: 'Credit',
        },
      ],
      rules: {
        customer: [
          { required: true, message: 'Customer is required', trigger: 'blur' },
        ],
        category: [
          { required: true, message: 'Payment is required', trigger: 'blur' },
        ],
      },
    };
  },
  computed: {
    contentShortLength() {
      return this.postForm.content_short.length;
    },
  },
  created() {
    this.loadOptions();
    if (this.isEdit) {
      const id = this.$route.params && this.$route.params.id;
      this.fetchDataOrder(id);
    } else {
      this.loadNomorPO();
    }
  },
  methods: {
    loadNomorPO() {
      this.loadingForm = true;
      fetchNoSO().then((response) => {
        this.newOrder.nomor = response.data.no_so;
        this.newOrder.date = response.data.date;
        this.loadingForm = false;
      });
    },
    loadOptions() {
      listProduct().then((response) => {
        this.productOptions = response.data;
      });
      ListCustomer().then((response) => {
        this.customerOptions = response.data;
      });
      ListTenor().then((response) => {
        this.tenorOptions = response.data;
      });
    },
    onShowCustomer(event) {
      const foundCustomer = this.customerOptions.find(
        (customer) => customer.id === event
      );
      this.newOrder.npwp = foundCustomer.npwp;
    },

    async onShowProduct(row, event) {
      listPrice(event).then((response) => {
        row.price = response.data.price;
      });
      listStock(event).then((response) => {
        row.status = response.data.stock > 0 ? '1' : '0';
        row.stock = response.data.stock;
      });
    },
    change(row, event) {
      var qty = parseInt(row.qty);
      var price = parseFloat(row.price);
      var discount = parseFloat(row.discount);
      row.sub_total = qty * price - discount;
    },
    addRow() {
      this.newOrder.items.push({
        product: '',
        price: '0',
        qty: '0',
        discount: '0',
        status: '2',
        sub_total: '0',
        stock: '0',
      });
    },
    handleDelete(index) {
      this.newOrder.items.splice(index, 1);
    },
    getSummaries(param) {
      const { columns, data } = param;
      const sums = [];
      columns.forEach((column, index) => {
        if (index === 0) {
          sums[index] = 'Total ';
          return;
        }
        const values = data.map((item) => Number(item[column.property]));
        if (!values.every((value) => isNaN(value))) {
          sums[index] =
            ' ' +
            values.reduce((prev, curr) => {
              const value = Number(curr);
              if (!isNaN(value)) {
                this.newOrder.total_amount = prev + curr;
                return prev + curr;
              } else {
                return prev;
              }
            }, 0);
        } else {
          sums[index] = 'N/A';
        }
      });
      return sums;
    },
    fetchDataOrder(id) {
      this.loadingForm = true;
      ShowOrder(id)
        .then((response) => {
          this.newOrder = {
            id: response.data.id,
            nomor: response.data.nomor,
            date: response.data.created_at,
            customer: response.data.customer_id,
            npwp: response.data.npwp,
            payment_method: response.data.m_payment_method_id,
            items: [],
          };
        })
        .catch((err) => {
          console.log(err);
        });
      ShowOrderDetail(id)
        .then((response) => {
          response.data.forEach((element, index) => {
            this.newOrder.items.push({
              detail_id: element.detail_id,
              product: element.product_id,
              qty: element.qty,
              price: element.unit_price,
              discount: element.discount,
              status: element.qty_stock > 0 ? '1' : '0',
              stock: element.qty_stock,
              sub_total: parseFloat(element.total_price),
            });
          });
          this.loadingForm = false;
        })
        .catch((err) => {
          console.log(err);
        });
    },

    saveOrder() {
      this.$refs['postForm'].validate((valid) => {
        if (valid) {
          this.newOrder.roles = [this.newOrder.role];
          this.loading = true;
          createOrder(this.newOrder)
            .then((response) => {
              this.$message({
                message: response.message,
                type: 'success',
                duration: 5 * 1000,
              });
              this.$router.push({ path: '/sales-order/list' });
              this.dialogFormVisible = false;
              this.handleFilter();
            })
            .catch((error) => {
              console.log(error);
            })
            .finally(() => {
              this.loading = false;
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

<style rel="stylesheet/scss" lang="scss" scoped>
@import '~@/styles/mixin.scss';
.createPost-container {
  position: relative;
  .createPost-main-container {
    padding: 0 45px 20px 50px;
    .postInfo-container {
      position: relative;
      @include clearfix;
      margin-bottom: 10px;
      .postInfo-container-item {
        float: left;
      }
    }
  }
  .word-counter {
    width: 40px;
    position: absolute;
    right: -10px;
    top: 0px;
  }
}
</style>
<style>
.createPost-container label.el-form-item__label {
  text-align: left;
}
</style>
