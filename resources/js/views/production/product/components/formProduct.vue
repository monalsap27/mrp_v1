<template>
  <div class="createPost-container">
    <el-form
      ref="postForm"
      v-loading.fullscreen.lock="loadingEdit"
      element-loading-background="rgba(122, 122, 122, 0.8)"
      element-loading-text="Loading..."
      :model="newProduct"
      :rules="rules"
      class="form-container"
    >
      <sticky :class-name="'sub-navbar ' + newProduct.status">
        <el-button
          v-loading="loading"
          style="margin-left: 10px"
          type="success"
          @click="saveProduct()"
        >
          Submit
        </el-button>
        <router-link :to="'/production-product/list'">
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
        <el-row>
          <el-col :span="24">
            <el-row>
              <el-col :span="8">
                <el-form-item style="margin-right: 40px" prop="name">
                  <MDinput
                    v-model="newProduct.name"
                    :maxlength="100"
                    name="name"
                    required
                  >
                    Name
                  </MDinput>
                </el-form-item>
              </el-col>

              <el-col :span="8">
                <el-form-item style="margin-left: 40px" prop="code">
                  <MDinput
                    v-model="newProduct.code"
                    :maxlength="100"
                    name="code"
                    required
                  >
                    Code
                  </MDinput>
                </el-form-item>
              </el-col>

              <el-col :span="8">
                <el-form-item style="margin-left: 40px" prop="unit_cost">
                  <MDinput
                    v-model="newProduct.unit_cost"
                    :maxlength="100"
                    name="unit_cost"
                    required
                  >
                    Unit Cost
                  </MDinput>
                </el-form-item>
              </el-col>
            </el-row>
            <el-row>
              <el-col :span="6">
                <el-form-item
                  style="margin-bottom: 40px; margin-right: 40px"
                  prop="length"
                >
                  <MDinput
                    v-model="newProduct.length"
                    :maxlength="100"
                    name="length"
                    required
                  >
                    Length
                  </MDinput>
                </el-form-item>
              </el-col>
              <el-col :span="6">
                <el-form-item
                  style="margin-bottom: 40px; margin-right: 40px"
                  prop="width"
                >
                  <MDinput
                    v-model="newProduct.width"
                    :maxlength="100"
                    name="width"
                    required
                  >
                    Width
                  </MDinput>
                </el-form-item>
              </el-col>
              <el-col :span="6">
                <el-form-item
                  style="margin-bottom: 40px; margin-right: 40px"
                  prop="height"
                >
                  <MDinput
                    v-model="newProduct.height"
                    :maxlength="100"
                    name="height"
                    required
                  >
                    Height
                  </MDinput>
                </el-form-item>
              </el-col>
              <el-col :span="6">
                <el-form-item
                  style="margin-bottom: 40px; margin-right: 40px"
                  prop="weight"
                >
                  <MDinput
                    v-model="newProduct.weight"
                    :maxlength="100"
                    name="weight"
                    required
                  >
                    Weight
                  </MDinput>
                </el-form-item>
              </el-col>
            </el-row>
            <el-row>
              <el-col :span="8">
                <el-form-item
                  label-width="100px"
                  label="Category:"
                  class="postInfo-container-item"
                >
                  <el-select
                    v-model="newProduct.categories"
                    class="filter-item w-200"
                    placeholder="Please select"
                    style="width: 80%"
                  >
                    <el-option
                      v-for="item_categories in categoriesOptions"
                      :key="item_categories.id"
                      :label="item_categories.name"
                      :value="item_categories.id"
                    />
                  </el-select>
                </el-form-item>
              </el-col>
              <el-col :span="8">
                <el-form-item
                  label-width="60px"
                  label="Unit:"
                  class="postInfo-container-item"
                  style="width: 80%"
                >
                  <el-select
                    v-model="newProduct.unit"
                    class="filter-item"
                    placeholder="Please select"
                    style="width: 80%"
                  >
                    <el-option
                      v-for="item_unit in unitOptions"
                      :key="item_unit.id"
                      :label="item_unit.name"
                      :value="item_unit.id"
                    />
                  </el-select>
                </el-form-item>
              </el-col>
              <el-col :span="8">
                <el-form-item
                  label-width="100px"
                  label=" Supplier :"
                  class="postInfo-container-item"
                >
                  <el-select
                    v-model="newProduct.supplier"
                    class="filter-item w-200"
                    placeholder="Please select"
                    style="width: 80%"
                  >
                    <el-option
                      v-for="item_supplier in supplierOptions"
                      :key="item_supplier.id"
                      :label="item_supplier.name"
                      :value="item_supplier.id"
                    />
                  </el-select>
                </el-form-item>
              </el-col>
            </el-row>
            <el-row>
              <el-col :span="12">
                <div style="display: inline-block">
                  <label class="radio-label">How do you source this product ?
                  </label>
                  <el-radio-group
                    v-model="newProduct.type"
                    :disabled="disabledWSGroup"
                  >
                    <el-radio :label="1" border @change="onChange($event)">
                      I make this product
                    </el-radio>
                    <el-radio :label="2" border @change="onChange($event)">
                      I buy this product
                    </el-radio>
                  </el-radio-group>
                </div>
              </el-col>
            </el-row>
          </el-col>
        </el-row>
        <br>
        <el-card v-show="dialogDetailVisible" class="box-card">
          <el-col :span="10">
            <el-form-item
              label-width="200px"
              label="Workstation Group : "
              class="postInfo-container-item"
            >
              <el-select
                v-model="newProduct.workStation"
                class="filter-item"
                placeholder="Please select"
                style="width: 80%"
                :disabled="disabledWSGroup"
                @change="onShowWorkstation($event)"
              >
                <el-option
                  v-for="item_workStation in workStationOptions"
                  :key="item_workStation.id"
                  :label="item_workStation.name"
                  :value="item_workStation.id"
                />
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="14">
            <el-button
              style="float: right"
              type="success"
              plain
              @click="addRow"
            >
              Add item
            </el-button>
          </el-col>
          <table />
          <el-table
            v-loading="listLoading"
            :data="newProduct.items"
            border
            fit
            highlight-current-row
            style="width: 100%"
          >
            <el-table-column align="center" label="Name" width="350">
              <template #default="scope">
                <template v-if="scope.row.edit">
                  <el-select
                    v-model="scope.row.workstation_id"
                    value-key="id"
                    placeholder="Please select"
                    style="width: 100%"
                    @change="onGetWorkstation(scope.row, $event)"
                  >
                    <el-option
                      v-for="item in workstationOptions"
                      :key="item.id"
                      :label="item.name"
                      :value="item"
                    />
                  </el-select>
                </template>
                <span v-else style="margin-left: 10px">
                  {{ scope.row.workstation_name }}
                </span>
              </template>
            </el-table-column>
            <el-table-column
              align="center"
              label="Description"
              prop="description"
              width="400"
            />
            <el-table-column align="center" prop="total_workforce" width="120">
              <template #header>
                <span style="margin-left: 10px">Workforce </span>
                <br>
                <span style="margin-left: 10px">Total ({{ newProduct.total_workforce }})
                </span>
              </template>
            </el-table-column>
            <el-table-column align="center" width="150">
              <template #header>
                <span style="margin-left: 10px">Timing </span>
                <br>
                <span style="margin-left: 10px">Total ({{ newProduct.total_timing }})
                </span>
              </template>
              <template #default="scope">
                <template v-if="scope.row.edit">
                  <el-input-number
                    v-model="scope.row.timing"
                    class="edit-input"
                    size="small"
                    @change="handleChangeTiming"
                  />
                </template>
                <span v-else style="margin-left: 10px">
                  {{ scope.row.timing }}
                </span>
              </template>
            </el-table-column>
            <el-table-column align="center" label="Material" width="350">
              <template #default="scope">
                <template v-if="scope.row.edit">
                  <el-select
                    v-model="scope.row.material_id"
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
                      <span style="float: left">{{ item_product.code }}</span>
                      <span
                        style="float: right; color: #8492a6; font-size: 13px"
                      >{{ item_product.name }}</span>
                    </el-option>
                  </el-select>
                </template>
                <span v-else style="margin-left: 10px">
                  {{ scope.row.material }}
                </span>
              </template>
            </el-table-column>

            <el-table-column align="center" label="QTY" width="150">
              <template #default="scope">
                <template v-if="scope.row.edit">
                  <el-input-number
                    v-model="scope.row.qty"
                    class="edit-input"
                    size="small"
                  />
                </template>
                <span v-else style="margin-left: 10px">
                  {{ scope.row.qty }}
                </span>
              </template>
            </el-table-column>

            <el-table-column fixed="right" label="Action" width="180">
              <template #default="scope">
                <el-button
                  v-if="!scope.row.edit"
                  size="small"
                  @click="handleEdit(scope.$index, scope.row)"
                >Edit</el-button>
                <el-button
                  type="danger"
                  size="small"
                  @click.prevent="handleDelete(scope.$index)"
                >Delete</el-button>
              </template>
            </el-table-column>
          </el-table>
        </el-card>
      </div>
    </el-form>
  </div>
