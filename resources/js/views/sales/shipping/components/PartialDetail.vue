<template>
  <div class="createPost-container">
    <sticky :class-name="'sub-navbar '">
      <router-link :to="'/vendor-shipping/list'">
        <el-button class="filter-item" style="margin-left: 10px" type="info">
          Close
        </el-button>
      </router-link>
    </sticky>
    <div class="createPost-main-container">
      <div slot="header" class="clearfix">
        <span style="padding-right: 10px; padding-top: 15px; float: left">Delivery Order</span>
        <el-collapse accordion>
          <el-collapse-item name="1">
            <template slot="title">
              <el-tag :type="status | statusType">
                {{ status | statusFilter }}
              </el-tag>
            </template>
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
          {{ dataDelivery.nomor }}
        </el-descriptions-item>
        <el-descriptions-item>
          <template slot="label">
            <svg-icon icon-class="calendar-clock" />
            Date
          </template>
          {{ dataDelivery.created_at }}
        </el-descriptions-item>

        <el-descriptions-item>
          <template slot="label">
            <svg-icon icon-class="calendar-clock" />
            Received Date
          </template>
          {{ dataDelivery.received_date }}
        </el-descriptions-item>
      </el-descriptions>

      <br>
      <el-card class="box-card">
        <table />
        <el-table
          v-loading="listLoading"
          :data="listDetail"
          border
          fit
          highlight-current-row
          style="width: 100%"
        >
          <el-table-column
            align="center"
            label="Nomor Transaction"
            prop="nomor"
          />
          <el-table-column align="center" label="Product" prop="product_name" />
          <el-table-column
            align="center"
            label="Code"
            prop="product_code"
            width="150"
          />
          <el-table-column
            align="center"
            label="Quantity"
            prop="qty"
            width="150"
          />
        </el-table>
      </el-card>
    </div>
  </div>
</template>
<script>
import Sticky from '@/components/Sticky';
import { ShowDelivery, ShowDeliveryDetail } from '@/api/vendor/delivery_order';

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
      postForm: Object.assign({}, defaultForm),
      loading: false,
      listLoading: false,
      status: null,
      listDetail: [],
      dataDelivery: null,
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
    this.fetchData(this.id);
    this.tempRoute = Object.assign({}, this.$route);
  },
  methods: {
    fetchData(id) {
      this.listLoading = true;
      ShowDelivery(id)
        .then((response) => {
          this.dataDelivery = response.data;
          this.status = response.data.status;
        })
        .catch((err) => {
          console.log(err);
        });
      ShowDeliveryDetail(id)
        .then((response) => {
          response.data.forEach((element, index) => {
            this.listDetail.push({
              id: element.id,
              nomor: element.nomor,
              product_name: element.name,
              product_code: element.code,
              qty: element.qty,
            });
          });
          this.listLoading = false;
        })
        .catch((err) => {
          console.log(err);
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
