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

      <el-table-column align="center" label="City" prop="city" />

      <el-table-column align="center" label="District" prop="district" />

      <el-table-column align="center" label="Sub District" prop="name" />

      <el-table-column align="center" label="Code" prop="code" />

      <el-table-column align="center" label="Actions" width="150">
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
              @click="handleEditSubDistrict(scope.row.id)"
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
      :title="'Create new Sub District'"
      :visible.sync="dialogFormVisible"
      width="50%"
    >
      <div v-loading="subDistrictCreating" class="form-container">
        <el-form
          ref="subDistrictForm"
          :rules="rules"
          :model="newSubDistrict"
          label-position="left"
          label-width="150px"
          style="max-width: 100%"
        >
          <el-form-item :label="$t('Provinces')" prop="provinces">
            <el-select
              v-model="newSubDistrict.provinces"
              filterable
              placeholder="Select"
              style="width: 100%"
              @input="handleChangeProv"
            >
              <el-option
                v-for="item in dataProv"
                :key="item.id"
                :label="item.label"
                :value="item.id"
              />
            </el-select>
          </el-form-item>
          <el-form-item :label="$t('Cities')" prop="cities">
            <el-select
              v-model="newSubDistrict.cities"
              filterable
              placeholder="Select"
              :disabled="isDisabledCity"
              style="width: 100%"
              @input="handleChangeCity"
            >
              <el-option
                v-for="item in dataCities"
                :key="item.id"
                :label="item.label"
                :value="item.id"
              />
            </el-select>
          </el-form-item>
          <el-form-item :label="$t('District')" prop="district">
            <el-select
              v-model="newSubDistrict.district"
              filterable
              placeholder="Select"
              :disabled="isDisabledDistrict"
              style="width: 100%"
            >
              <el-option
                v-for="item in dataDistricts"
                :key="item.id"
                :label="item.label"
                :value="item.id"
              />
            </el-select>
          </el-form-item>
          <el-form-item :label="$t('Name')" prop="name">
            <el-input
              v-model="newSubDistrict.name"
              placeholder="type name here"
            />
          </el-form-item>
          <el-form-item :label="$t('Code')" prop="code">
            <el-input
              v-model="newSubDistrict.code"
              placeholder="type code here"
            />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="createSubDistrict()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>

    <el-dialog
      :title="'Edit Sub District'"
      :visible.sync="dialogEditVisible"
      width="50%"
    >
      <div v-loading="subDistrictEdit" class="form-container">
        <el-form
          ref="subDistrictFormEdit"
          :rules="rules"
          :model="currentSubDistrict"
          label-position="left"
          label-width="150px"
          style="max-width: 100%"
        >
          <el-form-item :label="$t('Provinces')" prop="provinces">
            <el-select
              v-model="currentSubDistrict.provinces"
              filterable
              placeholder="Select"
              style="width: 100%"
            >
              <el-option
                v-for="item in dataProv"
                :key="item.id"
                :label="item.label"
                :value="item.id"
              />
            </el-select>
          </el-form-item>
          <el-form-item :label="$t('Cities')" prop="cities">
            <el-select
              v-model="currentSubDistrict.cities"
              filterable
              placeholder="Select"
              :disabled="isDisabledCity"
              style="width: 100%"
            >
              <el-option
                v-for="item in dataCities"
                :key="item.id"
                :label="item.label"
                :value="item.id"
              />
            </el-select>
          </el-form-item>
          <el-form-item :label="$t('District')" prop="district">
            <el-select
              v-model="currentSubDistrict.district"
              filterable
              placeholder="Select"
              :disabled="isDisabledDistrict"
              style="width: 100%"
            >
              <el-option
                v-for="item in dataDistricts"
                :key="item.id"
                :label="item.label"
                :value="item.id"
              />
            </el-select>
          </el-form-item>
          <el-form-item :label="$t('Name')" prop="name">
            <el-input
              v-model="currentSubDistrict.name"
              placeholder="type name here"
            />
          </el-form-item>
          <el-form-item :label="$t('Code')" prop="code">
            <el-input
              v-model="currentSubDistrict.code"
              placeholder="type code here"
            />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogEditVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="updateSubDistrict()">
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
  createOrUpdateSubDistrict,
  DeleteSubDistrict,
} from '@/api/customer/master/sub_district';
import { fetchList as listProv } from '@/api/customer/master/provinces';
import { showByProvince } from '@/api/customer/master/cities';
import { showByCity } from '@/api/customer/master/district';

