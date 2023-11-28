<template>
  <div class="createPost-container">
    <el-form ref="postForm">
      <sticky :class-name="'sub-navbar '">
        <span v-if="isApprove">
          <el-button
            v-loading="loading"
            style="margin-left: 10px"
            type="success"
            @click="handleApprove"
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
          <router-link :to="'/production-product/approval'">
            <el-button
              class="filter-item"
              style="margin-left: 10px"
              type="info"
            >
              Close
            </el-button>
          </router-link>
        </span>
        <span v-else>
          <router-link :to="'/production-product/list'">
            <el-button
              class="filter-item"
              style="margin-left: 10px"
              type="info"
            >
              Close
            </el-button>
          </router-link>
        </span>
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
                <div slot="header" class="clearfix">
                  <span style="padding-right: 10px; float: left">Product</span>
                  <span v-if="status == 1">
                    <el-collapse accordion>
                      <el-collapse-item name="1" disabled="true">
                        <template slot="title">
                          <el-tag type="warning" effect="plain">Waiting confirmation</el-tag>
                        </template>
                      </el-collapse-item>
                    </el-collapse>
                  </span>
                  <span v-else-if="status == 2">
                    <el-collapse accordion>
                      <el-collapse-item name="1" disabled="true">
                        <template slot="title">
                          <el-tag type="success" effect="plain">Approved</el-tag>
                        </template>
                      </el-collapse-item>
                    </el-collapse>
                  </span>
                  <span v-else type="text">
                    <el-collapse accordion>
                      <el-collapse-item name="1">
                        <template slot="title">
                          <el-tag class="mx-1" type="danger" effect="plain">Rejected</el-tag>
                        </template>
                        <div style="float: left">
                          {{ note }}
                        </div>
                      </el-collapse-item>
                    </el-collapse>
                  </span>
                </div>

                <el-descriptions class="margin-top" :column="4" border>
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
              <partial-detail-product-detail />
            </el-tab-pane>
            <el-tab-pane>
              <template #label>
                <span class="custom-tabs-label">
                  <el-tooltip content="Bill Of Material" placement="top">
                    <span>Bill Of Material</span>
                  </el-tooltip>
                </span>
              </template>
              <el-table
                v-loading="listLoadingBillOf"
                :data="listBillOf"
                border
                fit
                highlight-current-row
                style="width: 100%"
              >
                <el-table-column align="center" label="Product">
                  <template slot-scope="scope">
                    <span>{{ scope.row.name }}</span>
                  </template>
                </el-table-column>

                <el-table-column align="center" label="Code" prop="code" />

                <el-table-column
                  align="center"
                  label="Quantity"
                  width="100px"
                  prop="qty"
                />
              </el-table>
              <pagination
                v-show="total > 0"
                :total="total"
                :page.sync="query.page"
                :limit.sync="query.limit"
                @pagination="getListBillOf"
              />
            </el-tab-pane>
          </el-tabs>
        </div>
      </sticky>
    </el-form>
    <el-dialog :visible.sync="dialogFormVisible" title="Reject" width="30%">
      <el-form ref="formReject" :model="RejectField" :rules="rules">
        <el-form-item label="Note">
          <el-input
            v-model="RejectField.note"
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
          <el-button type="primary" @click="saveReject()"> Confirm </el-button>
        </span>
      </template>
    </el-dialog>
  </div>
</template>
<script>
import Sticky from '@/components/Sticky';
import PartialDetailProductDetail from './PartialDetailProductDetail';
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import {
  ApproveProduct,
  RejectProduct,
  ShowProduct,
  dataShowBillOf,
} from '@/api/production/product';

export default {
  name: 'ArticleDetail',
  components: {
    Sticky,
    PartialDetailProductDetail,
    Pagination,
  },
  props: {
    isApprove: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      RejectField: {},
      loading: false,
      listLoadingBillOf: false,
      dialogFormVisible: false,
      listProduct: null,
      listBillOf: null,
      id: null,
      tempRoute: {},
      status: null,
      note: null,
      rules: {
        note: [
          { required: true, message: 'Note is required', trigger: 'blur' },
        ],
      },
      query: { page: 1, limit: 15, keyword: '', role: '', sort: '-id' },
    };
  },
  created() {
    const id = this.$route.params && this.$route.params.id;
    this.id = id;
    this.fetchData(this.id);
    this.query.id = id;
    this.tempRoute = Object.assign({}, this.$route);
    this.getList();
  },
  methods: {
    getList() {
      this.listLoadingBillOf = true;
      const { limit, page } = this.query;
      dataShowBillOf(this.query).then((response) => {
        this.listBillOf = response.data;
        this.listBillOf.forEach((element, index) => {
          element['index'] = (page - 1) * limit + index + 1;
        });
        this.total = response.total;
        this.listLoadingBillOf = false;
      });
    },
    fetchData(id) {
      ShowProduct(id)
        .then((response) => {
          this.listProduct = response.data;
          this.status = response.data.status;
          this.note = response.data.note;
        })
        .catch((err) => {
          console.log(err);
        });
    },
    handleReject() {
      this.RejectField = { id: this.id, status: 0 };
      this.dialogFormVisible = true;
    },
    saveReject() {
      this.supplierCreating = true;
      RejectProduct(this.RejectField)
        .then((response) => {
          this.$message({
            message: 'This product has been rejected successfully',
            type: 'success',
            duration: 5 * 1000,
          });
          this.$router.push({ path: '/production-product/approval' });
        })
        .catch((error) => {
          console.log(error);
        })
        .finally(() => {
          this.supplierCreating = false;
        });
    },

    handleApprove() {
      this.$confirm('Are you sure you want to approve this ? ', {
        confirmButtonText: 'Yes, approve it!',
        cancelButtonText: 'No, cancel',
        type: 'warning',
      })
        .then(() => {
          ApproveProduct(this.id)
            .then((response) => {
              this.$message({
                type: 'success',
                message: 'This product has been approved successfully',
              });
              this.$router.push({ path: '/production-product/approval' });
            })
            .catch((error) => {
              console.log(error);
            });
        })
        .catch(() => {
          this.$message({
            type: 'info',
            message: 'Approval has been canceled',
          });
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
