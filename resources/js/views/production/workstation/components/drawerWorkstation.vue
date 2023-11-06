<template>
  <el-drawer title="I am the title" :visible="drawer_show" :with-header="false">
    <span>Hi there!</span>
  </el-drawer>
</template>
<script>
export default {
  props: {
    value: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    drawer_show: {
      get() {
        return this.value;
      },
      set(val) {
        this.$emit('input', val);
      },
    },
  },
};
</script>
