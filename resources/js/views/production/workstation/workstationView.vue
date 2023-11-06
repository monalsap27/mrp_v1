<template>
  <div class="createPost-container">
    <el-form ref="postForm" class="form-container">
      <sticky :class-name="'sub-navbar danger'">
        <router-link :to="'/production-workstation/workstation'">
          <el-button class="filter-item" type="danger"> Close </el-button>
        </router-link>
      </sticky>

      <div class="createPost-main-container">
        <el-row style="margin-top: 40px">
          <!-- <el-col :span="24"> -->
          <el-descriptions title="Workstation" :column="3" border>
            <el-descriptions-item label="Code">
              {{ dataWorkstation.code }}
            </el-descriptions-item>
            <el-descriptions-item label="Name">
              {{ dataWorkstation.name }}
            </el-descriptions-item>
            <el-descriptions-item width="50px" label="Description">
              {{ dataWorkstation.description }}
            </el-descriptions-item>
            <el-descriptions-item label="Timing">
              <el-tag size="small">{{ dataWorkstation.timing }}</el-tag>
            </el-descriptions-item>
            <el-descriptions-item>
              <template slot="label">
                Workforce ( {{ dataWorkstation.total_workforce }} )
              </template>
              No.1188, Wuzhong Avenue, Wuzhong District, Suzhou, Jiangsu
              Province
            </el-descriptions-item>
          </el-descriptions>
        </el-row>
        <br>
        <el-row>
          <label> Workstation Details</label>
          <el-table
            :data="wsDataDetail"
            border
            highlight-current-row
            max-height="250"
            style="width: 100%"
          >
            <el-table-column prop="id" label="ID" width="80">
              <template slot-scope="scope">
                <span>{{ scope.row.id }}</span>
              </template>
            </el-table-column>
            <el-table-column prop="product" label="Product">
              <template slot-scope="scope">
                <span>{{ scope.row.product }}</span>
              </template>
            </el-table-column>
            <el-table-column prop="qty" label="Quantity">
              <template slot-scope="scope">
                <span>{{ scope.row.qty }}</span>
              </template>
            </el-table-column>
          </el-table>
        </el-row>
      </div>
    </el-form>
  </div>
</template>
<script>
import Sticky from '@/components/Sticky';
import {
  fetchWorkstation,
  fetchWorkstationDetail,
} from '@/api/production/workstation';

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
  name: 'ViewWorkstation',
  components: { Sticky },
  props: {},
  data() {
    return {
      dataWorkstation: { items: [] },
      postForm: Object.assign({}, defaultForm),
      loading: false,
      workforce: [],
      productOptions: [],
      wsDataDetail: [],
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
    this.tempRoute = Object.assign({}, this.$route);
    const id = this.$route.params && this.$route.params.id;
    this.fetchData(id);
  },
  methods: {
    fetchData(id) {
      fetchWorkstation(id)
        .then((response) => {
          console.log(response);
          this.dataWorkstation = {
            id: response.data.id,
            name: response.data.name,
            code: response.data.code,
            description: response.data.description,
            timing: response.data.timing,
            workforce: response.data.workforce,
            total_workforce: response.data.total_workforce,
            items: [],
          };
        })
        .catch((err) => {
          console.log(err);
        });
      fetchWorkstationDetail(id)
        .then((response) => {
          console.log(response);
          response.data.forEach((element, index) => {
            this.wsDataDetail.push({
              product: element.product_code + ' - ' + element.product_name,
              qty: element.qty,
              id: element.id,
            });
          });
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
