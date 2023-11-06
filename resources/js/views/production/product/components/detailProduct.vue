<template>
  <div class="createPost-container">
    <el-form ref="postForm" class="form-container">
      <sticky :class-name="'sub-navbar '">
        <el-button
          v-loading="loading"
          style="margin-left: 10px"
          type="success"
          @click="saveProduct()"
        >
          Approve
        </el-button>
        <el-button
          v-loading="loading"
          style="margin-left: 10px"
          type="danger"
          text
          @click="handleReject"
        >
          Reject
        </el-button>
        <router-link :to="'/production-product/list'">
          <el-button class="filter-item" style="margin-left: 10px" type="info">
            Close
          </el-button>
        </router-link>
        <div class="createPost-container">
          <el-tabs type="border-card" class="demo-tabs">
            <el-tab-pane>
              <template #label>
                <span class="custom-tabs-label">
                  <el-tooltip content="I make this product" placement="top">
                    <span>Product</span>
                  </el-tooltip>
                </span>
              </template>
              <el-card class="box-card">
                <el-descriptions
                  class="margin-top"
                  title="Product"
                  :column="4"
                  border
                >
                  <template slot="extra" />
                  <el-descriptions-item>
                    <template slot="label"><i class="el-icon-goods" /> Name
                    </template>
                    <span style="width: 80%">{{ listProduct.product_name }}
                    </span>
                  </el-descriptions-item>
                  <el-descriptions-item>
                    <template slot="label">
                      <svg-icon icon-class="square-code-white" />
                      Code
                    </template>
                    {{ listProduct.code }}
                  </el-descriptions-item>
                  <el-descriptions-item>
                    <template slot="label">
                      <svg-icon icon-class="apps-add" />
                      Category
                    </template>
                    {{ listProduct.category_name }}
                  </el-descriptions-item>
                  <el-descriptions-item>
                    <template slot="label">
                      <svg-icon icon-class="gauge-circle-bolt" />
                      Unit
                    </template>
                    {{ listProduct.unit_name }}
                  </el-descriptions-item>
                  <el-descriptions-item>
                    <template slot="label">
                      <svg-icon icon-class="box" />
                      Type
                    </template>
                    {{ listProduct.type }}
                  </el-descriptions-item>
                  <el-descriptions-item>
                    <template slot="label">
                      <svg-icon icon-class="3m" />
                      Supplier
                    </template>
                    {{ listProduct.supplier_name }}
                  </el-descriptions-item>
                  <el-descriptions-item>
                    <template slot="label">
                      <svg-icon icon-class="tools" />
                      Total Workforce
                    </template>
                    {{ listProduct.total_workforce }}
                  </el-descriptions-item>
                  <el-descriptions-item>
                    <template slot="label">
                      <svg-icon icon-class="calendar-clock" />
                      Total Timing
                    </template>
                    {{ listProduct.total_timing }}
                  </el-descriptions-item>
                  <el-descriptions-item>
                    <template slot="label">
                      <svg-icon icon-class="ruler-horizontal" />
                      Length
                    </template>
                    {{ listProduct.length_product }}
                  </el-descriptions-item>
                  <el-descriptions-item>
                    <template slot="label">
                      <svg-icon icon-class="text-height" />
                      Height
                    </template>
                    {{ listProduct.height }}
                  </el-descriptions-item>
                  <el-descriptions-item>
                    <template slot="label">
                      <svg-icon icon-class="scale" />
                      Weight
                    </template>
                    {{ listProduct.weight }}
                  </el-descriptions-item>
                  <el-descriptions-item>
                    <template slot="label">
                      <svg-icon icon-class="text-width" />
                      Width
                    </template>
                    {{ listProduct.width }}
                  </el-descriptions-item>
                </el-descriptions>
              </el-card>
            </el-tab-pane>
            <el-tab-pane>
              <template #label>
                <span class="custom-tabs-label">
                  <el-tooltip content="I make this product" placement="top">
                    <span>Workstation</span>
                  </el-tooltip>
                </span>
              </template>
              <el-card class="box-card">
                <div slot="header" class="clearfix">
                  <span style="float: left">Workstation detail</span>
                </div>
                <el-table
                  :data="listDetailProduct"
                  border
                  fit
                  highlight-current-row
                  style="width: 100%"
                >
                  <el-table-column
                    align="center"
                    label="Name"
                    prop="workstation_name"
                  />

                  <el-table-column
                    align="center"
                    label="Description"
                    prop="description"
                  />

                  <el-table-column
                    align="center"
                    label="Workforce"
                    width="100px"
                    prop="total_workforce"
                  />

                  <el-table-column
                    align="center"
                    label="Timing"
                    width="100"
                    prop="timing"
                  />

                  <el-table-column
                    align="center"
                    label="Material"
                    prop="material"
                  />

                  <el-table-column
                    align="center"
                    label="QTY"
                    width="100px"
                    prop="qty"
                  />
                </el-table>
              </el-card>
            </el-tab-pane>
          </el-tabs>
        </div>
      </sticky>
    </el-form>

    <el-dialog :visible.sync="dialogFormVisible" title="Reject" width="30%">
      <el-form :model="formReject">
        <el-form-item label="Note">
          <el-input
            v-model="formReject.name"
            :rows="2"
            type="textarea"
            placeholder="Please input"
            autocomplete="off"
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="dialogFormVisible = false">Cancel</el-button>
          <el-button type="primary" @click="dialogFormVisible = false">
            Confirm
          </el-button>
        </span>
      </template>
    </el-dialog>
  </div>
</template>
<script>
import Sticky from '@/components/Sticky';
import { ShowProduct, ShowProductDetail } from '@/api/production/product';

const defaultForm = {
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
    isApprove: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      postForm: Object.assign({}, defaultForm),
      formReject: {},
      loading: false,
      listProduct: null,
      listDetailProduct: null,
      dialogFormVisible: false,
      tempRoute: {},
    };
  },
  computed: {
    contentShortLength() {
      return this.postForm.content_short.length;
    },
  },
  created() {
    const id = this.$route.params && this.$route.params.id;
    this.fetchData(id);
    this.tempRoute = Object.assign({}, this.$route);
  },
  methods: {
    fetchData(id) {
      ShowProduct(id)
        .then((response) => {
          this.listProduct = response.data;
        })
        .catch((err) => {
          console.log(err);
        });
      ShowProductDetail(id)
        .then((response) => {
          this.listDetailProduct = response.data;
        })
        .catch((err) => {
          console.log(err);
        });
    },
    handleReject() {
      this.dialogFormVisible = true;
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
