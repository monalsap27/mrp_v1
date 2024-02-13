<template>
  <div class="app-container">
    <div class="filter-container" />
    <el-row :gutter="24">
      <el-card class="box-card" style="width: 100%; margin-bottom: 20px">
        <div slot="header" class="clearfix">
          <span>Progress Production</span>
        </div>
        <el-col :span="24">
          <el-collapse v-model="activeName">
            <el-collapse-item title="Description" name="1">
              <el-row :gutter="24">
                <el-col :span="16">
                  <el-descriptions
                    class="margin-top"
                    title=""
                    :column="1"
                    border
                  >
                    <template slot="extra" />
                    <el-descriptions-item>
                      <template slot="label"><i class="el-icon-goods" /> Name
                      </template>
                      <span style="width: 80%">{{ listProduct.name }} </span>
                    </el-descriptions-item>
                    <el-descriptions-item>
                      <template slot="label">
                        <svg-icon icon-class="boxes" />
                        Quantity
                      </template>
                      <span style="width: 80%">{{ listProduct.qty }} </span>
                    </el-descriptions-item>
                    <el-descriptions-item>
                      <template slot="label">
                        <svg-icon icon-class="calendar-clock" />
                        Date
                      </template>
                      <span style="width: 80%">{{
                        listProduct.date | parseTime('{d}-{m}-{y} {h}:{i}')
                      }}
                      </span>
                    </el-descriptions-item>
                    <el-descriptions-item>
                      <template slot="label">
                        <svg-icon icon-class="watch" />
                        Timing
                      </template>
                      <span style="width: 80%">{{ listProduct.total_timing }}
                      </span>
                    </el-descriptions-item>
                  </el-descriptions>
                </el-col>
                <el-col :span="8">
                  <div slot="header" class="clearfix">
                    <span>Progress</span>
                  </div>
                  <div
                    class="text item"
                    style="
                      width: 100%;
                      display: inline-block;
                      text-align: center;
                    "
                  >
                    <el-progress
                      type="dashboard"
                      :percentage="percentage"
                      :color="colors"
                    />
                  </div>
                  <div class="text item">
                    <el-statistic
                      ref="statistic"
                      format="DD day HH:mm:ss"
                      :value="countDown"
                      time-indices
                    />
                  </div>
                </el-col>
              </el-row>
            </el-collapse-item>
          </el-collapse>
        </el-col>
        <br>
        <br>
        <el-col :span="12" style="margin-bottom: 18px">
          <el-collapse accordion>
            <el-collapse-item name="2">
              <template slot="title"> Timeline </template>
              <div>
                <el-steps :active="3" align-center>
                  <el-timeline>
                    <el-timeline-item
                      v-for="(timeline, index) in dataTimeline"
                      :key="index"
                      :icon="timeline.icon"
                      :type="timeline.type"
                      :timestamp="timeline.date"
                    >
                      {{ timeline.name }} ({{ timeline.timing }})
                    </el-timeline-item>
                  </el-timeline>
                </el-steps>
              </div>
            </el-collapse-item>
          </el-collapse>
        </el-col>
        <el-col :span="12">
          <el-collapse accordion>
            <el-collapse-item name="3">
              <template slot="title"> Actual </template>
              <div>
                <el-steps :active="3" align-center>
                  <el-timeline>
                    <el-timeline-item
                      v-for="(timeline, index) in dataTimeline"
                      :key="index"
                      :icon="timeline.icon"
                      :type="timeline.type"
                      :timestamp="timeline.actual_date"
                    >
                      {{ timeline.name }} ({{ timeline.actual_timing }})
                    </el-timeline-item>
                  </el-timeline>
                </el-steps>
              </div>
            </el-collapse-item>
          </el-collapse>
        </el-col>
      </el-card>

      <el-card shadow="hover" style="width: 100%; margin-top: 20px">
        <el-table
          v-loading="listLoading"
          :data="listProgress"
          border
          fit
          highlight-current-row
          style="width: 100%"
        >
          <el-table-column align="center" label="Name" prop="name" />
          <el-table-column align="center" label="Start Date" prop="date" />
          <el-table-column align="center" label="Count Down">
            <template slot-scope="scope">
              <el-statistic
                ref="statistic"
                format="DD day HH:mm:ss"
                :value="scope.row.count_down"
                time-indices
              />
            </template>
          </el-table-column>
          <el-table-column
            align="center"
            label="Timing"
            prop="timing"
            width="100"
          />
          <el-table-column
            align="center"
            label="Actual Time"
            prop="actual_timing"
            width="150"
          />
          <el-table-column
            align="center"
            label="End Date"
            prop="actual_date"
            width="180"
          />
          <el-table-column align="center" label="Actions" width="200">
            <template slot-scope="scope">
              <span v-if="scope.row.is_start == 1">
                <el-tooltip
                  class="item"
                  effect="dark"
                  content="Pause"
                  placement="top-start"
                >
                  <el-button
                    type="warning"
                    size="small"
                    @click="pauseORFinish(scope.row, 1)"
                  >
                    <svg-icon icon-class="pause" />
                  </el-button>
                </el-tooltip>
                <el-tooltip
                  class="item"
                  effect="dark"
                  content="Stop OR Finish"
                  placement="top-start"
                >
                  <el-button
                    type="danger"
                    size="small"
                    @click="pauseORFinish(scope.row, 2)"
                  >
                    <svg-icon icon-class="stop" />
                  </el-button>
                </el-tooltip>
              </span>
              <span v-else>
                <el-tooltip
                  class="item"
                  effect="dark"
                  content="Start Production"
                  placement="top-start"
                >
                  <el-button
                    :disabled="scope.row.disable_start"
                    type="success"
                    size="small"
                    @click="startWorkstation(scope.row.id)"
                  >
                    <svg-icon icon-class="play" />
                  </el-button>

                  <el-button
                    class="filter-item"
                    style="margin-left: 10px"
                    type="primary"
                    icon="el-icon-plus"
                    @click="handleCreate"
                  >
                    {{ $t('table.add') }}
                  </el-button>
                </el-tooltip>
              </span>
            </template>
          </el-table-column>
        </el-table>
      </el-card>
      <el-card shadow="hover" style="width: 100%; margin-top: 20px">
        <el-table
          v-loading="listLoading"
          :data="listMaterial"
          border
          fit
          highlight-current-row
          style="width: 100%"
        >
          <el-table-column align="center" label="Name" prop="name" />
          <el-table-column align="center" label="Code" prop="code" />
          <el-table-column align="center" label="Quantity" prop="qty" />
          <el-table-column align="center" label="Actions" width="100">
            <template slot-scope="scope">
              <el-tooltip
                class="item"
                effect="dark"
                content="Change Control ID"
                placement="top-start"
              >
                <el-button
                  :disabled="changeMaterial"
                  type="primary"
                  size="small"
                  @click="handleStockOut(scope.row)"
                >
                  <svg-icon icon-class="exit" />
                </el-button>
              </el-tooltip>
            </template>
          </el-table-column>
        </el-table>
      </el-card>
    </el-row>
    <el-drawer
      title="Material"
      :visible.sync="drawerTable"
      direction="rtl"
      size="40%"
    >
      <el-table v-loading="loadingMaterialUsed" :data="listMaterialUsed">
        <el-table-column prop="name" label="Name" width="350" />
        <el-table-column prop="control_id" label="Control ID" width="300">
          <template #default="scope">
            <template v-if="scope.row.edit">
              <el-select
                v-model="scope.row.control_id"
                filterable
                class="filter-item w-200"
                placeholder="Please select control ID"
                style="width: 100%"
              >
                <el-option
                  v-for="item in controlIDOptions"
                  :key="item.control_id"
                  :label="item.control_id"
                  :value="item.control_id"
                />
              </el-select>
            </template>
            <span v-else style="margin-left: 10px">
              {{ scope.row.control_id }}
            </span>
          </template>
        </el-table-column>
        <el-table-column fixed="right" label="Action" width="80">
          <template #default="scope">
            <el-button
              v-if="!scope.row.edit"
              size="small"
              type="warning"
              @click="handleEdit(scope.$index, scope.row)"
            >Edit</el-button>
            <el-button
              v-else
              size="small"
              type="success"
              style="margin-left: 10px"
              @click="handleChangeControlID(scope.$index, scope.row)"
            >
              OK
            </el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-drawer>
  </div>
