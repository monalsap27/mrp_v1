<template>
  <div class="createPost-container">
    <el-form
      ref="postForm"
      v-loading.fullscreen.lock="listLoading"
      element-loading-background="rgba(122, 122, 122, 0.8)"
      element-loading-text="Loading..."
      :model="currentRequest"
      :rules="rules"
      class="form-container"
    >
      <sticky :class-name="'sub-navbar '">
        <span v-if="isConfirm">
          <el-button
            v-loading="loading"
            style="margin-left: 10px"
            type="success"
            @click="handleConfirm"
          >
            Confirm
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
          <router-link :to="'/vendor-request/list'">
            <el-button
              class="filter-item"
              style="margin-left: 10px"
              type="info"
            >
              Close
            </el-button>
          </router-link>
        </span>
      </sticky>
      <div class="createPost-main-container">
        <div slot="header" class="clearfix">
          <span style="padding-right: 10px; padding-top: 15px; float: left">Request</span>
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

        <el-descriptions class="margin-top" :column="3" border>
          <template slot="extra" />
          <el-descriptions-item>
            <template slot="label">
              <svg-icon icon-class="square-code-white" />
              Code
            </template>
            {{ currentRequest.nomor }}
          </el-descriptions-item>
          <el-descriptions-item>
            <template slot="label">
              <svg-icon icon-class="calendar-clock" />
              Date
            </template>
            {{ currentRequest.created_at }}
          </el-descriptions-item>
          <el-descriptions-item>
            <template slot="label">
              <svg-icon icon-class="dollar" />
              Total Price
            </template>
            {{ currentRequest.total_amount }}
          </el-descriptions-item>
        </el-descriptions>

        <br>
        <el-card class="box-card">
          <table />
          <el-table
            v-loading="listLoading"
            :data="currentRequest.items"
            border
            fit
            highlight-current-row
            style="width: 100%"
          >
            <el-table-column align="center" label="Name">
              <template #default="scope">
                <span style="margin-left: 10px">
                  {{ scope.row.product_name }}
                </span>
              </template>
            </el-table-column>
            <el-table-column
              align="center"
              label="Description"
              prop="product_code"
            />
            <el-table-column align="center" width="150">
              <template #header>
                <span style="margin-left: 10px">QTY Request </span>
              </template>
              <template #default="scope">
                <span style="margin-left: 10px">
                  {{ scope.row.qty_request }}
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
                    @change="change(scope.row)"
                  />
                </template>
                <span v-else style="margin-left: 10px">
                  {{ scope.row.qty }}
                </span>
              </template>
            </el-table-column>

            <el-table-column
              align="center"
              label="Unit Price Request"
              width="150"
            >
              <template #default="scope">
                <span style="margin-left: 10px">
                  {{ scope.row.unit_price_request }}
                </span>
              </template>
            </el-table-column>

            <el-table-column align="center" label="Unit Price" width="150">
              <template #default="scope">
                <template v-if="scope.row.edit">
                  <el-input-number
                    v-model="scope.row.unit_price"
                    class="edit-input"
                    size="small"
                    @change="change(scope.row)"
                  />
                </template>
                <span v-else style="margin-left: 10px">
                  {{ scope.row.unit_price }}
                </span>
              </template>
            </el-table-column>

            <el-table-column align="center" label="Total" width="150">
              <template #default="scope">
                <span style="margin-left: 10px">
                  {{ scope.row.total }}
                </span>
              </template>
            </el-table-column>

            <el-table-column fixed="right" label="Action" width="70">
              <template #default="scope">
                <el-button
                  v-if="!scope.row.edit && isConfirm"
                  size="small"
                  @click="handleEdit(scope.$index, scope.row)"
                >Edit</el-button>
              </template>
            </el-table-column>
          </el-table>
        </el-card>
      </div>
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
import {
  ShowRequest,
  ShowRequestDetail,
  StoreConfirm,
  RejectRequest,
} from '@/api/vendor/request';

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
  name: 'ArticleDetail',
  components: {
    Sticky,
  },
  filters: {
    statusType(status) {
      const statusMap = { 1: 'info', 0: 'danger', 2: 'success' };
      return statusMap[status];
    },
    statusFilter(status) {
      const statusMap = {
        1: 'Waiting',
        2: 'Approved',
        4: 'Delivery',
        0: 'Rejected',
      };
      return statusMap[status];
    },
    orderNoFilter(str) {
      return str;
    },
  },
  props: {
    isConfirm: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      currentRequest: {
        total_amount: 0,
        items: [],
      },
      postForm: Object.assign({}, defaultForm),
      loading: false,
      listLoading: false,
      status: null,
      note: null,
      RejectField: {},
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
    const id = this.$route.params && this.$route.params.id;
    this.id = id;
    this.currentRequest.id = id;
    this.fetchData(this.id);
    this.tempRoute = Object.assign({}, this.$route);
  },
  methods: {
    fetchData(id) {
      this.listLoading = true;
      ShowRequest(id)
        .then((response) => {
          this.currentRequest = response.data;
          this.currentRequest.items = [];
          this.status = response.data.status;
          this.note = response.data.note;
        })
        .catch((err) => {
          console.log(err);
        });
      ShowRequestDetail(id)
        .then((response) => {
          response.data.forEach((element, index) => {
            this.currentRequest.items.push({
              qty: element.qty,
              unit_price: element.unit_price,
              qty_request: element.qty_request,
              unit_price_request: element.unit_price_request,
              product_name: element.product_name,
              product_code: element.product_code,
              transaction_detail_id: element.transaction_detail_id,
              total:
                parseInt(element.unit_price_request) *
                parseInt(element.qty_request),
              edit: false,
            });
          });
          this.listLoading = false;
        })
        .catch((err) => {
          console.log(err);
        });
    },
    change(row) {
      row.total = parseInt(row.qty) * parseInt(row.unit_price);
      const sum = this.currentRequest.items.reduce((s, a) => s + a.total, 0);
      this.currentRequest.total_amount = sum;
    },
    handleEdit(index, row) {
      row.edit = true;
    },
    handleReject() {
      this.RejectField = { id: this.id, status: 0 };
      this.dialogFormVisible = true;
    },
    saveReject() {
      this.listLoading = true;
      RejectRequest(this.RejectField)
        .then((response) => {
          this.$message({
            message: 'This Request has been rejected successfully',
            type: 'success',
            duration: 5 * 1000,
          });
          this.$router.push({ path: '/vendor-request/list' });
        })
        .catch((error) => {
          console.log(error);
        })
        .finally(() => {
          this.listLoading = false;
        });
    },
    handleConfirm() {
      this.$confirm('Are you sure you want to confirm this ? ', {
        confirmButtonText: 'Yes, confirm it!',
        cancelButtonText: 'No, cancel',
        type: 'warning',
      })
        .then(() => {
          this.currentRequest.status = 2;
          StoreConfirm(this.currentRequest)
            .then((response) => {
              this.$message({
                type: 'success',
                message: 'This product has been approved successfully',
              });
              this.$router.push({ path: '/vendor-request/list' });
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
