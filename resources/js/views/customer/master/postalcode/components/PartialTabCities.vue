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
      <el-table-column align="center" label="Province" prop="province" />

      <el-table-column align="center" label="City" prop="name" />

      <el-table-column align="center" label="Code" prop="code" />

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
              @click="handleEditCities(scope.row.id)"
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
      :title="'Create new city'"
      :visible.sync="dialogFormVisible"
      width="30%"
    >
      <div v-loading="citiesCreating" class="form-container">
        <el-form
          ref="citiesForm"
          :rules="rules"
          :model="newCities"
          label-position="left"
          label-width="150px"
          style="max-width: 100%"
        >
          <el-form-item :label="$t('Provinces')" prop="provinces">
            <el-select
              v-model="newCities.provinces"
              filterable
              placeholder="Select"
            >
              <el-option
                v-for="item in dataProv"
                :key="item.id"
                :label="item.label"
                :value="item.id"
              />
            </el-select>
          </el-form-item>
          <el-form-item :label="$t('Name')" prop="name">
            <el-input v-model="newCities.name" placeholder="type name here" />
          </el-form-item>
          <el-form-item :label="$t('Code')" prop="code">
            <el-input v-model="newCities.code" placeholder="type code here" />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="createCities()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>

    <el-dialog
      :title="'Edit City '"
      :visible.sync="dialogEditVisible"
      width="30%"
    >
      <div v-loading="citiesEdit" class="form-container">
        <el-form
          ref="citiesFormEdit"
          :rules="rules"
          :model="currentCities"
          label-position="left"
          label-width="150px"
          style="max-width: 100%"
        >
          <el-form-item :label="$t('Provinces')" prop="provinces">
            <el-select
              v-model="currentCities.provinces"
              filterable
              placeholder="Select"
            >
              <el-option
                v-for="item in dataProv"
                :key="item.id"
                :label="item.label"
                :value="item.id"
              />
            </el-select>
          </el-form-item>
          <el-form-item :label="$t('Name')" prop="name">
            <el-input
              v-model="currentCities.name"
              placeholder="type name here"
            />
          </el-form-item>
          <el-form-item :label="$t('Code')" prop="code">
            <el-input
              v-model="currentCities.code"
              placeholder="type code here"
            />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogEditVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="updateCities()">
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
  createOrUpdateCities,
  DeleteCities,
} from '@/api/customer/master/cities';
import { fetchList as listProv } from '@/api/customer/master/provinces';

export default {
  name: 'PostalCodeList',
  components: { Pagination },
  filters: {},
  data() {
    return {
      total: 0,
      list: null,
      listLoading: true,
      citiesEdit: false,
      citiesCreating: false,
      dialogFormVisible: false,
      dialogEditVisible: false,
      newCities: {},
      currentCities: {},
      dataProv: [],
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
        provinces: [
          { required: true, message: 'Province is required', trigger: 'blur' },
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
      listProv().then((response) => {
        response.data.forEach((element, index) => {
          this.dataProv.push({ id: element.id, label: element.name });
        });
      });
    },
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
    createCities() {
      this.$refs['citiesForm'].validate((valid) => {
        console.log(valid);
        if (valid) {
          this.newCities.roles = [this.newCities.role];
          this.citiesCreating = true;
          createOrUpdateCities(this.newCities)
            .then((response) => {
              this.$message({
                message:
                  'New province ' +
                  this.newCities.name +
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
              this.citiesCreating = false;
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
      this.newCities = {
        id: '',
        name: '',
        description: '',
        role: 'cities',
      };
    },
    handleCreate() {
      this.resetNewForm();
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['citiesForm'].clearValidate();
      });
    },
    async handleEditCities(id) {
      this.currentId = id;
      this.citiesEdit = true;
      this.dialogEditVisible = true;
      const foundCities = this.list.find((cities) => cities.id === id);
      this.currentCities = {
        id: foundCities.id,
        name: foundCities.name,
        code: foundCities.code,
        provinces: foundCities.m_provinces_id,
      };
      this.citiesEdit = false;
    },
    updateCities() {
      this.citiesEdit = true;
      createOrUpdateCities(this.currentCities)
        .then((response) => {
          this.$message({
            message:
              'Cities ' +
              this.currentCities.name +
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
          this.citiesEdit = false;
        });
    },

    handleDelete(id, name) {
      this.$confirm(
        'This will permanently delete City ' + name + '. Continue?',
        'Warning',
        {
          confirmButtonText: 'OK',
          cancelButtonText: 'Cancel',
          type: 'warning',
        }
      )
        .then(() => {
          DeleteCities(id)
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
