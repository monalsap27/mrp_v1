<template>
  <div class="createPost-container">
    <el-form
      ref="postForm"
      v-loading.fullscreen.lock="loadingForm"
      element-loading-background="rgba(122, 122, 122, 0.8)"
      element-loading-text="Loading..."
      :model="newOrder"
      class="form-container"
    >
      <sticky :class-name="'sub-navbar ' + newOrder.status">
        <!-- <el-button
          v-loading="loading"
          style="margin-left: 10px"
          type="success"
          @click="saveOrder()"
        >
          Submit
        </el-button> -->
        <router-link :to="'/production-request/list'">
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
                    : {{ newOrder.nomor }}
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
                    : {{ newOrder.date }}
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
                    : {{ newOrder.customer_name }}
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
                    : {{ newOrder.npwp }}
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
                    : {{ newOrder.category_name }}
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
                    : {{ newOrder.tenor_name }}
                  </el-form-item>
                </el-col>
              </el-row>
            </div>
          </el-col>
        </el-row>
        <br>
        <el-card class="box-card">
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
            <el-table-column
              align="center"
              label="Product"
              prop="product_name"
              width="400"
            />

            <el-table-column
              align="center"
              label="Qty"
              prop="qty"
              width="130"
            />

            <el-table-column
              align="center"
              label="Qty Packed"
              prop="qty_packed"
              width="130"
            />

            <el-table-column
              align="center"
              label="Price"
              prop="price"
              width="200"
            >
              <template #default="scope">
                <el-form-item prop="price">
                  {{ scope.row.price }}
                </el-form-item>
              </template>
            </el-table-column>

            <el-table-column
              align="center"
              label="Discount"
              prop="discount"
              width="200"
            />

            <el-table-column
              label="Status"
              prop="status"
              width="200"
              align="center"
            >
              <template #default="scope">
                <el-tag
                  :type="scope.row && scope.row.status | statusType"
                  @click="showQTY(scope.row.id)"
                >
                  {{ scope.row && scope.row.status | statusFilter }} :
                  {{ scope.row.stock }}
                </el-tag>
              </template>
            </el-table-column>

            <el-table-column
              align="center"
              label="Sub Total"
              prop="sub_total"
              width="250"
            />

            <el-table-column fixed="right" label="Action" width="130">
              <template #default="scope">
                <el-form-item prop="action">
                  <span v-if="scope.row.status_order == 0">
                    <el-tooltip
                      class="item"
                      effect="dark"
                      content="Production"
                      placement="top-start"
                    >
                      <el-button
                        type="primary"
                        size="small"
                        @click.prevent="handleNewOrder(scope.row)"
                      >
                        <svg-icon icon-class="package" />
                      </el-button>
                    </el-tooltip>
                  </span>

                  <span
                    v-if="
                      scope.row.stock >= scope.row.qty &&
                        scope.row.status_order <= 4
                    "
                  >
                    <el-tooltip
                      class="item"
                      effect="dark"
                      content="Packed"
                      placement="top-start"
                    >
                      <el-button
                        type="success"
                        size="small"
                        @click.prevent="handleStockPacked(scope.row)"
                      >
                        <svg-icon icon-class="box-open" />
                      </el-button>
                    </el-tooltip>
                  </span>
                </el-form-item>
              </template>
            </el-table-column>
          </el-table>
        </el-card>
      </div>
    </el-form>
    <el-dialog title="Form Packed" :visible.sync="dialogFormPacked" width="60%">
      <el-form ref="PackedForm" :model="newPacked">
        <el-table
          ref="multipleTable"
          v-loading="listLoadingStockIn"
          :data="tableDataStock"
          style="width: 100%"
          @selection-change="handleSelectionChange"
        >
          <el-table-column type="selection" width="55" />
          <el-table-column align="center" label="Date" width="150">
            <template slot-scope="scope">
              <span>{{
                scope.row.created_at | parseTime('{d}-{m}-{y} {h}:{i}')
              }}</span>
            </template>
          </el-table-column>
          <el-table-column align="center" label="QR Code" width="120">
            <template slot-scope="scope">
              <qr-code :text="scope.row.control_id.toString()" :size="100" />
            </template>
          </el-table-column>
          <el-table-column label="Control ID">
            <template slot-scope="scope">
              <span>{{ scope.row.control_id }} </span>
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
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="dialogFormPacked = false">Cancel</el-button>
        <el-button type="primary" @click="createPacked()">Confirm</el-button>
      </span>
    </el-dialog>
  </div>
