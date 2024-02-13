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

      <el-table-column
        align="center"
        label="Sub District"
        prop="sub_district"
      />

      <el-table-column align="center" label="Postal Code" prop="postal_code" />

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
              @click="handleEditPostalCode(scope.row.id)"
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
              @click="handleDelete(scope.row.id, scope.row.postal_code)"
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
      :title="'Create new Postal Code'"
      :visible.sync="dialogFormVisible"
      width="50%"
    >
      <div v-loading="postalCodeCreating" class="form-container">
        <el-form
          ref="postalCodeForm"
          :rules="rules"
          :model="newPostalCode"
          label-position="left"
          label-width="150px"
          style="max-width: 100%"
        >
          <el-form-item :label="$t('Provinces')" prop="provinces">
            <el-select
              v-model="newPostalCode.provinces"
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
              v-model="newPostalCode.cities"
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
              v-model="newPostalCode.district"
              filterable
              placeholder="Select"
              :disabled="isDisabledDistrict"
              style="width: 100%"
              @input="handleChangeDistrict"
            >
              <el-option
                v-for="item in dataDistricts"
                :key="item.id"
                :label="item.label"
                :value="item.id"
              />
            </el-select>
          </el-form-item>
          <el-form-item :label="$t('Sub District')" prop="sub_district">
            <el-select
              v-model="newPostalCode.sub_district"
              filterable
              placeholder="Select"
              :disabled="isDisabledSubDistrict"
              style="width: 100%"
            >
              <el-option
                v-for="item in dataSubDistricts"
                :key="item.id"
                :label="item.label"
                :value="item.id"
              />
            </el-select>
          </el-form-item>
          <el-form-item :label="$t('Postal Code')" prop="postal_code">
            <el-input
              v-model="newPostalCode.postal_code"
              placeholder="type Postal Code here"
            />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="createPostalCode()">
            {{ $t('table.confirm') }}
          </el-button>
        </div>
      </div>
    </el-dialog>

    <el-dialog
      :title="'Edit Postal Code '"
      :visible.sync="dialogEditVisible"
      width="50%"
    >
      <div v-loading="postalCodeEdit" class="form-container">
        <el-form
          ref="postalCodeFormEdit"
          :rules="rules"
          :model="currentPostalCode"
          label-position="left"
          label-width="150px"
          style="max-width: 100%"
        >
          <el-form-item :label="$t('Provinces')" prop="provinces">
            <el-select
              v-model="currentPostalCode.provinces"
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
              v-model="currentPostalCode.cities"
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
              v-model="currentPostalCode.district"
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
          <el-form-item :label="$t('Sub District')" prop="sub_district">
            <el-select
              v-model="currentPostalCode.sub_district"
              filterable
              placeholder="Select"
              :disabled="isDisabledSubDistrict"
              style="width: 100%"
            >
              <el-option
                v-for="item in dataSubDistricts"
                :key="item.id"
                :label="item.label"
                :value="item.id"
              />
            </el-select>
          </el-form-item>
          <el-form-item :label="$t('Postal Code')" prop="postal_code">
            <el-input
              v-model="currentPostalCode.postal_code"
              placeholder="type Postal Code here"
            />
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogEditVisible = false">
            {{ $t('table.cancel') }}
          </el-button>
          <el-button type="primary" @click="updatePostalCode()">
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
  createOrUpdatePostalCode,
  DeletePostalCode,
} from '@/api/customer/master/postal_code';
import { fetchList as listProv } from '@/api/customer/master/provinces';
import { showByProvince } from '@/api/customer/master/cities';
import { showByCity } from '@/api/customer/master/district';
import { showByDistrict } from '@/api/customer/master/sub_district';

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
      postalCodeCreating: false,
      postalCodeEdit: false,
      dialogFormVisible: false,
      dialogEditVisible: false,
      isDisabledCity: true,
      isDisabledDistrict: true,
      isDisabledSubDistrict: true,
      newPostalCode: {},
      currentPostalCode: {},
      dataProv: [],
      dataCities: [],
      dataDistricts: [],
      dataSubDistricts: [],
      defaultProps: { children: 'children', label: 'label' },
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        role: '',
        sort: '-id',
      },
      rules: {
        provinces: [
          {
            required: true,
            message: 'Province Code is required',
            trigger: 'blur',
          },
        ],
        cities: [
          { required: true, message: 'City is required', trigger: 'blur' },
        ],
        district: [
          { required: true, message: 'District is required', trigger: 'blur' },
        ],
        sub_district: [
          {
            required: true,
            message: 'Sub District is required',
            trigger: 'blur',
          },
        ],
        postal_code: [
          {
            required: true,
            message: 'Postal Code is required',
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
    createPostalCode() {
      this.$refs['postalCodeForm'].validate((valid) => {
        console.log(valid);
        if (valid) {
          this.newPostalCode.roles = [this.newPostalCode.role];
          this.postalCodeCreating = true;
          createOrUpdatePostalCode(this.newPostalCode)
            .then((response) => {
              this.$message({
                message:
                  'New province ' +
                  this.newPostalCode.name +
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
              this.postalCodeCreating = false;
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
        role: 'postalCode',
      };
    },
    handleCreate() {
      this.resetNewForm();
      this.dialogFormVisible = true;
      this.$nextTick(() => {
        this.$refs['postalCodeForm'].clearValidate();
      });
    },
    handleEditPostalCode(id) {
      this.currentId = id;
      this.postalCodeEdit = true;
      this.dialogEditVisible = true;
      this.isDisabledCity = false;
      this.isDisabledDistrict = false;
      this.isDisabledSubDistrict = false;
      const foundPostalCode = this.list.find(
        (postalCode) => postalCode.id === id
      );
      showByProvince(foundPostalCode.m_provinces_id).then((response) => {
        response.data.forEach((element, index) => {
          this.dataCities.push({ id: element.id, label: element.name });
        });
      });
      showByCity(foundPostalCode.m_cities_id).then((response) => {
        response.data.forEach((element, index) => {
          this.dataDistricts.push({ id: element.id, label: element.name });
        });
      });
      showByDistrict(foundPostalCode.m_districts_id).then((response) => {
        response.data.forEach((element, index) => {
          this.dataSubDistricts.push({ id: element.id, label: element.name });
        });
      });
      this.currentPostalCode = {
        id: foundPostalCode.id,
        postal_code: foundPostalCode.postal_code,
        provinces: foundPostalCode.m_provinces_id,
        cities: foundPostalCode.m_cities_id,
        district: foundPostalCode.m_districts_id,
        sub_district: foundPostalCode.m_subdistricts_id,
      };
      this.postalCodeEdit = false;
    },
    updatePostalCode() {
      this.postalCodeEdit = true;
      createOrUpdatePostalCode(this.currentPostalCode)
        .then((response) => {
          this.$message({
            message:
              'Postal Code ' +
              this.currentPostalCode.postal_code +
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
          this.postalCodeEdit = false;
        });
    },
    handleDelete(id, postal_code) {
      this.$confirm(
        'This will permanently delete Postal Code ' +
          postal_code +
          '. Continue?',
        'Warning',
        {
          confirmButtonText: 'OK',
          cancelButtonText: 'Cancel',
          type: 'warning',
        }
      )
        .then(() => {
          DeletePostalCode(id)
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
    handleChangeDistrict(value) {
      this.dataSubDistricts = [];
      this.isDisabledSubDistrict = false;
      showByDistrict(value).then((response) => {
        response.data.forEach((element, index) => {
          this.dataSubDistricts.push({ id: element.id, label: element.name });
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
