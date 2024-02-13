<template>
  <partial-detail :is-confirm="true" />
</template>

<script>
import PartialDetail from './components/PartialDetail';

export default {
  name: 'ApprovalForm',
  components: { PartialDetail },
};
</script>
