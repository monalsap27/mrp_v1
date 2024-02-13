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
      <el-button class="filter-item" type="primary" icon="el-icon-search">
        {{ $t('table.search') }}
      </el-button>
      <router-link :to="'/sales-order/create'">
        <el-button
          class="filter-item"
          style="margin-left: 10px"
          type="primary"
          icon="el-icon-plus"
        >
          {{ $t('table.add') }}
        </el-button>
      </router-link>
    </div>

    <el-table
      v-loading="listLoading"
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 100%"
    >
      <el-table-column align="center" label="Date" width="150">
        <template slot-scope="scope">
          <span>{{
            scope.row.created_at | parseTime('{d}-{m}-{y} {h}:{i}')
          }}</span>
        </template>
      </el-table-column>

      <el-table-column
        align="center"
        label="Nomor"
        width="130px"
        prop="nomor"
      />

      <el-table-column
        align="center"
        label="Customer"
        width="200px"
        prop="customer_name"
      />

      <el-table-column align="center" label="NPWP" width="200px" prop="npwp" />

      <el-table-column
        align="center"
        label="Total Price"
        width="200px"
        prop="total_price"
      >
        <template slot-scope="scope">
          {{ scope.row.total_price | toThousandFilter }}
        </template>
      </el-table-column>

      <el-table-column
        align="center"
        label="Total Payment"
        width="200px"
        prop="total_price"
      >
        <template slot-scope="scope">
          {{ scope.row.total_payment | toThousandFilter }}
        </template>
      </el-table-column>

      <el-table-column
        align="center"
        label="Created By"
        min-width="200px"
        prop="created_name"
      />

      <el-table-column align="center" label="Status" width="150px">
        <template slot-scope="scope">
          <span v-if="scope.row.status == 3">
            <el-tag
              :type="scope.row && scope.row.status | statusType"
              @click="showQTY(scope.row.id)"
            >
              {{ scope.row && scope.row.status | statusFilter }}
            </el-tag>
          </span>
          <span v-else-if="scope.row.status == 4">
            <el-tag
              :type="scope.row && scope.row.status | statusType"
              @click="showDelivery(scope.row.id)"
            >
              {{ scope.row && scope.row.status | statusFilter }} :
              {{ scope.row.count_delivery }}
            </el-tag>
          </span>
          <span v-else>
            <el-tag :type="scope.row && scope.row.status | statusType">
              {{ scope.row && scope.row.status | statusFilter }}
            </el-tag>
          </span>
        </template>
      </el-table-column>
      <el-table-column align="center" fixed="right" label="Actions" width="150">
        <template #default="scope">
          <el-tooltip
            class="item"
            effect="dark"
            content="Detail"
            placement="top-start"
          >
            <router-link :to="'/purchasing-order/detailOrder/' + scope.row.id">
              <el-button type="info" size="small">
                <svg-icon icon-class="eye-melek" />
              </el-button>
            </router-link>
          </el-tooltip>
          <span v-if="scope.row.status == 3 || scope.row.status == 1">
            <el-tooltip
              class="item"
              effect="dark"
              content="Payment"
              placement="top-start"
            >
              <el-button
                type="success"
                size="small"
                @click="handlePayment(scope.row.id)"
              >
                <svg-icon icon-class="wallet-arrow" />
              </el-button>
            </el-tooltip>
          </span>

          <span v-if="scope.row.status == 1">
            <el-tooltip
              class="item"
              effect="dark"
              content="Edit"
              placement="top-start"
            >
              <router-link :to="'/sales-order/edit/' + scope.row.id">
                <el-button type="warning" size="small" icon="el-icon-edit" />
              </router-link>
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
      :title="'Form Payment'"
      :visible.sync="dialogFormVisible"
      width="50%"
    >
      <div v-loading="loadingPayment" class="form-container">
        <el-form
          ref="paymentForm"
          :rules="rules"
          :model="newPayment"
          label-position="left"
          label-width="150px"
          style="max-width: 100%"
        >
          <el-form-item :label="$t('Deposit')" prop="deposit">
            <el-checkbox v-model="newPayment.deposit" border>{{ newPayment.amount | toThousandFilter }}
            </el-checkbox>
          </el-form-item>
          <el-form-item :label="$t('Payment Method')" prop="paymentMethod">
            <el-select
              v-model="newPayment.paymentMethod"
              filterable
              placeholder="Select"
              style="width: 100%"
            >
              <el-option
                v-for="item in paymentMethodOptions"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              />
            </el-select>
          </el-form-item>
          <el-form-item :label="$t('Bank')" prop="bank">
            <el-select
              v-model="newPayment.bank"
              filterable
              placeholder="Select"
              style="width: 100%"
            >
              <el-option
                v-for="item in bankOptions"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              />
            </el-select>
          </el-form-item>
          <el-form-item :label="$t('Total Payment')" prop="totalPayment">
            <el-input
              v-model="newPayment.totalPayment"
              placeholder="type Total Payment here"
            />
          </el-form-item>
          <el-form-item :label="$t('Total Invoice')" prop="totalInvoice">
            {{ newPayment.total_price | toThousandFilter }}
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="createPayment()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import { fetchList } from '@/api/sales/order';
import { showQtyVendor } from '@/api/vendor/request';
import { ShowDeliveryByTransaction } from '@/api/vendor/delivery_order';
import { showByCustomer } from '@/api/customer/deposit';
import { fetchList as listPaymentMethod } from '@/api/sales/master/payment_method';
import { fetchList as listBank } from '@/api/sales/master/bank';
import { createPayment } from '@/api/sales/payment';

