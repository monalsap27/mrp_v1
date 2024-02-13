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
          @click="saveDeliveryOrder()"
        >
          Submit
        </el-button>
        <router-link :to="'/sales-shipping/list'">
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
              label="Order : "
              class="postInfo-container-item"
            >
              <el-select
                v-model="newDelivery.order"
                class="filter-item"
                placeholder="Please select"
                style="width: 80%"
                @change="onShowOrder($event)"
              >
                <el-option
                  v-for="item_order in orderOptions"
                  :key="item_order.id"
                  :label="item_order.nomor"
                  :value="item_order.id"
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
            <el-table-column
              align="center"
              label="Qty"
              prop="qty_packed"
              width="250"
            >
              <template #default="scope">
                <el-form-item prop="qty_packed">
                  <el-input-number
                    v-model="scope.row.qty_packed"
                    placeholder="type qty_packed here"
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
        :data="dataOrderDetail"
        style="width: 100%"
        @selection-change="handleSelectionChange"
      >
        <el-table-column type="selection" width="55" />
        <el-table-column prop="product_name" label="Product" />
        <el-table-column prop="product_code" label="Code" width="250" />
        <el-table-column prop="qty_packed" label="Quantity" width="100" />
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
import { fetchNoDO, createDeliveryOrder } from '@/api/sales/delivery_order';
import { fetchList, ShowDetailByOrder } from '@/api/sales/order';

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
      dataOrder: null,
      dataOrderDetail: [],
      idOrder: null,
      loadingDrawer: false,
      newDelivery: {
        items: [],
      },
      productOptions: {},
      orderOptions: {},
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
      fetchList().then((response) => {
        this.orderOptions = response.data;
      });
    },
    async onShowOrder(event) {
      this.isDisabledAddRow = false;
      this.idOrder = event;
    },
    addRow() {
      this.drawerShow = true;
      this.loadingDrawer = true;
      this.dataOrderDetail = null;
      this.dataOrderDetail = [];
      ShowDetailByOrder(this.idOrder)
        .then((response) => {
          response.data.forEach((element, index) => {
            if (element.qty !== 0) {
              this.dataOrderDetail.push({
                product_name: element.product_name,
                product_code: element.product_code,
                product_id: element.product_id,
                order_id: element.order_id,
                qty_packed: element.qty_packed,
                nomor: element.nomor,
                detail_id: element.detail_id,
              });
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
    saveDeliveryOrder() {
      this.$refs['postForm'].validate((valid) => {
        if (valid) {
          this.newDelivery.roles = [this.newDelivery.role];
          this.loading = true;
          createDeliveryOrder(this.newDelivery)
            .then((response) => {
              this.$message({
                message: response.message,
                type: 'success',
                duration: 5 * 1000,
              });
              this.$router.push({ path: '/sales-shipping/list' });
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
          order_id: element.order_id,
          product_id: element.product_id,
          qty_packed: element.qty_packed,
          product_name: element.product_name,
          nomor: element.nomor,
          product_code: element.product_code,
          detail_id: element.detail_id,
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
