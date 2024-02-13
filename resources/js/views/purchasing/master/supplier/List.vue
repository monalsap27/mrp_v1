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
      >
        <template slot-scope="scope">
          <span>{{ scope.row.id }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Name" width="180px">
        <template slot-scope="scope">
          <span>{{ scope.row.name }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Code" width="100px">
        <template slot-scope="scope">
          <span>{{ scope.row.code }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Phone" width="100px">
        <template slot-scope="scope">
          <span>{{ scope.row.phone }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Address" min-width="300px">
        <template slot-scope="scope">
          <span>{{ scope.row.address }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Description" width="350px">
        <template slot-scope="scope">
          <span>{{ scope.row.description }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Type" width="100px">
        <template slot-scope="scope">
          <span>{{ scope.row.type | typeFilter }}</span>
        </template>
      </el-table-column>

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
              @click="handleEditSupplier(scope.row.id)"
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
      :title="'Create new supplier'"
      :visible.sync="dialogFormVisible"
      width="30%"
    >
      <div v-loading="supplierCreating" class="form-container">
        <el-form
          ref="supplierForm"
          :rules="rules"
          :model="newSupplier"
          label-position="left"
          label-width="150px"
          style="max-width: 100%"
        >
          <el-form-item :label="$t('Name')" prop="name">
            <el-input v-model="newSupplier.name" placeholder="type name here" />
          </el-form-item>
          <el-form-item :label="$t('Code')" prop="code">
            <el-input v-model="newSupplier.code" placeholder="type code here" />
          </el-form-item>
          <el-form-item :label="$t('Phone')" prop="phone">
            <el-input
              v-model="newSupplier.phone"
              placeholder="type phone here"
            />
          </el-form-item>
          <el-form-item :label="$t('Address')" prop="address">
            <el-input
              v-model="newSupplier.address"
              placeholder="type address here"
            />
          </el-form-item>
          <el-form-item :label="$t('Description')" prop="description">
            <el-input
              v-model="newSupplier.description"
              placeholder="type description here"
            />
          </el-form-item>
          <el-form-item :label="$t('Type')" prop="type">
            <el-select
              v-model="newSupplier.type"
              class="filter-item"
              placeholder="Please select"
              style="width: 100%"
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
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="createSupplier()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>
    <el-dialog
      :title="'Edit Supplier '"
      :visible.sync="dialogEditVisible"
      width="30%"
    >
      <div v-loading="supplierEdit" class="form-container">
        <el-form
          ref="supplierFormEdit"
          :rules="rules"
          :model="currentSupplier"
          label-position="left"
          label-width="150px"
          style="max-width: 100%"
        >
          <el-form-item :label="$t('Name')" prop="name">
            <el-input
              v-model="currentSupplier.name"
              placeholder="type name here"
            />
          </el-form-item>
          <el-form-item :label="$t('Code')" prop="code">
            <el-input
              v-model="currentSupplier.code"
              placeholder="type code here"
            />
          </el-form-item>
          <el-form-item :label="$t('Phone')" prop="phone">
            <el-input
              v-model="currentSupplier.phone"
              placeholder="type phone here"
            />
          </el-form-item>
          <el-form-item :label="$t('Address')" prop="address">
            <el-input
              v-model="currentSupplier.address"
              placeholder="type address here"
            />
          </el-form-item>
          <el-form-item :label="$t('Description')" prop="description">
            <el-input
              v-model="currentSupplier.description"
              placeholder="type description here"
            />
          </el-form-item>
          <el-form-item :label="$t('Type')" prop="type">
            <el-select
              v-model="currentSupplier.type"
              class="filter-item"
              placeholder="Please select"
              style="width: 100%"
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
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogEditVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="updateSupplier()">
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
  createOrUpdateSupplier,
  DeleteSupplier,
} from '@/api/production/master/supplier';

export default {
  name: 'SupplierList',
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
    typeFilter(status) {
      const statusMap = {
        1: 'Internal',
        2: 'External',
      };
      return statusMap[status];
    },
  },
  data() {
    return {
      total: 0,
      list: null,
      listLoading: true,
      supplierEdit: false,
      supplierCreating: false,
      dialogFormVisible: false,
      dialogEditVisible: false,
      newSupplier: {},
      currentSupplier: {},
      supplierOptions: [
        {
          id: 1,
          name: 'Internal',
        },
        {
          id: 2,
          name: 'External',
        },
      ],
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
        type: [
          { required: true, message: 'Type is required', trigger: 'blur' },
        ],
      },
    };
  },
  created() {
    this.resetNewForm();
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
    createSupplier() {
      this.$refs['supplierForm'].validate((valid) => {
        if (valid) {
          this.newSupplier.roles = [this.newSupplier.role];
          this.supplierCreating = true;
          createOrUpdateSupplier(this.newSupplier)
            .then((response) => {
              this.$message({
                message:
                  'New supplier ' +
                  this.newSupplier.name +
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
              this.supplierCreating = false;
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
      this.newSupplier = {
        id: '',
        name: '',
        phone: '',
        address: '',
        description: '',
        role: 'supplier',
      };
    },
    handleCreate() {
      this.resetNewForm();
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['supplierForm'].clearValidate();
      });
    },
    async handleEditSupplier(id) {
      this.currentId = id;
      this.supplierEdit = true;
      this.dialogEditVisible = true;
      const foundSupplier = this.list.find((supplier) => supplier.id === id);
      this.currentSupplier = {
        id: foundSupplier.id,
        name: foundSupplier.name,
        type: foundSupplier.type,
        code: foundSupplier.code,
        phone: foundSupplier.phone,
        address: foundSupplier.address,
        description: foundSupplier.description,
      };
      this.supplierEdit = false;
    },
    updateSupplier() {
      this.supplierEdit = true;
      createOrUpdateSupplier(this.currentSupplier)
        .then((response) => {
          this.$message({
            message:
              'Supplier ' +
              this.currentSupplier.name +
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
          this.supplierEdit = false;
        });
    },

    handleDelete(id, name) {
      this.$confirm(
        'This will permanently delete Supplier ' + name + '. Continue?',
        'Warning',
        {
          confirmButtonText: 'OK',
          cancelButtonText: 'Cancel',
          type: 'warning',
        }
      )
        .then(() => {
          DeleteSupplier(id)
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
</style>
