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
      <el-table-column align="center" label="Npwp" prop="npwp" />

      <el-table-column align="center" label="Customer" prop="customer" />

      <el-table-column align="center" label="Balance" prop="balance">
        <template slot-scope="scope">
          {{ scope.row.balance | toThousandFilter }}
        </template>
      </el-table-column>

      <el-table-column align="center" label="Actions" width="100">
        <template slot-scope="scope">
          <el-tooltip
            class="item"
            effect="dark"
            content="Balance Mutation"
            placement="top-start"
          >
            <el-button
              type="info"
              size="small"
              @click="handleMutation(scope.row.id)"
            >
              <svg-icon icon-class="time-past" />
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
      :title="'Form Deposit'"
      :visible.sync="dialogFormVisible"
      width="50%"
    >
      <div v-loading="depositCreating" class="form-container">
        <el-form
          ref="depositForm"
          :rules="rules"
          :model="newDeposit"
          label-position="left"
          label-width="150px"
          style="max-width: 100%"
        >
          <el-form-item :label="$t('Customer')" prop="customer">
            <el-select
              v-model="newDeposit.customer"
              filterable
              placeholder="Select"
              style="width: 100%"
              @input="handleChangeCustomer"
            >
              <el-option
                v-for="item in customerOptions"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              />
            </el-select>
          </el-form-item>
          <el-form-item :label="$t('Bank')" prop="bank">
            <el-select
              v-model="newDeposit.bank"
              filterable
              placeholder="Select"
              style="width: 100%"
              @input="handleChangeBank"
            >
              <el-option
                v-for="item in bankOptions"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              />
            </el-select>
          </el-form-item>
          <el-form-item :label="$t('Amount')" prop="amount">
            <el-input
              v-model="newDeposit.amount"
              placeholder="type Amount here"
            />
          </el-form-item>
          <el-form-item :label="$t('Note')" prop="note">
            <el-input v-model="newDeposit.note" placeholder="type note here" />
          </el-form-item>
          <el-form-item :label="$t('Deposit Balance')" prop="note">
            {{ dataDeposit.amount | toThousandFilter }}
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="createDeposit()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>
    <el-drawer :visible.sync="drawerMutation" direction="rtl" size="50%">
      <el-table :data="dataMutation">
        <el-table-column prop="created_at" label="Date" width="150">
          <template slot-scope="scope">
            <span>{{
              scope.row.created_at | parseTime('{d}-{m}-{y} {h}:{i}')
            }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="amount" label="Amount" width="200">
          <template slot-scope="scope">
            <span v-if="scope.row.type == '+'" style="color: chartreuse">
              {{ scope.row.type }} {{ scope.row.amount | toThousandFilter }}
            </span>
            <span v-else style="color: crimson">
              {{ scope.row.type }} {{ scope.row.amount | toThousandFilter }}
            </span>
          </template>
        </el-table-column>
        <el-table-column prop="note" label="Note" />
      </el-table>
    </el-drawer>
  </div>
</template>

<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import {
  fetchList,
  createDeposit,
  showByCustomer,
  dataTransaction,
} from '@/api/customer/deposit';
import { fetchList as listCustomer } from '@/api/customer/customer';
import { fetchList as listBank } from '@/api/sales/master/bank';

export default {
  name: 'PostalCodeList',
  components: { Pagination },
  filters: {},
  data() {
    return {
      total: 0,
      list: null,
      listLoading: true,
      depositEdit: false,
      depositCreating: false,
      dialogFormVisible: false,
      dialogEditVisible: false,
      drawerMutation: false,
      dataMutation: null,
      dataDeposit: { amount: 0 },
      newDeposit: {},
      currentDeposit: {},
      customerOptions: {},
      bankOptions: {},
      defaultProps: { children: 'children', label: 'label' },
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        role: '',
        sort: '-id',
      },
      rules: {
        customer: [
          { required: true, message: 'Customer is required', trigger: 'blur' },
        ],
        bank: [
          { required: true, message: 'Bank is required', trigger: 'blur' },
        ],
        amount: [
          { required: true, message: 'Amount is required', trigger: 'blur' },
        ],
      },
    };
  },
  created() {
    this.resetNewForm();
    this.getList();
    this.loadOption();
  },
  methods: {
    loadOption() {
      listCustomer().then((response) => {
        this.customerOptions = response.data;
      });
      listBank().then((response) => {
        this.bankOptions = response.data;
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
    createDeposit() {
      this.$refs['depositForm'].validate((valid) => {
        if (valid) {
          this.newDeposit.roles = [this.newDeposit.role];
          this.depositCreating = true;
          createDeposit(this.newDeposit)
            .then((response) => {
              this.$message({
                message: 'Top Up Deposit  has been successfully.',
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
              this.depositCreating = false;
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
      this.newDeposit = {
        id: '',
        note: '',
        role: 'deposit',
      };
      this.dataDeposit = { amount: 0 };
    },
    handleCreate() {
      this.resetNewForm();
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['depositForm'].clearValidate();
      });
    },
    handleChangeCustomer(value) {
      showByCustomer(value).then((response) => {
        this.dataDeposit = response.data;
      });
    },
    handleChangeBank(value) {
      const foundBank = this.bankOptions.find((bank) => bank.id === value);
      this.newDeposit.bank_name = foundBank.name;
      this.newDeposit.no_rek = foundBank.no_rek;
    },
    handleMutation(id) {
      dataTransaction(id).then((response) => {
        this.dataMutation = response.data;
      });
      this.drawerMutation = true;
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