</template>
<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import Sticky from '@/components/Sticky';
import {
  ShowOrder,
  ShowOrderDetail,
  ConfirmManufacture,
  ConfirmPacked,
} from '@/api/sales/order';
import { fetchListStokIn } from '@/api/production/stock';

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
    Pagination,
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
      total: 0,
      loading: false,
      loadingForm: false,
      LoadingStockOUT: false,
      listLoadingStockIn: false,
      listLoading: false,
      newManufaturingOrder: {},
      newPacked: {
        items: [],
      },
      dialogFormManufacturing: false,
      dialogFormPacked: false,
      LoadingForm: false,
      tableDataStock: [],
      query: { page: 1, limit: 15, keyword: '', role: '', sort: '-id' },
      newOrder: {
        items: [],
      },
      postForm: Object.assign({}, defaultForm),
      rulesPacked: {
        qty: [
          { required: true, message: 'Quantity is required', trigger: 'blur' },
        ],
      },
      rulesManufacturing: {
        date: [
          { required: true, message: 'Date is required', trigger: 'blur' },
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
    const id = this.$route.params && this.$route.params.id;
    this.fetchDataOrder(id);
    this.resetNewFormManufacturing();
  },
  methods: {
    resetFormStockPacked() {
      this.newStock = {};
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
            category: response.data.category,
            customer_name: response.data.customer_name,
            tenor_name: response.data.tenor_name,
            category_name:
              response.data.category_name === 1 ? 'Cash' : 'Credit',
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
              product_name: element.product_name,
              total_timing: element.total_timing,
              qty_packed: element.qty_packed,
              sub_total: parseFloat(element.total_price),
              status_order: element.status,
            });
          });
          this.loadingForm = false;
        })
        .catch((err) => {
          console.log(err);
        });
    },
    resetNewFormManufacturing() {
      this.newManufaturingOrder = {
        id: '',
        timing: '',
        qty: '',
        role: 'deposit',
      };
      this.dataDeposit = { amount: 0 };
    },
    handleNewOrder(data) {
      this.LoadingForm = true;
      this.newManufaturingOrder.product_id = data.product;
      this.newManufaturingOrder.total_timing = data.total_timing;
      this.newManufaturingOrder.qty = data.qty;
      this.newManufaturingOrder.detail_id = data.detail_id;
      this.LoadingForm = false;
      this.dialogFormManufacturing = true;
    },
    createManufacture() {
      this.$refs['ManufaturingOrderForm'].validate((valid) => {
        if (valid) {
          this.newManufaturingOrder.roles = [this.newManufaturingOrder.role];
          this.LoadingForm = true;
          ConfirmManufacture(this.newManufaturingOrder)
            .then((response) => {
              this.$message({
                message: 'The order status has entered the production queue.',
                type: 'success',
                duration: 5 * 1000,
              });
              this.dialogFormManufacturing = false;
              this.handleFilter();
            })
            .catch((error) => {
              console.log(error);
            })
            .finally(() => {
              this.LoadingForm = false;
            });
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
    handleSelectionChange(val) {
      this.newPacked.items = val;
    },
    handleStockPacked(dataRequest) {
      this.listLoadingStockIn = true;
      this.dialogFormPacked = true;
      this.newPacked.detail_id = dataRequest.detail_id;
      this.newPacked.nomor = this.newOrder.nomor;
      this.resetFormStockPacked();
      this.query.product_id = dataRequest.product;
      this.getList();
    },
    getList() {
      this.listLoadingStockIn = true;
      const { limit, page } = this.query;
      fetchListStokIn(this.query).then((response) => {
        this.tableDataStock = response.data;
        this.tableDataStock.forEach((element, index) => {
          element['index'] = (page - 1) * limit + index + 1;
        });
        this.total = response.meta.total;
        this.listLoadingStockIn = false;
      });
    },
    createPacked() {
      this.$refs['PackedForm'].validate((valid) => {
        if (valid) {
          this.newPacked.roles = [this.newPacked.role];
          this.LoadingForm = true;
          ConfirmPacked(this.newPacked)
            .then((response) => {
              this.$message({
                message: 'Packed successfully.',
                type: 'success',
                duration: 5 * 1000,
              });
              this.dialogFormPacked = false;
              this.$router.go(this.$router.currentRoute);
            })
            .catch((error) => {
              console.log(error);
            })
            .finally(() => {
              this.LoadingForm = false;
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
