<template>
  <el-table
    v-loading="loading"
    :data="list"
    height="600"
    style="width: 100%; padding-top: 15px"
  >
    <el-table-column label="Product" width="200">
      <template slot-scope="scope">
        {{ scope.row.name }}
      </template>
    </el-table-column>
    <el-table-column label="Quantity" width="100" align="center">
      <template slot-scope="scope">
        {{ scope.row && scope.row.qty }}
      </template>
    </el-table-column>
    <el-table-column label="Status" width="100" align="center">
      <template slot-scope="scope">
        <el-tag :type="scope.row && scope.row.status | statusType">
          {{ scope.row && scope.row.status | statusFilter }}
        </el-tag>
      </template>
    </el-table-column>
  </el-table>
</template>

<script>
import { fetchListThisMonth } from '@/api/production/manufactur_order';

export default {
  filters: {
    statusType(status) {
      const statusMap = {
        2: 'info',
        3: 'warning',
        4: 'success',
      };
      return statusMap[status];
    },
    statusFilter(status) {
      const statusMap = {
        1: 'Waiting',
        2: 'Draft',
        3: 'On Progress',
        4: 'Done',
      };
      return statusMap[status];
    },

    orderNoFilter(str) {
      return str;
    },
  },
  data() {
    return {
      list: [{ order_no: '1', price: '2', status: 'pending' }],
      loading: true,
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    async fetchData() {
      const { data } = await fetchListThisMonth();
      this.list = data;
      console.log(data);
      console.log(data);
      this.loading = false;
    },
  },
};
</script>
