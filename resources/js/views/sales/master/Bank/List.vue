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
      <el-table-column align="center" label="Name" prop="name" />

      <el-table-column align="center" label="No Rekening" prop="no_rek" />

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
              @click="handleEditBank(scope.row.id)"
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
      :title="'Create Bank'"
      :visible.sync="dialogFormVisible"
      width="40%"
    >
      <div v-loading="paymentMethodCreating" class="form-container">
        <el-form
          ref="paymentMethodForm"
          :rules="rules"
          :model="newBank"
          label-position="left"
          label-width="150px"
          style="max-width: 100%"
        >
          <el-form-item :label="$t('Name')" prop="name">
            <el-input v-model="newBank.name" placeholder="type name here" />
          </el-form-item>
          <el-form-item :label="$t('No Rekening')" prop="no_rek">
            <el-input
              v-model="newBank.no_rek"
              placeholder="type No Rekening here"
            />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="createBank()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>

    <el-dialog
      :title="'Edit Bank'"
      :visible.sync="dialogEditVisible"
      width="40%"
    >
      <div v-loading="paymentMethodEdit" class="form-container">
        <el-form
          ref="paymentMethodFormEdit"
          :rules="rules"
          :model="currentBank"
          label-position="left"
          label-width="150px"
          style="max-width: 100%"
        >
          <el-form-item :label="$t('Name')" prop="name">
            <el-input v-model="currentBank.name" placeholder="type name here" />
          </el-form-item>
          <el-form-item :label="$t('No Rekening')" prop="no_rek">
            <el-input
              v-model="currentBank.no_rek"
              placeholder="type No Rekening here"
            />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogEditVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="updateBank()">
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
  createOrUpdateBank,
  DeleteBank,
} from '@/api/sales/master/bank';

export default {
  name: 'PostalCodeList',
  components: { Pagination },
  filters: {},
  data() {
    return {
      total: 0,
      list: null,
      listLoading: true,
      paymentMethodEdit: false,
      paymentMethodCreating: false,
      dialogFormVisible: false,
      dialogEditVisible: false,
      newBank: {},
      currentBank: {},
      defaultProps: { children: 'children', label: 'label' },
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        role: '',
        sort: '-id',
      },
      rules: {
        name: [
          { required: true, message: 'Name is required', trigger: 'blur' },
        ],
        no_rek: [
          {
            required: true,
            message: 'No Rekening is required',
            trigger: 'blur',
          },
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
    loadOption() {},
    async getList() {
      const { limit, page } = this.query;
      this.listLoading = true;
      const { data, meta } = await fetchList(this.query);
      console.log(data);
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
    createBank() {
      this.$refs['paymentMethodForm'].validate((valid) => {
        console.log(valid);
        if (valid) {
          this.newBank.roles = [this.newBank.role];
          this.paymentMethodCreating = true;
          createOrUpdateBank(this.newBank)
            .then((response) => {
              this.$message({
                message:
                  'New Bank ' +
                  this.newBank.name +
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
              this.paymentMethodCreating = false;
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
      this.newBank = {
        id: '',
        name: '',
        description: '',
        role: 'paymentMethod',
      };
    },
    handleCreate() {
      this.resetNewForm();
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['paymentMethodForm'].clearValidate();
      });
    },
    async handleEditBank(id) {
      this.currentId = id;
      this.paymentMethodEdit = true;
      this.dialogEditVisible = true;
      const foundBank = this.list.find(
        (paymentMethod) => paymentMethod.id === id
      );
      this.currentBank = {
        id: foundBank.id,
        name: foundBank.name,
        no_rek: foundBank.no_rek,
        provinces: foundBank.m_provinces_id,
      };
      this.paymentMethodEdit = false;
    },
    updateBank() {
      this.paymentMethodEdit = true;
      createOrUpdateBank(this.currentBank)
        .then((response) => {
          this.$message({
            message:
              'Bank ' +
              this.currentBank.name +
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
          this.paymentMethodEdit = false;
        });
    },

    handleDelete(id, name) {
      this.$confirm(
        'This will permanently delete bank ' + name + '. Continue?',
        'Warning',
        {
          confirmButtonText: 'OK',
          cancelButtonText: 'Cancel',
          type: 'warning',
        }
      )
        .then(() => {
          DeleteBank(id)
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
