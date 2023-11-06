<template>
  <div class="createPost-container">
    <el-form
      ref="postForm"
      v-loading="loadingForm"
      :model="newWorkstation"
      :rules="rules"
      class="form-container"
    >
      <sticky :class-name="'sub-navbar danger'">
        <el-button
          v-loading="loading"
          style="margin-left: 10px"
          type="success"
          @click="saveWorkstation()"
        >
          Submit
        </el-button>
        <router-link :to="'/production-workstation/workstation'">
          <el-button class="filter-item" type="danger"> Close </el-button>
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
                    style="width: 80%"
                    label-width="100px"
                    label="Name :"
                    class="postInfo-container-item"
                  >
                    <el-input
                      v-model="newWorkstation.name"
                      placeholder="Please input"
                      style="width: 100%"
                    >
                      <template #append>Name </template>
                    </el-input>
                  </el-form-item>
                </el-col>

                <el-col :span="12">
                  <el-form-item
                    prop="code"
                    label-width="125px"
                    label="Code :"
                    class="postInfo-container-item"
                    style="width: 80%"
                  >
                    <el-input
                      v-model="newWorkstation.code"
                      placeholder="Please input"
                      style="width: 100%"
                    >
                      <template #append>Code </template>
                    </el-input>
                  </el-form-item>
                </el-col>
              </el-row>
              <el-row>
                <el-col :span="12">
                  <el-form-item
                    prop="timing"
                    style="width: 80%"
                    label-width="100px"
                    label="Timing :"
                    class="postInfo-container-item"
                  >
                    <el-time-select
                      v-model="newWorkstation.timing"
                      style="width: 100%"
                      :picker-options="{
                        start: '00:00',
                        step: '00:15',
                        end: '23:59',
                      }"
                      placeholder="Select time"
                    >
                      <template #append>Description</template>
                    </el-time-select>
                  </el-form-item>
                </el-col>

                <el-col :span="12">
                  <el-form-item
                    prop="workforce"
                    label-width="125px"
                    label="Workforce:"
                    class="postInfo-container-item"
                    style="width: 80%"
                  >
                    <el-select
                      v-model="newWorkstation.workforce"
                      placeholder="Select Item"
                      style="width: 100%"
                      multiple
                      collapse-tags
                      collapse-tags-tooltip
                      :max-collapse-tags="3"
                    >
                      <el-option
                        v-for="item in userListOptions"
                        :key="item.name"
                        style="width: 100%"
                        :label="item.name"
                        :value="item.id"
                      >
                        <span style="float: left">{{ item.name }}</span>
                        <span
                          style="
                            float: right;
                            color: var(--el-text-color-secondary);
                            font-size: 13px;
                          "
                        >{{ item.id }}</span>
                      </el-option>
                    </el-select>
                  </el-form-item>
                </el-col>
              </el-row>
              <el-row>
                <el-col :span="12">
                  <el-form-item
                    prop="description"
                    label-width="100px"
                    label="Description :"
                    class="postInfo-container-item"
                    style="width: 80%"
                  >
                    <el-input
                      v-model="newWorkstation.description"
                      placeholder="Please input"
                      style="width: 100%"
                      type="textarea"
                    />
                  </el-form-item>
                </el-col>
              </el-row>
            </div>
          </el-col>
        </el-row>
        <el-card class="box-card">
          <div slot="header" class="clearfix">
            <span>Row Material</span>
            <el-button
              style="float: right"
              type="success"
              plain
              @click="addRow"
            >
              Add item
            </el-button>
          </div>
          <el-col style="margin-bottom: 18px">
            <el-form-item>
              <el-row :gutter="24">
                <el-col :span="16"> <el-form-item label="Product" /></el-col>
                <el-col :span="5"> <el-form-item label="Quantity" /></el-col>
                <el-col :span="3"> <el-form-item label="Action" /></el-col>
              </el-row>
            </el-form-item>
            <div
              v-if="newWorkstation.items.length > 0"
              class="flex flex-col gap-3"
            >
              <div v-for="(item, index) in newWorkstation.items" :key="index">
                <transition name="fade-transform" mode="out-in">
                  <el-form-item>
                    <el-row :gutter="24">
                      <el-col :span="16">
                        <el-select
                          v-model="item.product"
                          class="filter-item w-200"
                          placeholder="Please select product"
                          style="width: 100%"
                        >
                          <el-option
                            v-for="item_product in productOptions"
                            :key="item_product.id"
                            :label="item_product.name"
                            :value="item_product.id"
                          >
                            <span style="float: left">{{
                              item_product.code
                            }}</span>
                            <span
                              style="
                                float: right;
                                color: #8492a6;
                                font-size: 13px;
                              "
                            >{{ item_product.name }}</span>
                          </el-option>
                        </el-select>
                      </el-col>
                      <el-col :span="5">
                        <el-input-number v-model="item.qty" :min="1" />
                      </el-col>
                      <el-col :span="3">
                        <el-button
                          style="height: 40px; padding-top: 7px"
                          plain
                          type="danger"
                          @click="newWorkstation.items.splice(index, 1)"
                        >
                          <i class="el-icon-close" style="font-size: 25px" />
                        </el-button>
                      </el-col>
                    </el-row>
                  </el-form-item>
                </transition>
              </div>
            </div>
            <div v-else>
              <el-col :span="24">
                <el-card shadow="always" style="text-align: center">
                  No Data
                </el-card>
              </el-col>
            </div>
          </el-col>
        </el-card>
      </div>
    </el-form>
  </div>
