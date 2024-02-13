<template>
  <div>
    <el-card class="box-card">
      <div slot="header" class="clearfix">
        <span style="float: left">Details</span>
      </div>
      <el-table
        :data="listDetail"
        border
        fit
        highlight-current-row
        style="width: 100%"
      >
        <el-table-column align="center" label="Name" prop="product_name">
          <template slot-scope="scope">
            {{ scope.row.product_code }} - {{ scope.row.product_name }}
          </template>
        </el-table-column>
        <el-table-column align="center" label="QTY" prop="qty" />
        <el-table-column
          align="center"
          label="Unit Price"
          width="100px"
          prop="unit_price"
        />
        <el-table-column align="center" label="Sub Total" width="100">
          <template slot-scope="scope">
            {{ scope.row.unit_price * scope.row.qty }}
          </template>
        </el-table-column>
      </el-table>
    </el-card>
  </div>
</template>

<script>
import { ShowOrderDetail } from '@/api/purchasing/order';

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
  components: {},
  props: {},
  data() {
    return {
      postForm: Object.assign({}, defaultForm),
      loading: false,
      listDetail: null,
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
      ShowOrderDetail(id)
        .then((response) => {
          this.listDetail = response.data;
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
