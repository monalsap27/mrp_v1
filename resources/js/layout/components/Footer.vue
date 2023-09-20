<template>

  <div class="footer">
    <h1>COPYRIGHT MRP</h1>
  </div>
</template>

<script>
// import { mapGetters } from 'vuex';
// import Breadcrumb from '@/components/Breadcrumb';
// import Hamburger from '@/components/Hamburger';
// import Screenfull from '@/components/Screenfull';
// import SizeSelect from '@/components/SizeSelect';
// import LangSelect from '@/components/LangSelect';
// import Search from '@/components/HeaderSearch';

export default {
  // eslint-disable-next-line vue/no-reserved-component-names
  name: 'Footer',
  components: {
    // Breadcrumb,
    // Hamburger,
    // Screenfull,
    // SizeSelect,
    // LangSelect,
    // Search,
  },
  // computed: {
  //   ...mapGetters([
  //     'sidebar',
  //     'name',
  //     'avatar',
  //     'device',
  //     'userId',
  //   ]),
  // },
  methods: {
    toggleSideBar() {
      this.$store.dispatch('app/toggleSideBar');
    },
    async logout() {
      await this.$store.dispatch('user/logout');
      this.$router.push(`/login?redirect=${this.$route.fullPath}`);
    },
  },
};
</script>

<style lang="scss" scoped>
.footer {
  height: 50px;
  // overflow: hidden;
  // position: relative;
  // background: #fff;
  // box-shadow: 0 1px 4px rgba(0,21,41,.08);
}
</style>