export default {
  name: 'PostalCodeList',
  components: { Pagination },
  filters: {},
  data() {
    return {
      total: 0,
      list: null,
      listLoading: true,
      districtEdit: false,
      subDistrictCreating: false,
      subDistrictEdit: false,
      dialogFormVisible: false,
      dialogEditVisible: false,
      isDisabledCity: true,
      isDisabledDistrict: true,
      newSubDistrict: {},
      currentSubDistrict: {},
      dataProv: [],
      dataCities: [],
      dataDistricts: [],
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
        district: [
          { required: true, message: 'District is required', trigger: 'blur' },
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
    createSubDistrict() {
      this.$refs['subDistrictForm'].validate((valid) => {
        console.log(valid);
        if (valid) {
          this.newSubDistrict.roles = [this.newSubDistrict.role];
          this.subDistrictCreating = true;
          createOrUpdateSubDistrict(this.newSubDistrict)
            .then((response) => {
              this.$message({
                message:
                  'New province ' +
                  this.newSubDistrict.name +
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
              this.subDistrictCreating = false;
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
      this.newDistrict = {
        id: '',
        name: '',
        description: '',
        role: 'subDistrict',
      };
    },
    handleCreate() {
      this.resetNewForm();
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['subDistrictForm'].clearValidate();
      });
    },
    handleEditSubDistrict(id) {
      this.currentId = id;
      this.subDistrictEdit = true;
      this.dialogEditVisible = true;
      this.isDisabledCity = false;
      this.isDisabledDistrict = false;
      const foundSubDistrict = this.list.find(
        (subDistrict) => subDistrict.id === id
      );
      showByProvince(foundSubDistrict.m_provinces_id).then((response) => {
        response.data.forEach((element, index) => {
          this.dataCities.push({ id: element.id, label: element.name });
        });
      });
      showByCity(foundSubDistrict.m_cities_id).then((response) => {
        response.data.forEach((element, index) => {
          this.dataDistricts.push({ id: element.id, label: element.name });
        });
      });
      this.currentSubDistrict = {
        id: foundSubDistrict.id,
        name: foundSubDistrict.name,
        code: foundSubDistrict.code,
        provinces: foundSubDistrict.m_provinces_id,
        cities: foundSubDistrict.m_cities_id,
        district: foundSubDistrict.m_districts_id,
      };
      this.subDistrictEdit = false;
    },
    updateSubDistrict() {
      this.subDistrictEdit = true;
      createOrUpdateSubDistrict(this.currentSubDistrict)
        .then((response) => {
          this.$message({
            message:
              'Sub District ' +
              this.currentSubDistrict.name +
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
          this.subDistrictEdit = false;
        });
    },
    handleDelete(id, name) {
      this.$confirm(
        'This will permanently delete Sub District ' + name + '. Continue?',
        'Warning',
        {
          confirmButtonText: 'OK',
          cancelButtonText: 'Cancel',
          type: 'warning',
        }
      )
        .then(() => {
          DeleteSubDistrict(id)
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
    handleChangeProv(value) {
      this.dataCities = [];
      this.isDisabledCity = false;
      showByProvince(value).then((response) => {
        response.data.forEach((element, index) => {
          this.dataCities.push({ id: element.id, label: element.name });
        });
      });
    },
    handleChangeCity(value) {
      this.dataDistricts = [];
      this.isDisabledDistrict = false;
      showByCity(value).then((response) => {
        response.data.forEach((element, index) => {
          this.dataDistricts.push({ id: element.id, label: element.name });
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
