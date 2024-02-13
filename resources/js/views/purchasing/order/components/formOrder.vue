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
                    style="width: 80%"
                    label-width="100px"
                    label="Nomor :"
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
                    label-width="125px"
                    label="Date :"
                    class="postInfo-container-item"
                    style="width: 80%"
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
            </div>
          </el-col>
        </el-row>
        <br>
        <el-card class="box-card">
          <el-col :span="10">
            <el-form-item
              label-width="200px"
              label="Supplier : "
              class="postInfo-container-item"
            >
              <el-select
                v-model="newOrder.supplier"
                class="filter-item"
                placeholder="Please select"
                style="width: 80%"
                @change="onShowSupplier($event)"
              >
                <el-option
                  v-for="item_supplier in supplierOptions"
                  :key="item_supplier.id"
                  :label="item_supplier.name"
                  :value="item_supplier.id"
                />
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="14">
            <el-button
              style="float: right"
              type="success"
              plain
              :disabled="isDisabledAddRow"
              @click="addRow"
            >
              Add item
            </el-button>
          </el-col>
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
            <el-table-column align="center" label="Name" width="400">
              <template #default="scope">
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
              </template>
            </el-table-column>
            <el-table-column
              align="center"
              label="Recomendation Price"
              prop="recomend_price"
              width="130"
            >
              <template #default="scope">
                <el-form-item prop="recomend_price">
                  <el-input
                    v-model="scope.row.recomend_price"
                    placeholder="type recomend price here"
                    style="text-align: right"
                  />
                </el-form-item>
              </template>
            </el-table-column>

            <el-table-column
              align="center"
              label="Recomendation Order"
              prop="recomend_order"
              width="130"
            >
              <template #default="scope">
                <el-form-item prop="recomend_order">
                  <el-input
                    v-model="scope.row.recomend_qty"
                    placeholder="type recomend order here"
                    :disabled="true"
                  />
                </el-form-item>
              </template>
            </el-table-column>

            <el-table-column
              align="center"
              label="Qty Request"
              prop="qty_request"
              width="130"
            >
              <template #default="scope">
                <el-form-item prop="qty_request">
                  <el-input
                    v-model="scope.row.qty_request"
                    placeholder="type recomend order here"
                    :disabled="true"
                  />
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
              label="Unit"
              prop="unit"
              width="200"
            >
              <template #default="scope">
                <el-form-item prop="unit">
                  <el-input
                    v-model="scope.row.unit_name"
                    placeholder="type qty here"
                    :disabled="true"
                  />
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
                <el-button
                  type="danger"
                  size="small"
                  @click.prevent="handleDelete(scope.$index)"
                >Delete</el-button>
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
  fetchNoPO,
  createOrder,
  ShowOrder,
  ShowOrderDetail,
} from '@/api/purchasing/order';
import { fetchList as listSupplier } from '@/api/production/master/supplier';
import { fetchSubmitBySupplier } from '@/api/purchasing/submit';
import { fetchProductSettingBySupplier } from '@/api/purchasing/master/setting';

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
      isDisabledAddRow: true,
      newOrder: {
        items: [],
      },
      productOptions: {},
      supplierOptions: {},
      postForm: Object.assign({}, defaultForm),
      id_supplier: null,
      rules: {
        name: [
          { required: true, message: 'Name is required', trigger: 'blur' },
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
      fetchNoPO().then((response) => {
        this.newOrder.nomor = response.data.no_po;
        this.newOrder.date = response.data.date;
        this.loadingForm = false;
      });
    },
    loadOptions() {
      listSupplier().then((response) => {
        this.supplierOptions = response.data;
      });
    },
    async onShowSupplier(event) {
      this.newOrder.items = [];
      this.listLoading = true;
      fetchProductSettingBySupplier(event).then((response) => {
        this.productOptions = response.data;
      });
      const { data } = await fetchSubmitBySupplier(event);
      this.isDisabledAddRow = false;
      if (data.length === 0) {
        this.$message({
          message: 'Data not found',
          type: 'success',
          duration: 5 * 1000,
        });
      } else {
        data.forEach((element, index) => {
          this.newOrder.items.push({
            product: element.product_id,
            code: element.product_code,
            recomend_price: element.unit_price,
            qty: element.qty_request,
            recomend_qty: element.recomend_qty,
            qty_request: element.qty_request,
            unit_name: element.unit_name,
            submit_id: element.submit_id,
            sub_total:
              parseInt(element.unit_price) * parseInt(element.qty_request),
            edit: false,
          });
        });
      }
      this.listLoading = false;
    },
    change(row, event) {
      row.sub_total =
        parseInt(event.target.value) * parseInt(row.recomend_price);
    },
    addRow() {
      this.newOrder.items.push({
        product: '',
        code: '',
        recomend_price: '',
        recomend_qty: '',
        qty: '0',
        qty_request: '0',
        unit_name: '',
        sub_total: '0',
      });
    },
    async onShowProduct(row, event) {
      const dataProduct = this.productOptions.find(
        (combo) => combo.id === event
      );
      row.recomend_price = dataProduct.unit_price;
      row.recomend_qty = dataProduct.recomend_qty;
      row.unit_name = dataProduct.unit_name;
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
      this.listLoading = true;
      ShowOrder(id)
        .then((response) => {
          this.newOrder = {
            id: response.data.id,
            nomor: response.data.nomor,
            date: response.data.created_at,
            supplier: response.data.m_supplier_id,
            items: [],
          };
          this.listLoading = false;

          fetchProductSettingBySupplier(response.data.m_supplier_id).then(
            (response) => {
              this.productOptions = response.data;
            }
          );
        })
        .catch((err) => {
          console.log(err);
        });
      ShowOrderDetail(id)
        .then((response) => {
          response.data.forEach((element, index) => {
            this.newOrder.items.push({
              product: element.product_id,
              code: element.product_code,
              recomend_price: element.unit_price,
              qty: element.qty_request,
              recomend_qty: element.recomend_qty,
              qty_request: element.qty_request,
              unit_name: element.unit_name,
              submit_id: element.submit_id,
              sub_total:
                parseInt(element.unit_price) * parseInt(element.qty_request),
              edit: false,
            });
          });
          this.unique = response.data.filter(
            (obj, index) =>
              response.data.findIndex(
                (item) => item.workstation_id === obj.workstation_id
              ) === index
          );

          this.loadingEdit = false;
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
              this.$router.push({ path: '/purchasing-order/list' });
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
