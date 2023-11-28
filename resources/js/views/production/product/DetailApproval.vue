<template>
  <partial-detail-product :is-approve="true" />
</template>

<script>
import PartialDetailProduct from './components/PartialDetailProduct';

export default {
  name: 'ApprovalForm',
  components: { PartialDetailProduct },
};
</script>
