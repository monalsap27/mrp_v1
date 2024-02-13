<template>
  <div>
    <div class="app-container">
      <div class="filter-container">
        <el-input
          v-model="query.keyword"
          :placeholder="$t('table.keyword')"
          style="width: 200px"
          class="filter-item"
          @keyup.enter.native="handleFilter"
        />
        <el-button
          class="filter-item"
          type="primary"
          icon="el-icon-search"
          @click="handleFilter"
        >
          {{ $t('table.search') }}
        </el-button>
      </div>
      <el-table
        v-loading="listLoading"
        :data="list"
        border
        fit
        highlight-current-row
        style="width: 100%"
      >
        <el-table-column align="center" label="Submission Date">
          <template slot-scope="scope">
            <span>{{
              scope.row.created_at | parseTime('{d}-{m}-{y} {h}:{i}')
            }}</span>
          </template>
        </el-table-column>
        <el-table-column align="center" label="Production Date">
          <template slot-scope="scope">
            <span>{{
              scope.row.production_date | parseTime('{d}-{m}-{y} {h}:{i}')
            }}</span>
          </template>
        </el-table-column>

        <el-table-column align="center" label="Name" prop="name" />

        <el-table-column
          align="center"
          label="Production Quantity"
          prop="qty"
        />
        <el-table-column align="center" label="Ingredients">
          <template #default="scope">
            <span v-if="scope.row.is_available == 2">
              <el-tag
                class="mx-1"
                type="success"
                effect="plain"
                @click="showDetail(scope.row.id)"
              >Available</el-tag>
            </span>
            <span v-else>
              <el-tag
                class="mx-1"
                type="danger"
                effect="plain"
                @click="showDetail(scope.row.id)"
              >Not Available</el-tag>
            </span>
          </template>
        </el-table-column>
        <el-table-column align="center" label="Status" prop="status">
          <template #default="scope">
            <span v-if="arrStatus.includes(scope.row.status)">
              <el-tag :type="scope.row && scope.row.status | statusType">
                {{ scope.row && scope.row.status | statusFilter }}
              </el-tag>
            </span>
            <span v-else>
              <el-tag class="mx-1" type="warning" effect="plain">On Progress</el-tag>
            </span>
          </template>
        </el-table-column>
        <el-table-column
          align="center"
          label="Nomor Sales Order"
          prop="nomor_so"
        />

        <el-table-column align="center" label="Actions" width="200">
          <template slot-scope="scope">
            <span v-if="scope.row.status == 1">
              <el-button
                type="success"
                size="small"
                @click="showDataApproval(scope.row)"
              >
                <svg-icon icon-class="question-square" />
              </el-button>
            </span>
          </template>
        </el-table-column>
      </el-table>
      <pagination
        v-show="total > 0"
        :total="total"
        :page.sync="query.page"
        :limit.sync="query.limit"
        @pagination="getList"
      />
      <el-dialog
        title="Material Details"
        :visible.sync="dialogTableVisible"
        width="60%"
      >
        <el-table v-loading="listDetailLoading" :data="listDetail">
          <el-table-column prop="name_material" label="Material" width="300" />
          <el-table-column prop="qty_stock" label="Stock" width="100" />
          <el-table-column
            prop="qty_produksi"
            label="Production Quantity"
            width="100"
          />
          <el-table-column prop="claim_stock" label="Claim Stock" width="110" />
          <el-table-column property="status" label="Status">
            <template #default="scope">
              <span v-if="scope.row.status">
                <el-tag class="mx-1" type="success" effect="plain">Available</el-tag>
              </span>
              <span v-else>
                <el-tag class="mx-1" type="danger" effect="plain">Not Available</el-tag>
              </span>
            </template>
          </el-table-column>
        </el-table>
      </el-dialog>
      <el-dialog
        :title="'Form Approval'"
        :visible.sync="dialogForm"
        width="35%"
      >
        <div v-loading="LoadingForm" class="form-container">
          <el-form
            ref="ManufaturingOrderForm"
            :model="dataManufaturingOrder"
            label-position="left"
            label-width="150px"
            style="max-width: 100%"
          >
            <el-col>
              <el-form-item
                :label="$t('Quantity')"
                prop="qty"
                style="width: 100%"
              >
                <el-input-number
                  v-model="dataManufaturingOrder.qty"
                  placeholder="type here"
                  style="width: 100%"
                  :min="1"
                />
              </el-form-item>
            </el-col>
            <el-col>
              <el-form-item :label="$t('Date')" prop="date" style="width: 100%">
                <el-date-picker
                  v-model="dataManufaturingOrder.date"
                  type="datetime"
                  placeholder="Select date and time"
                  format="dd-MM-yyyy HH:mm:ss"
                  value-format="yyyy-MM-dd HH:mm:ss"
                  style="width: 100%"
                />
              </el-form-item>
            </el-col>
          </el-form>
          <br>
          <div slot="footer" class="dialog-footer">
            <el-button
              v-loading="LoadingForm"
              style="margin-left: 10px"
              type="success"
              :value="2"
              @click="handleApprove"
            >
              Approve
            </el-button>
            <el-button
              v-loading="LoadingForm"
              style="margin-left: 10px"
              type="danger"
              :value="3"
              @click="handleApprove"
            >
              Reject
            </el-button>
          </div>
        </div>
        <el-dialog
          width="25%"
          title="Reason"
          :visible.sync="dialogNoteReject"
          append-to-body
        >
          <el-form
            ref="ManufaturingOrderForm"
            :model="dataManufaturingOrder"
            label-position="left"
            style="max-width: 100%"
          >
            <el-form-item style="width: 100%">
              <el-input
                v-model="dataManufaturingOrder.note"
                :rows="2"
                type="textarea"
                placeholder="Please input"
                autocomplete="off"
                style="width: 100%"
              />
            </el-form-item>
          </el-form>
          <template #footer>
            <span class="dialog-footer">
              <el-button @click="dialogNoteReject = false">Cancel</el-button>
              <el-button type="primary" @click="saveReject()">
                Confirm
              </el-button>
            </span>
          </template>
        </el-dialog>
      </el-dialog>
    </div>
  </div>
