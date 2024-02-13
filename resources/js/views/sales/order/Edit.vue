<template>
  <form-order :is-edit="true" />
</template>

<script>
import formOrder from './components/formOrder';

export default {
  name: 'EditForm',
  components: { formOrder },
};
</script>
