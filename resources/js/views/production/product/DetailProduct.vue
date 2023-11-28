<template>
  <partial-detail-product :is-approve="false" />
</template>

<script>
import PartialDetailProduct from './components/PartialDetailProduct';

export default {
  name: 'DetailForm',
  components: { PartialDetailProduct },
};
</script>