</template>

<script>
import {
  timelineProgress,
  showDetailManufaturingOrder,
  showDetailMaterial,
  startWorkstation,
  pauseORFinish,
  storeChangeControlID,
  getMaterialUsed,
} from '@/api/production/manufactur_order';
import { showControlID } from '@/api/production/stock';

export default {
  name: 'UnitList',
  components: {},
  filters: {
    statusFilter(status) {
      const statusMap = {
        published: 'success',
        draft: 'info',
        deleted: 'danger',
      };
      return statusMap[status];
    },
  },
  data() {
    return {
      activeName: '1',
      newStockOUT: {},
      dataTimeline: null,
      listProgress: null,
      listLoading: false,
      dialogFormStockOutVisible: false,
      listStockIN: null,
      LoadingStockOUT: false,
      loadingMaterialUsed: false,
      listProduct: null,
      countDown: null,
      listMaterial: null,
      changeMaterial: true,
      disabledMaterial: false,
      percentage: 0,
      drawerTable: false,
      paramsGetMaterialUsed: {},
      listMaterialUsed: null,
      controlIDOptions: [],
      changeControlID: {},
      colors: [
        { color: '#f56c6c', percentage: 20 },
        { color: '#e6a23c', percentage: 40 },
        { color: '#5cb87a', percentage: 60 },
        { color: '#1989fa', percentage: 80 },
        { color: '#6f7ad3', percentage: 100 },
      ],
    };
  },
  created() {
    const id = this.$route.params && this.$route.params.id;
    this.getList(id);
    this.paramsGetMaterialUsed.manufacture_order_id = id;
  },
  methods: {
    getList(id) {
      this.listLoading = true;
      timelineProgress(id).then((response) => {
        this.dataTimeline = response.data;
        this.listProgress = response.data;
        this.listProgress = response.data.map((v) => {
          this.$set(v, 'count_down', new Date(Date.parse(v.count_down)));
          return v;
        });
        response.data.forEach((element, index) => {
          if (element.is_start != null && element.finish_at == null) {
            this.changeMaterial = element.change_material;
          }
        });
        this.listLoading = false;
      });
      showDetailManufaturingOrder(id)
        .then((response) => {
          this.listProduct = response.data;
          this.countDown = new Date(Date.parse(this.listProduct.count_down));
          this.percentage = parseInt(this.listProduct.persen);
        })
        .catch((err) => {
          console.log(err);
        });
      showDetailMaterial(id).then((response) => {
        this.listMaterial = response.data;
        this.listLoading = false;
      });
    },
    startWorkstation(id) {
      this.listLoading = true;
      this.$confirm(
        'Are you sure you want to start production at this stage ? ',
        {
          confirmButtonText: 'Yes !',
          cancelButtonText: 'No, cancel',
          type: 'warning',
        }
      )
        .then(() => {
          startWorkstation(id)
            .then((response) => {
              this.$message({
                type: 'success',
                message: ' The product has started.',
              });
              this.listLoading = false;
              this.$router.go();
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
          this.resetNewForm();
          this.dialogForm = false;
          this.handleFilter();
          this.LoadingForm = false;
        });
    },

    pauseORFinish(data, type) {
      data.type = type;
      this.listLoading = true;
      pauseORFinish(data)
        .then((response) => {
          this.$message({
            type: 'success',
            message: ' The product has started.',
          });
          this.$router.go();
          this.listLoading = false;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    async handleStockOut(data) {
      this.loadingMaterialUsed = true;
      this.drawerTable = true;
      this.paramsGetMaterialUsed.product_id = data.product_id;
      getMaterialUsed(this.paramsGetMaterialUsed).then((response) => {
        this.listMaterialUsed = response.data;
        this.listMaterialUsed = response.data.map((v) => {
          this.$set(v, 'edit', false);
          return v;
        });
        this.loadingMaterialUsed = false;
      });
      showControlID(this.paramsGetMaterialUsed).then((response) => {
        this.controlIDOptions = response.data;
      });
    },
    handleSelectionChange(val) {
      this.listMaterial;
      this.newStockOUT.items = val;
    },
    handleEdit(index, row) {
      this.resetNewForm();
      this.listMaterialUsed.edit = false;
      row.edit = true;
      this.changeControlID.manufature_material_id = row.id;
    },
    handleChangeControlID(index, row) {
      this.loadingMaterialUsed = true;
      this.changeControlID = row;
      storeChangeControlID(this.changeControlID)
        .then((response) => {
          this.$message({
            type: 'success',
            message: ' Successfully changed control ID',
          });
          this.handleStockOut(this.paramsGetMaterialUsed);
          row.edit = false;
          this.loadingMaterialUsed = false;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    resetNewForm() {
      this.changeControlID = {
        manufature_material_id: '',
        control_id: '',
      };
    },
  },
};
</script>

<style lang="scss" scoped>
.edit-input {
  padding-right: 100px;
}
.cancel-btn {
  position: absolute;
  right: 15px;
  top: 10px;
}
.dialog-footer {
  text-align: left;
  padding-top: 0;
  margin-left: 150px;
}
.app-container {
  flex: 1;
  justify-content: space-between;
  font-size: 14px;
  padding-right: 8px;
  .block {
    float: left;
    min-width: 250px;
  }
  .clear-left {
    clear: left;
  }
}
.el-tag {
  cursor: pointer;
}
.el-button--small {
  border-radius: 10px;
}
</style>
<style scoped>
.dialog-footer button:first-child {
  margin-right: 10px;
}
</style>