</template>
<script>
import Pagination from '@/components/Pagination'; // Secondary package based on el-pagination
import {
  fetchListApproval,
  detailListAvailable,
  storeApproval,
} from '@/api/production/manufactur_order';

export default {
  name: 'WorkstationList',
  components: { Pagination },
  filters: {
    statusType(status) {
      const statusMap = { 1: 'warning', 0: 'danger', 2: 'success' };
      return statusMap[status];
    },
    statusFilter(status) {
      const statusMap = {
        1: 'Planning',
        2: 'Approved',
        0: 'Rejected',
      };
      return statusMap[status];
    },
  },
  data() {
    return {
      total: 0,
      list: null,
      listDetail: null,
      dataManufaturingOrder: {},
      newSafety: { safety_stock: 0 },
      listLoading: true,
      listDetailLoading: true,
      dialogTableVisible: false,
      dialogForm: false,
      dialogNoteReject: false,
      LoadingForm: false,
      listLoadingStockIn: false,
      arrStatus: [0, 1, 2],
      query: {
        page: 1,
        limit: 15,
        keyword: '',
        role: '',
        sort: '-id',
      },
    };
  },
  created() {
    this.getList();
  },
  methods: {
    async getList() {
      const { limit, page } = this.query;
      this.listLoading = true;
      const { data, meta } = await fetchListApproval(this.query);
      this.list = data;
      this.list.forEach((element, index) => {
        element['index'] = (page - 1) * limit + index + 1;
      });
      this.total = meta.total;
      this.listLoading = false;
    },
    handleFilter() {
      this.query.page = 1;
      this.getList();
    },
    showDetail(id) {
      this.listDetailLoading = true;
      this.dialogTableVisible = true;
      detailListAvailable(id).then((response) => {
        this.listDetail = response.data.map((v) => {
          const stock =
            parseInt(v.qty_stock) -
            (parseInt(v.claim_stock) + parseInt(v.qty_produksi));
          const status = parseFloat(stock) > 0;
          this.$set(v, 'status', status);
          return v;
        });
        this.listDetail = response.data;
        this.listDetailLoading = false;
      });
    },
    showDataApproval(data) {
      this.dialogForm = true;
      this.dataManufaturingOrder = {
        id: data.id,
        qty: data.qty,
        date: data.production_date,
      };
    },
    resetNewForm() {
      this.dataManufaturingOrder = {
        id: '',
        qty: '',
        date: '',
        value: '',
        noe: '',
      };
    },
    saveReject() {
      this.supplierCreating = true;
      this.dataManufaturingOrder.status = '0';
      storeApproval(this.dataManufaturingOrder)
        .then((response) => {
          this.$message({
            message: 'This product has been rejected successfully',
            type: 'success',
            duration: 5 * 1000,
          });
          this.resetNewForm();
          this.dialogForm = false;
          this.handleFilter();
          this.LoadingForm = false;
          this.dialogNoteReject = false;
        })
        .catch((error) => {
          console.log(error);
        })
        .finally(() => {
          this.supplierCreating = false;
        });
    },
    handleApprove(event) {
      const value = event.target.value;
      if (value === '3') {
        this.dialogNoteReject = true;
      } else {
        this.LoadingForm = true;
        this.dataManufaturingOrder.status = '2';
        this.$confirm('Are you sure you want to approve this ? ', {
          confirmButtonText: 'Yes, approve it!',
          cancelButtonText: 'No, cancel',
          type: 'warning',
        })
          .then(() => {
            storeApproval(this.dataManufaturingOrder)
              .then((response) => {
                this.$message({
                  type: 'success',
                  message: 'This product has been approved successfully',
                });
                this.resetNewForm();
                this.dialogForm = false;
                this.handleFilter();
                this.LoadingForm = false;
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
      }
    },
  },
};
</script>

<style>
.el-tag {
  cursor: pointer;
}
</style>
