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
          @click="saveWorkstationGroup()"
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
            </div>
          </el-col>
        </el-row>
        <dnd-list-custom
          :list1="newWorkstation.list1"
          :list2="newWorkstation.list2"
          list1-title="List Workstation"
          list2-title="Group Workstation pool"
        />
      </div>
    </el-form>
  </div>
</template>
<script>
import Sticky from '@/components/Sticky';
import DndListCustom from '@/components/DndListCustom';
import UserResource from '@/api/user';
import { fetchList } from '@/api/production/workstation';
import { createWorkstationGroup } from '@/api/production/workstation_group';
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
  components: { Sticky, DndListCustom },
  props: {
    isEdit: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      newWorkstation: { list1: [], list2: [] },
      postForm: Object.assign({}, defaultForm),
      loading: false,
      loadingForm: false,
      userListOptions: [],
      workforce: [],
      productOptions: [],
      list2: [],
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
    this.fetchData();
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
      fetchList().then((response) => {
        this.newWorkstation.list1 = response.data;
      });
      this.loadingForm = false;
    },
    saveWorkstationGroup() {
      this.$refs['postForm'].validate((valid) => {
        if (valid) {
          this.newWorkstation.roles = [this.newWorkstation.role];
          this.loading = true;
          createWorkstationGroup(this.newWorkstation)
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