</template>
<script>
import MDinput from '@/components/MDinput';
import Sticky from '@/components/Sticky';
import { fetchList as listCategories } from '@/api/production/master/categories';
import { fetchList as listUnit } from '@/api/production/master/unit';
import { fetchList as listSupplier } from '@/api/production/master/supplier';
import {
  createProduct,
  comboData as listProduct,
  ShowProduct,
  ShowProductDetail,
} from '@/api/production/product';
import { fetchList as listWorkStasionGroup } from '@/api/production/workstation_group';
import {
  fetchList as comboWorkstation,
  fetchWorkstationByGroup,
} from '@/api/production/workstation';

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
    MDinput,
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
      newProduct: {
        total_workforce: 0,
        total_timing: 0,
        items: [],
      },
      postForm: Object.assign({}, defaultForm),
      loading: false,
      listLoading: false,
      loadingEdit: true,
      dialogDetailVisible: false,
      userListOptions: [],
      categoriesOptions: [],
      unitOptions: [],
      supplierOptions: [],
      workStationOptions: [],
      productOptions: [],
      workstationOptions: [],
      disabledWSGroup: false,
      unique: null,
      rules: {
        name: [
          { required: true, message: 'Name is required', trigger: 'blur' },
        ],
      },
      tempRoute: {},
    };
  },
  computed: {
    contentShortLength() {
      return this.postForm.content_short.length;
    },
  },
  created() {
    this.loadOptions();
    this.tempRoute = Object.assign({}, this.$route);
    if (this.isEdit) {
      this.disabledWSGroup = true;
      this.loadingEdit = true;
      const id = this.$route.params && this.$route.params.id;
      this.fetchData(id);
    } else {
      this.loadingEdit = false;
    }
  },
  methods: {
    loadOptions() {
      listCategories().then((response) => {
        this.categoriesOptions = response.data;
      });
      listUnit().then((response) => {
        this.unitOptions = response.data;
      });
      listSupplier().then((response) => {
        this.supplierOptions = response.data;
      });
      listWorkStasionGroup().then((response) => {
        this.workStationOptions = response.data;
      });
      listProduct().then((response) => {
        this.productOptions = response.data;
      });
      comboWorkstation().then((response) => {
        this.workstationOptions = response.data;
      });
    },
    onChange(event) {
      if (Number(event) === 1) {
        this.dialogDetailVisible = true;
      } else {
        this.dialogDetailVisible = false;
      }
    },
    onGetWorkstation(row, data) {
      if (this.unique != null) {
        if (
          this.unique.find((x) => x.workstation_id === data.id) === undefined
        ) {
          this.newProduct.total_workforce =
            parseInt(this.newProduct.total_workforce) +
            parseInt(data.total_workforce);
          this.newProduct.total_timing =
            parseInt(this.newProduct.total_timing) + parseInt(data.timing);
        }
        row.timing = data.timing;
        row.description = data.description;
        row.total_workforce = data.total_workforce;
      }
    },
    handleEdit(index, row) {
      row.edit = true;
      row.workstation_id = '';
    },
    handleDelete(index) {
      this.newProduct.items.splice(index, 1);
    },
    addRow() {
      this.newProduct.items.push({
        workstation_name: '',
        description: '',
        material: '',
        timing: '',
        total_workforce: '',
        qty: '',
        edit: true,
        id: '',
        material_id: '',
        workstation_id: '',
      });
      this.unique = this.newProduct.items;
    },

    async onShowWorkstation(event) {
      this.newProduct.items = [];
      this.listLoading = true;
      const { data } = await fetchWorkstationByGroup(event);
      data.forEach((element, index) => {
        this.newProduct.items.push({
          workstation_name: element.workstation_name,
          description: element.description,
          material: element.material,
          timing: element.timing,
          total_workforce: element.total_workforce,
          qty: element.qty,
          edit: false,
          id: element.id,
          material_id: element.material_id,
          workstation_id: element.workstation_id,
        });
      });
      this.unique = data.filter(
        (obj, index) =>
          data.findIndex(
            (item) => item.workstation_id === obj.workstation_id
          ) === index
      );
      this.unique.forEach((dataUniq, idx) => {
        this.newProduct.total_workforce += parseInt(dataUniq.total_workforce);
        this.newProduct.total_timing += parseInt(dataUniq.timing);
      });
      this.listLoading = false;
    },
    fetchData(id) {
      this.loadingEdit = true;
      ShowProduct(id)
        .then((response) => {
          this.newProduct = {
            id: response.data.id,
            name: response.data.product_name,
            code: response.data.code,
            description: response.data.description,
            categories: response.data.m_category_id,
            unit: response.data.m_unit_id,
            length: response.data.length_product,
            height: response.data.height,
            weight: response.data.weight,
            width: response.data.width,
            unit_cost: response.data.unit_cost,
            type: response.data.type,
            supplier: response.data.m_supplier_id,
            total_workforce: response.data.total_workforce,
            total_timing: response.data.total_timing,
            items: [],
          };
          this.onChange(response.data.type);
          this.loadingEdit = false;
        })
        .catch((err) => {
          console.log(err);
        });
      ShowProductDetail(id)
        .then((response) => {
          response.data.forEach((element, index) => {
            this.newProduct.items.push({
              workstation_name: element.workstation_name,
              description: element.description,
              material: element.material,
              timing: element.timing,
              total_workforce: element.total_workforce,
              qty: element.qty,
              edit: false,
              id: element.id,
              material_id: element.material_id,
              workstation_id: element.workstation_id,
            });
          });
          this.unique = response.data.filter(
            (obj, index) =>
              response.data.findIndex(
                (item) => item.workstation_id === obj.workstation_id
              ) === index
          );

          this.loadingEdit = false;
        })
        .catch((err) => {
          console.log(err);
        });
    },

    resetNewForm() {
      this.newProduct = {
        id: '',
        name: '',
        length: '',
        height: '',
        width: '',
        weight: '',
        unit_cost: '',
        type: '',
        description: '',
        role: 'product',
        items: [],
      };
    },
    handleChangeTiming(oldValue, currentValue) {
      this.newProduct.total_timing = parseInt(
        this.newProduct.total_timing +
          (parseInt(oldValue) - parseInt(currentValue))
      );
    },
    saveProduct() {
      this.$refs['postForm'].validate((valid) => {
        if (valid) {
          this.newProduct.roles = [this.newProduct.role];
          this.loading = true;
          createProduct(this.newProduct)
            .then((response) => {
              this.$message({
                message: this.newProduct.name + response.message,
                type: 'success',
                duration: 5 * 1000,
              });
              this.resetNewForm();
              this.$router.push({
                path: '/production-product/list',
              });
              this.dialogFormVisible = false;
              this.handleFilter();
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