export default {
  name: 'SupplierList',
  components: { Pagination },
  filters: {
    statusType(status) {
      const statusMap = {
        1: 'info',
        2: 'warning',
        3: 'warning',
        4: 'primary',
        5: 'primary',
        6: 'success',
        7: 'success',
      };
      return statusMap[status];
    },
    statusFilter(status) {
      const statusMap = {
        1: 'Waiting Payment',
        2: 'Paid',
        3: 'Partial Payment',
        4: 'Delivery',
        5: 'Partial Delivery',
        6: 'Done',
        7: 'Partial Done',
      };
      return statusMap[status];
    },
    orderNoFilter(str) {
      return str;
    },
  },
  data() {
    return {
      total: 0,
      list: null,
      listQtyProduct: null,
      listDeliveryDone: [],
      listDeliveryInProcess: [],
      loadingPayment: false,
      listLoading: true,
      drawerQuantity: false,
      drawerDelivery: false,
      loadingDelivery: false,
      dialogFormVisible: false,
      newPayment: {},
      dataDeposit: {},
      paymentMethodOptions: {},
      bankOptions: {},
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        role: '',
        sort: '-id',
      },
      rules: {
        note: [
          { required: true, message: 'Note is required', trigger: 'blur' },
        ],
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
    handlePayment(id) {
      this.resetNewFormPayment();
      this.$nextTick(() => {
        this.$refs['paymentForm'].clearValidate();
      });
      this.loadingPayment = true;
      const foundDataOrder = this.list.find((DataOrder) => DataOrder.id === id);
      this.newPayment.order_id = foundDataOrder.id;
      this.newPayment.total_price = foundDataOrder.total_price;
      if (foundDataOrder.total_payment >= 0) {
        this.newPayment.total_price =
          parseFloat(foundDataOrder.total_price) -
          parseFloat(foundDataOrder.total_payment);
      }
      this.newPayment.customer_id = foundDataOrder.customer_id;
      this.newPayment.nomor = foundDataOrder.nomor;
      showByCustomer(foundDataOrder.customer_id).then((response) => {
        this.dataDeposit = response.data;
        if (
          parseFloat(foundDataOrder.total_price) >
          parseFloat(response.data.amount)
        ) {
          this.newPayment.amount = response.data.amount;
        } else {
          this.newPayment.amount = foundDataOrder.total_price;
        }
      });
      listPaymentMethod().then((response) => {
        this.paymentMethodOptions = response.data;
      });
      listBank().then((response) => {
        this.bankOptions = response.data;
        this.loadingPayment = false;
      });
      this.dialogFormVisible = true;
    },
    createPayment() {
      this.$refs['paymentForm'].validate((valid) => {
        if (valid) {
          this.newPayment.roles = [this.newPayment.role];
          this.depositCreating = true;
          createPayment(this.newPayment)
            .then((response) => {
              this.$message({
                message: 'Top Up Deposit  has been successfully.',
                type: 'success',
                duration: 5 * 1000,
              });
              this.resetNewFormPayment();
              this.dialogFormVisible = false;
              this.handleFilter();
            })
            .catch((error) => {
              console.log(error);
            })
            .finally(() => {
              this.depositCreating = false;
            });
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
    resetNewFormPayment() {
      this.newPayment = { id: '', bank: '', deposit: '', role: 'deposit' };
      this.dataDeposit = { amount: 0 };
    },

    // handleDelete(id, name) {
    //   this.$confirm('This will permanently delete  Continue?', 'Warning', {
    //     confirmButtonText: 'OK',
    //     cancelButtonText: 'Cancel',
    //     type: 'warning',
    //   })
    //     .then(() => {
    //       DeleteOrder(id)
    //         .then((response) => {
    //           this.$message({
    //             type: 'success',
    //             message: 'Delete completed',
    //           });
    //           this.handleFilter();
    //         })
    //         .catch((error) => {
    //           console.log(error);
    //         });
    //     })
    //     .catch(() => {
    //       this.$message({
    //         type: 'info',
    //         message: 'Delete canceled',
    //       });
    //     });
    // },
    showQTY(row) {
      this.drawerQuantity = true;
      this.loadingQty = true;
      showQtyVendor(row).then((response) => {
        this.listQtyProduct = response.data;
        this.loadingQty = false;
      });
    },
    showDelivery(row) {
      this.drawerDelivery = true;
      this.loadingQty = true;
      this.listDeliveryInProcess = [];
      this.listDeliveryDone = [];
      ShowDeliveryByTransaction(row).then((response) => {
        response.data.forEach((element, index) => {
          console.log(element);
          if (element.status === 4) {
            this.listDeliveryInProcess.push({
              date: element.date,
              nomor: element.nomor,
              received_date: element.received_date,
            });
          } else {
            this.listDeliveryDone.push({
              date: element.date,
              nomor: element.nomor,
              received_date: element.received_date,
            });
          }
          // this.newDelivery.items.push({
          //   transaction_id: element.transaction_id,
          //   product_id: element.product_id,
          //   qty: element.qty,
          //   product_name: element.product_name,
          //   nomor: element.nomor,
          //   product_code: element.product_code,
          // });
        });
        this.loadingQty = false;
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
.el-tag {
  cursor: pointer;
}
</style>
