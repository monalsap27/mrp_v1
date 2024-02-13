<template>
  <div class="createPost-container">
    <el-form
      ref="postForm"
      v-loading.fullscreen.lock="loadingForm"
      element-loading-background="rgba(122, 122, 122, 0.8)"
      element-loading-text="Loading..."
      :model="newDelivery"
      :rules="rules"
      class="form-container"
    >
      <sticky :class-name="'sub-navbar ' + newDelivery.status">
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
                      v-model="newDelivery.nomor"
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
                      v-model="newDelivery.date"
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
              label="Transaction : "
              class="postInfo-container-item"
            >
              <el-select
                v-model="newDelivery.transaction"
                class="filter-item"
                placeholder="Please select"
                style="width: 80%"
                @change="onShowTransaction($event)"
              >
                <el-option
                  v-for="item_transaction in transactionOptions"
                  :key="item_transaction.id"
                  :label="item_transaction.nomor"
                  :value="item_transaction.id"
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
            :data="newDelivery.items"
            border
            style="width: 100%"
          >
            <el-table-column
              align="center"
              prop="nomor"
              label="Nomor"
              width="200"
            />
            <el-table-column
              align="center"
              prop="product_name"
              label="Product"
            />
            <el-table-column align="center" prop="product_code" label="Code" />
            <el-table-column align="center" label="Qty" prop="qty" width="250">
              <template #default="scope">
                <el-form-item prop="qty">
                  <el-input-number
                    v-model="scope.row.qty"
                    placeholder="type qty here"
                    :min="1"
                  />
                </el-form-item>
              </template>
            </el-table-column>

            <el-table-column label="Action" width="90">
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
    <el-drawer
      title="I have a nested table inside!"
      :visible.sync="drawerShow"
      direction="rtl"
      size="40%"
    >
      <el-table
        ref="multipleTable"
        v-loading="loadingDrawer"
        :data="dataTransactionDetail"
        style="width: 100%"
        @selection-change="handleSelectionChange"
      >
        <el-table-column type="selection" width="55" />
        <el-table-column prop="product_name" label="Product" />
        <el-table-column prop="product_code" label="Code" width="250" />
        <el-table-column prop="qty" label="Quantity" width="100" />
      </el-table>
      <div class="demo-drawer__footer">
        <el-button>Cancel</el-button>
        <el-button type="primary" :loading="loading" @click="closeDrawer()">{{
          loading ? 'Submitting ...' : 'Submit'
        }}</el-button>
      </div>
    </el-drawer>
  </div>
</template>
<script>
import Sticky from '@/components/Sticky';
import {
  fetchNoDO,
  ShowDetailTransaction,
  createOrder,
} from '@/api/vendor/delivery_order';
import { fetchTransaction } from '@/api/vendor/request';

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
      drawerShow: false,
      dataTransaction: null,
      dataTransactionDetail: [],
      idTransaction: null,
      loadingDrawer: false,
      newDelivery: {
        items: [],
      },
      productOptions: {},
      transactionOptions: {},
      postForm: Object.assign({}, defaultForm),
      id_supplier: null,
      rules: {
        name: [
          { required: true, message: 'Name is required', trigger: 'blur' },
        ],
      },
      multipleSelection: [],
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
      this.loadNomorDO();
    }
  },
  methods: {
    loadNomorDO() {
      this.loadingForm = true;
      fetchNoDO().then((response) => {
        this.newDelivery.nomor = response.data.no_do;
        this.newDelivery.date = response.data.date;
        this.loadingForm = false;
      });
    },
    loadOptions() {
      fetchTransaction().then((response) => {
        this.transactionOptions = response.data;
      });
    },
    async onShowTransaction(event) {
      this.isDisabledAddRow = false;
      this.idTransaction = event;
    },
    addRow() {
      this.drawerShow = true;
      this.loadingDrawer = true;
      this.dataTransactionDetail = null;
      this.dataTransactionDetail = [];
      ShowDetailTransaction(this.idTransaction)
        .then((response) => {
          response.data.forEach((element, index) => {
            if (element.qty !== 0) {
              this.dataTransactionDetail.push({
                product_name: element.product_name,
                product_code: element.product_code,
                product_id: element.product_id,
                transaction_id: element.transaction_id,
                qty: element.qty,
                nomor: element.nomor,
              });
              console.log(this.dataTransactionDetail);
            }
          });
          this.loadingDrawer = false;
        })
        .catch((err) => {
          console.log(err);
        });
    },
    handleDelete(index) {
      this.newDelivery.items.splice(index, 1);
    },
    saveOrder() {
      this.$refs['postForm'].validate((valid) => {
        if (valid) {
          this.newDelivery.roles = [this.newDelivery.role];
          this.loading = true;
          createOrder(this.newDelivery)
            .then((response) => {
              this.$message({
                message: response.message,
                type: 'success',
                duration: 5 * 1000,
              });
              this.$router.push({ path: '/vendor-shipping/list' });
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
    closeDrawer() {
      this.loading = true;
      this.multipleSelection.forEach((element, index) => {
        this.newDelivery.items.push({
          transaction_id: element.transaction_id,
          product_id: element.product_id,
          qty: element.qty,
          product_name: element.product_name,
          nomor: element.nomor,
          product_code: element.product_code,
        });
        this.loading = false;
        this.drawerShow = false;
      });
    },
    toggleSelection(rows) {
      if (rows) {
        rows.forEach((row) => {
          this.$refs.multipleTable.toggleRowSelection(row);
        });
      } else {
        this.$refs.multipleTable.clearSelection();
      }
    },
    handleSelectionChange(val) {
      this.multipleSelection = val;
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
