<template>
  <partial-detail-order :is-approve="true" />
</template>

<script>
import PartialDetailOrder from './components/PartialDetailOrder';

export default {
  name: 'ApprovalForm',
  components: { PartialDetailOrder },
};
</script>
