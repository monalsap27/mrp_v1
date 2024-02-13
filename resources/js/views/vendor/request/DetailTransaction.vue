<template>
  <partial-detail :is-confirm="false" />
</template>

<script>
import PartialDetail from './components/PartialDetail';

export default {
  name: 'ApprovalForm',
  components: { PartialDetail },
};
</script>
