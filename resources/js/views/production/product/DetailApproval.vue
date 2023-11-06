<template>
  <detail-product :is-approve="true" />
</template>

<script>
import detailProduct from './components/detailProduct';

export default {
  name: 'DetailProduct',
  components: { detailProduct },
};
</script>
