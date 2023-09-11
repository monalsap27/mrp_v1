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

      <el-table-column align="center" label="Name">
        <template slot-scope="scope">
          <span>{{ scope.row.name }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Code">
        <template slot-scope="scope">
          <span>{{ scope.row.code }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Description">
        <template slot-scope="scope">
          <span>{{ scope.row.description }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="Actions" width="200">
        <template slot-scope="scope">
          <el-button
            type="warning"
            size="small"
            icon="el-icon-edit"
            @click="handleEditMaterial(scope.row.id)"
          >
            Edit
          </el-button>
          <el-button
            type="danger"
            size="small"
            icon="el-icon-delete"
            @click="handleDelete(scope.row.id, scope.row.name)"
          >
            Delete
          </el-button>
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
      :title="'Create new material'"
      :visible.sync="dialogFormVisible"
      width="30%"
    >
      <div v-loading="materialCreating" class="form-container">
        <el-form
          ref="materialForm"
          :rules="rules"
          :model="newMaterial"
          label-position="left"
          label-width="150px"
          style="max-width: 100%"
        >
          <el-form-item :label="$t('Name')" prop="name">
            <el-input
              v-model="newMaterial.name"
              placeholder="type name here"
            />
          </el-form-item>
          <el-form-item :label="$t('Code')" prop="code">
            <el-input
              v-model="newMaterial.code"
              placeholder="type code here"
            />
          </el-form-item>
          <el-form-item :label="$t('Description')" prop="description">
            <el-input
              v-model="newMaterial.description"
              placeholder="type description here"
            />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="createMaterial()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>

    <el-dialog
      :title="'Edit Material '"
      :visible.sync="dialogEditVisible"
      width="30%"
    >
      <div v-loading="materialEdit" class="form-container">
        <el-form
          ref="materialFormEdit"
          :rules="rules"
          :model="currentMaterial"
          label-position="left"
          label-width="150px"
          style="max-width: 100%"
        >
          <el-form-item :label="$t('Name')" prop="name">
            <el-input
              v-model="currentMaterial.name"
              placeholder="type name here"
            />
          </el-form-item>
          <el-form-item :label="$t('Code')" prop="code">
            <el-input
              v-model="currentMaterial.code"
              placeholder="type code here"
            />
          </el-form-item>
          <el-form-item :label="$t('Description')" prop="description">
            <el-input
              v-model="currentMaterial.description"
              placeholder="type description here"
            />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogEditVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="updateMaterial()">
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
  createOrUpdateMaterial,
  DeleteMaterial,
} from '@/api/product/master/material';

export default {
  name: 'MaterialList',
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
  },
  data() {
    return {
      total: 0,
      list: null,
      listLoading: true,
      materialEdit: false,
      materialCreating: false,
      dialogFormVisible: false,
      dialogEditVisible: false,
      newMaterial: {},
      currentMaterial: {},
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
    createMaterial() {
      this.$refs['materialForm'].validate((valid) => {
        if (valid) {
          this.newMaterial.roles = [this.newMaterial.role];
          this.materialCreating = true;
          createOrUpdateMaterial(this.newMaterial)
            .then((response) => {
              this.$message({
                message:
                  'New material ' +
                  this.newMaterial.name +
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
              this.materialCreating = false;
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
      this.newMaterial = {
        id: '',
        name: '',
        description: '',
        role: 'material',
      };
    },
    handleCreate() {
      this.resetNewForm();
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['materialForm'].clearValidate();
      });
    },
    async handleEditMaterial(id) {
      this.currentId = id;
      this.materialEdit = true;
      this.dialogEditVisible = true;
      const foundMaterial = this.list.find(
        (material) => material.id === id
      );
      this.currentMaterial = {
        id: foundMaterial.id,
        name: foundMaterial.name,
        code: foundMaterial.code,
        description: foundMaterial.description,
      };
      this.materialEdit = false;
    },
    updateMaterial() {
      this.materialEdit = true;
      createOrUpdateMaterial(this.currentMaterial)
        .then((response) => {
          this.$message({
            message:
              'Material ' +
              this.currentMaterial.name +
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
          this.materialEdit = false;
        });
    },

    handleDelete(id, name) {
      this.$confirm(
        'This will permanently delete user ' + name + '. Continue?',
        'Warning',
        {
          confirmButtonText: 'OK',
          cancelButtonText: 'Cancel',
          type: 'warning',
        }
      )
        .then(() => {
          DeleteMaterial(id)
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