</template>
<script>
import Sticky from '@/components/Sticky';
import UserResource from '@/api/user';
import {
  createWorkstation,
  fetchWorkstation,
  fetchWorkstationDetail,
} from '@/api/production/workstation';
import { comboData as listProduct } from '@/api/production/product';

const userResource = new UserResource();

const defaultForm = {
  status: 'draft',
  name: '',
  content: '',
  content_short: '',
  display_time: undefined,
  id: undefined,
  platforms: ['a-platform'],
  importance: 0,
};

export default {
  name: 'FormWorkstation',
  components: { Sticky },
  props: {
    isEdit: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      newWorkstation: { items: [] },
      postForm: Object.assign({}, defaultForm),
      loading: false,
      loadingForm: false,
      userListOptions: [],
      workforce: [],
      productOptions: [],
      rules: {
        name: [
          { required: true, message: 'Name is required', trigger: 'blur' },
        ],
        code: [
          { required: true, message: 'Code is required', trigger: 'blur' },
        ],
        timing: [
          { required: true, message: 'Timing is required', trigger: 'blur' },
        ],
        workforce: [
          { required: true, message: 'Workforce is required', trigger: 'blur' },
        ],
      },
    };
  },
  computed: {
    contentShortLength() {
      return this.postForm.content_short.length;
    },
    lang() {
      return this.$store.getters.language;
    },
  },
  created() {
    this.loadOptions();
    this.tempRoute = Object.assign({}, this.$route);
    if (this.isEdit) {
      const id = this.$route.params && this.$route.params.id;
      this.fetchData(id);
    } else {
      this.loadingForm = false;
    }
  },
  methods: {
    loadOptions() {
      userResource.list().then((response) => {
        this.userListOptions = response.data;
      });
      listProduct().then((response) => {
        this.productOptions = response.data;
      });
    },
    fetchData(id) {
      this.loadingForm = true;
      fetchWorkstation(id)
        .then((response) => {
          this.newWorkstation = {
            id: response.data.id,
            name: response.data.name,
            code: response.data.code,
            description: response.data.description,
            timing: response.data.timing,
            workforce: response.data.workforce,
            items: [],
          };
        })
        .catch((err) => {
          console.log(err);
        });
      fetchWorkstationDetail(id)
        .then((response) => {
          response.data.forEach((element, index) => {
            this.newWorkstation.items.push({
              product: element.product_id,
              qty: element.qty,
              id: element.id,
            });
          });
        })
        .catch((err) => {
          console.log(err);
        });
      this.loadingForm = false;
    },
    saveWorkstation() {
      this.$refs['postForm'].validate((valid) => {
        if (valid) {
          this.newWorkstation.roles = [this.newWorkstation.role];
          this.loading = true;
          createWorkstation(this.newWorkstation)
            .then((response) => {
              if (response.error === 0) {
                this.$message({
                  message:
                    'New Workstation ' +
                    this.newWorkstation.name +
                    ' has been created successfully.',
                  type: 'success',
                  duration: 5 * 1000,
                });
                this.resetNewForm();
                this.$router.push({
                  path: '/production-workstation/workstation',
                });
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
    addRow() {
      this.newWorkstation.items.push({
        product: '',
        qty: '',
      });
    },
    resetNewForm() {
      this.newWorkstation = {
        id: '',
        name: '',
        code: '',
        workforce: '',
        timing: '',
        description: '',
        items: [],
        role: 'product',
      };
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
