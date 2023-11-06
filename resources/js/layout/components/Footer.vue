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

  data() {
    return {
      elts: {
        text1: document.getElementById('text1'),
        text2: document.getElementById('text2'),
      },
      // The strings to morph between. You can change these to anything you want!
      texts: ['Semesta', 'JADIKAN', 'SATU', '#JADIKANSATU'],
      // Controls the speed of morphing.
      morphTime: 1,
      cooldownTime: 0.55,
      textIndex: this.texts.length - 1,
      time: new Date(),
      morph: 0,
      cooldown: this.cooldownTime,
      newTime: new Date(),
      shouldIncrementIndex: this.cooldown > 0,
      dt: (this.newTime - this.time) / 1500,
    };
  },
  methods: {
    //  this.elts.text1.textContent = texts[textIndex % texts.length],
    //   elts.text2.textContent = texts[(textIndex + 1) % texts.length],
    toggleSideBar() {
      this.$store.dispatch('app/toggleSideBar');
    },
    async logout() {
      await this.$store.dispatch('user/logout');
      this.$router.push(`/login?redirect=${this.$route.fullPath}`);
    },
    doMorph() {
      this.morph -= this.cooldown;
      this.cooldown = 0;

      let fraction = this.morph / this.morphTime;

      if (fraction > 1) {
        this.cooldown = this.cooldownTime;
        fraction = 1;
      }
      this.setMorph(fraction);
    },
    setMorph(fraction) {
      // fraction = Math.cos(fraction * Math.PI) / -2 + .5;
      this.elts.text2.style.filter = `blur(${Math.min(
        8 / fraction - 8,
        100
      )}px)`;
      this.elts.text2.style.opacity = `${Math.pow(fraction, 0.4) * 100}%`;

      fraction = 1 - fraction;
      this.elts.text1.style.filter = `blur(${Math.min(
        8 / fraction - 8,
        100
      )}px)`;
      this.elts.text1.style.opacity = `${Math.pow(fraction, 0.4) * 100}%`;

      this.elts.text1.textContent =
        this.texts[this.textIndex % this.texts.length];
      this.elts.text2.textContent =
        this.texts[(this.textIndex + 1) % this.texts.length];
    },
    doCooldown() {
      this.morph = 0;

      this.elts.text2.style.filter = '';
      this.elts.text2.style.opacity = '100%';

      this.elts.text1.style.filter = '';
      this.elts.text1.style.opacity = '0%';
    },
    animate() {
      requestAnimationFrame(this.animate);
      this.time = this.newTime;

      this.cooldown -= this.dt;

      if (this.cooldown <= 0) {
        if (this.shouldIncrementIndex) {
          this.textIndex++;
        }
        this.doMorph();
      } else {
        this.doCooldown();
      }
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
