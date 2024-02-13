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

      <el-table-column align="center" label="Tenor" prop="tenor" />

      <el-table-column align="center" label="Note" prop="note" />

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
              @click="handleEditTenor(scope.row.id)"
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
              @click="handleDelete(scope.row.id)"
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
      :title="'Create new tenor'"
      :visible.sync="dialogFormVisible"
      width="40%"
    >
      <div v-loading="tenorCreating" class="form-container">
        <el-form
          ref="tenorForm"
          :rules="rules"
          :model="newTenor"
          label-position="left"
          label-width="150px"
          style="max-width: 100%"
        >
          <el-form-item :label="$t('Tenor')" prop="tenor">
            <el-input-number
              v-model="newTenor.tenor"
              placeholder="type tenor here"
              style="width: 100%"
            />
          </el-form-item>
          <el-form-item :label="$t('Note')" prop="note">
            <el-input v-model="newTenor.note" placeholder="type note here" />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="createTenor()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>

    <el-dialog
      :title="'Edit Tenor '"
      :visible.sync="dialogEditVisible"
      width="40%"
    >
      <div v-loading="priceEdit" class="form-container">
        <el-form
          ref="priceForm"
          :rules="rules"
          :model="currentTenor"
          label-position="left"
          label-width="150px"
          style="max-width: 100%"
        >
          <el-form-item :label="$t('Tenor')" prop="tenor">
            <el-input-number
              v-model="currentTenor.tenor"
              placeholder="type tenor here"
              style="width: 100%"
            />
          </el-form-item>
          <el-form-item :label="$t('Note')" prop="note">
            <el-input
              v-model="currentTenor.note"
              placeholder="type note here"
            />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogEditVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="updateTenor()">
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
  createOrUpdateTenor,
  DeleteTenor,
} from '@/api/sales/master/tenor';
import { comboData as listProduct } from '@/api/production/product';

export default {
  name: 'TenorList',
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
      tenorEdit: false,
      tenorCreating: false,
      dialogFormVisible: false,
      dialogEditVisible: false,
      newTenor: {},
      currentTenor: {},
      productOptions: {},
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        role: '',
        sort: '-id',
      },
      rules: {
        tenor: [
          { required: true, message: 'Tenor is required', trigger: 'blur' },
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
    createTenor() {
      this.$refs['tenorForm'].validate((valid) => {
        if (valid) {
          this.newTenor.roles = [this.newTenor.role];
          this.tenorCreating = true;
          createOrUpdateTenor(this.newTenor)
            .then((response) => {
              this.$message({
                message:
                  'New Tenor ' +
                  this.newTenor.name +
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
              this.tenorCreating = false;
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
      this.newTenor = {
        id: '',
        tenor: '',
        note: '',
        role: 'tenor',
      };
    },
    handleCreate() {
      this.resetNewForm();
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['tenorForm'].clearValidate();
      });
    },
    async handleEditTenor(id) {
      this.currentId = id;
      this.tenorEdit = true;
      this.dialogEditVisible = true;
      const foundTenor = this.list.find((tenor) => tenor.id === id);
      console.log(foundTenor);
      this.currentTenor = {
        id: foundTenor.id,
        note: foundTenor.note,
        tenor: foundTenor.tenor,
        status: foundTenor.status,
      };
      this.priceEdit = false;
    },
    updateTenor() {
      this.priceEdit = true;
      createOrUpdateTenor(this.currentTenor)
        .then((response) => {
          this.$message({
            message: 'Tenor ' + ' has been updated successfully.',
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

    handleDelete(id) {
      this.$confirm('This will permanently delete, Continue?', 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      })
        .then(() => {
          DeleteTenor(id)
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
