<template>
  <div class="createPost-container">
    <el-form
      ref="postForm"
      v-loading.fullscreen.lock="loadingForm"
      element-loading-background="rgba(122, 122, 122, 0.8)"
      element-loading-text="Loading..."
      :model="newCustomer"
      :rules="rules"
      class="form-container"
    >
      <sticky :class-name="'sub-navbar ' + newCustomer.status">
        <el-button
          v-loading="loading"
          style="margin-left: 10px"
          type="success"
          @click="saveCustomer()"
        >
          Submit
        </el-button>
        <router-link :to="'/customer-customer/list'">
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
                    prop="name"
                    style="width: 90%"
                    label-width="100px"
                    label="Name "
                    class="postInfo-container-item"
                  >
                    <el-input
                      v-model="newCustomer.name"
                      placeholder="Please input"
                      style="width: 100%"
                    >
                      <template #append>
                        <svg-icon
                          icon-class="people"
                          class-name="card-panel-icon"
                        />
                      </template>
                    </el-input>
                  </el-form-item>
                </el-col>

                <el-col :span="12">
                  <el-form-item
                    prop="phone"
                    label-width="100px"
                    label="Phone "
                    class="postInfo-container-item"
                    style="width: 90%"
                  >
                    <el-input
                      v-model="newCustomer.phone"
                      placeholder="Please input"
                      style="width: 100%"
                    >
                      <template #append>
                        <svg-icon
                          icon-class="mobile-notch"
                          class-name="card-panel-icon"
                        />
                      </template>
                    </el-input>
                  </el-form-item>
                </el-col>
              </el-row>

              <el-row>
                <el-col :span="12">
                  <el-form-item
                    prop="NIK"
                    style="width: 90%"
                    label-width="100px"
                    label="NIK "
                    class="postInfo-container-item"
                  >
                    <el-input
                      v-model="newCustomer.nik"
                      placeholder="Please input"
                      style="width: 100%"
                    >
                      <template #append>
                        <svg-icon
                          icon-class="id-badge"
                          class-name="card-panel-icon"
                        />
                      </template>
                    </el-input>
                  </el-form-item>
                </el-col>

                <el-col :span="12">
                  <el-form-item
                    prop="npwp"
                    label-width="100px"
                    label="NPWP "
                    class="postInfo-container-item"
                    style="width: 90%"
                  >
                    <el-input
                      v-model="newCustomer.npwp"
                      placeholder="Please input"
                      style="width: 100%"
                    >
                      <template #append>
                        <svg-icon
                          icon-class="tax"
                          class-name="card-panel-icon"
                        />
                      </template>
                    </el-input>
                  </el-form-item>
                </el-col>
              </el-row>
              <el-row>
                <el-col :span="12">
                  <el-form-item
                    label-width="100px"
                    label="Province"
                    prop="provinces"
                    style="width: 90%"
                  >
                    <el-select
                      v-model="newCustomer.provinces"
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
                </el-col>

                <el-col :span="12">
                  <el-form-item
                    label-width="100px"
                    label="City"
                    prop="cities"
                    style="width: 90%"
                  >
                    <el-select
                      v-model="newCustomer.cities"
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
                </el-col>
              </el-row>
              <el-row>
                <el-col :span="12">
                  <el-form-item
                    label="District"
                    prop="district"
                    style="width: 90%"
                    label-width="100px"
                  >
                    <el-select
                      v-model="newCustomer.district"
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
                </el-col>

                <el-col :span="12">
                  <el-form-item
                    label="Sub District"
                    style="width: 90%"
                    label-width="100px"
                    prop="sub_district"
                  >
                    <el-select
                      v-model="newCustomer.sub_district"
                      filterable
                      placeholder="Select"
                      :disabled="isDisabledSubDistrict"
                      style="width: 100%"
                      @input="handleChangeSubDistrict"
                    >
                      <el-option
                        v-for="item in dataSubDistricts"
                        :key="item.id"
                        :label="item.label"
                        :value="item.id"
                      />
                    </el-select>
                  </el-form-item>
                </el-col>
              </el-row>
              <el-row>
                <el-col :span="12">
                  <el-form-item
                    label="Address"
                    prop="address"
                    style="width: 90%"
                    label-width="100px"
                  >
                    <el-input
                      v-model="newCustomer.address"
                      type="textarea"
                      placeholder="Please input"
                      style="width: 100%"
                    />
                  </el-form-item>
                </el-col>

                <el-col :span="12">
                  <el-form-item
                    label="Postal Code"
                    style="width: 90%"
                    label-width="100px"
                    prop="postal_code"
                  >
                    <el-input
                      v-model="newCustomer.postal_code"
                      placeholder="Please input"
                      style="width: 100%"
                      :disabled="true"
                    >
                      <template #append>
                        <svg-icon
                          icon-class="mailbox"
                          class-name="card-panel-icon"
                        />
                      </template>
                    </el-input>
                  </el-form-item>
                </el-col>
              </el-row>
            </div>
          </el-col>
        </el-row>
      </div>
    </el-form>
  </div>
