<template>
  <partial-detail-order :is-approve="false" />
</template>

<script>
import PartialDetailOrder from './components/PartialDetailOrder';

export default {
  name: 'ApprovalForm',
  components: { PartialDetailOrder },
};
</script>
