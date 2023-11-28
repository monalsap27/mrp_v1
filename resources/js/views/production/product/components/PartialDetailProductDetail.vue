<template>
  <div>
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
        <el-table-column align="center" label="Name" prop="workstation_name" />

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

        <el-table-column align="center" label="Material" prop="material" />
        <el-table-column align="center" label="QTY" width="100px" prop="qty" />
      </el-table>
    </el-card>
  </div>
</template>

<script>
import { ShowProductDetail } from '@/api/production/product';

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
      listDetailProduct: null,
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
      ShowProductDetail(id)
        .then((response) => {
          this.listDetailProduct = response.data;
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