</template>
<script>
import Sticky from '@/components/Sticky';
import { fetchList as listProv } from '@/api/customer/master/provinces';
import { showByProvince } from '@/api/customer/master/cities';
import { showByCity } from '@/api/customer/master/district';
import { showByDistrict } from '@/api/customer/master/sub_district';
import { showBySubDistrict } from '@/api/customer/master/postal_code';
import { createCustomer, fetchShowCustomer } from '@/api/customer/customer';

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
      isDisabledCity: true,
      isDisabledDistrict: true,
      isDisabledSubDistrict: true,
      newCustomer: { postal_code: null },
      dataProv: [],
      dataCities: [],
      dataDistricts: [],
      dataSubDistricts: [],
      postForm: Object.assign({}, defaultForm),
      id_supplier: null,
      rules: {
        name: [
          { required: true, message: 'Name is required', trigger: 'blur' },
        ],
        phone: [
          { required: true, message: 'Phone is required', trigger: 'blur' },
        ],
        npwp: [
          { required: true, message: 'NPWP is required', trigger: 'blur' },
        ],
        address: [
          {
            required: true,
            message: 'Address is required',
            trigger: 'blur',
          },
        ],
      },
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
      this.fetchShowCustomer(id);
    }
  },
  methods: {
    loadOptions() {
      listProv().then((response) => {
        response.data.forEach((element, index) => {
          this.dataProv.push({ id: element.id, label: element.name });
        });
      });
    },
    fetchShowCustomer(id) {
      this.loadingForm = true;
      this.isDisabledCity = false;
      this.isDisabledDistrict = false;
      this.isDisabledSubDistrict = false;
      fetchShowCustomer(id).then((response) => {
        const data = response.data;
        showByProvince(data.m_provinces_id).then((response) => {
          response.data.forEach((element, index) => {
            this.dataCities.push({ id: element.id, label: element.name });
          });
        });
        showByCity(data.m_cities_id).then((response) => {
          response.data.forEach((element, index) => {
            this.dataDistricts.push({ id: element.id, label: element.name });
          });
        });
        showByDistrict(data.m_districts_id).then((response) => {
          response.data.forEach((element, index) => {
            this.dataSubDistricts.push({ id: element.id, label: element.name });
          });
        });
        this.newCustomer.name = response.data.name;
        this.newCustomer.nik = response.data.nik;
        this.newCustomer.npwp = response.data.npwp;
        this.newCustomer.phone = response.data.phone;
        this.newCustomer.provinces = response.data.m_provinces_id;
        this.newCustomer.cities = response.data.m_cities_id;
        this.newCustomer.district = response.data.m_districts_id;
        this.newCustomer.sub_district = response.data.m_subdistricts_id;
        this.newCustomer.address = response.data.address;
        this.newCustomer.postal_code = response.data.postal_code;
        this.loadingForm = false;
      });
    },
    saveCustomer() {
      this.$refs['postForm'].validate((valid) => {
        if (valid) {
          this.newCustomer.roles = [this.newCustomer.role];
          this.loading = true;
          createCustomer(this.newCustomer)
            .then((response) => {
              console.log(response.is_empty);
              if (response.is_empty === 0) {
                this.$message({
                  message: response.message,
                  type: 'error',
                  duration: 5 * 1000,
                });
              } else {
                this.$message({
                  message: response.message,
                  type: 'success',
                  duration: 5 * 1000,
                });
                this.$router.push({ path: '/customer-customer/list' });
                this.dialogFormVisible = false;
                this.handleFilter();
              }
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
      console.log(value);
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
    handleChangeSubDistrict(value) {
      this.isDisabledSubDistrict = false;
      showBySubDistrict(value).then((response) => {
        this.newCustomer.postal_code = response.data.postal_code;
        this.newCustomer.postal_code_id = response.data.id;
      });
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
