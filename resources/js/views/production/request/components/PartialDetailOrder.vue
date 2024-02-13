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
          <router-link :to="'/purchasing-approval/list'">
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
          <router-link :to="'/purchasing-order/list'">
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
                  <span>Order</span>
                </span>
              </template>
              <el-card class="box-card">
                <div slot="header" class="clearfix">
                  <span style="padding-right: 10px; float: left">Order</span>
                  <el-collapse accordion>
                    <el-collapse-item name="1">
                      <template slot="title">
                        <el-tag :type="status | statusType">
                          {{ status | statusFilter }}
                        </el-tag>
                      </template>
                      <div style="float: left">
                        {{ note }}
                      </div>
                    </el-collapse-item>
                  </el-collapse>
                </div>

                <el-descriptions class="margin-top" :column="2" border>
                  <template slot="extra" />
                  <el-descriptions-item>
                    <template slot="label">
                      <svg-icon icon-class="square-code-white" />
                      Code
                    </template>
                    {{ listOrder.nomor }}
                  </el-descriptions-item>
                  <el-descriptions-item>
                    <template slot="label">
                      <svg-icon icon-class="calendar-clock" />
                      Date
                    </template>
                    {{ listOrder.created_at }}
                  </el-descriptions-item>
                  <el-descriptions-item>
                    <template slot="label">
                      <svg-icon icon-class="3m" />
                      Supplier
                    </template>
                    {{ listOrder.supplier_name }}
                  </el-descriptions-item>
                  <el-descriptions-item>
                    <template slot="label">
                      <svg-icon icon-class="dollar" />
                      Total Price
                    </template>
                    {{ listOrder.total_amount }}
                  </el-descriptions-item>
                  <el-descriptions-item>
                    <template slot="label">
                      <svg-icon icon-class="user" />
                      Created By
                    </template>
                    {{ listOrder.user_name }}
                  </el-descriptions-item>
                </el-descriptions>
              </el-card>
            </el-tab-pane>
            <el-tab-pane>
              <template #label>
                <span class="custom-tabs-label">
                  <el-tooltip content="I make this product" placement="top">
                    <span>Order details</span>
                  </el-tooltip>
                </span>
              </template>
              <partial-order-product />
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
import PartialOrderProduct from './PartialOrderProduct';

import { ApproveOrders, RejectOrders, ShowOrder } from '@/api/purchasing/order';

export default {
  name: 'ArticleDetail',
  components: {
    Sticky,
    PartialOrderProduct,
  },
  filters: {
    statusType(status) {
      const statusMap = { 1: 'info', 0: 'danger', 2: 'success', 6: 'success' };
      return statusMap[status];
    },
    statusFilter(status) {
      const statusMap = {
        1: 'Waiting',
        2: 'Approved',
        0: 'Rejected',
        6: 'Done',
      };
      return statusMap[status];
    },
    orderNoFilter(str) {
      return str;
    },
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
      listOrder: null,
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
  },
  methods: {
    fetchData(id) {
      ShowOrder(id)
        .then((response) => {
          this.listOrder = response.data;
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
      RejectOrders(this.RejectField)
        .then((response) => {
          this.$message({
            message: 'This PO has been rejected successfully',
            type: 'success',
            duration: 5 * 1000,
          });
          this.$router.push({ path: '/purchasing-approval/list' });
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
          ApproveOrders(this.id)
            .then((response) => {
              this.$message({
                type: 'success',
                message: 'This product has been approved successfully',
              });
              this.$router.push({ path: '/purchasing-approval/list' });
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
