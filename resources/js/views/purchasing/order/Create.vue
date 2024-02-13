<template>
  <form-order :is-edit="false" />
</template>

<script>
import formOrder from './components/formOrder';

export default {
  name: 'CreateForm',
  components: { formOrder },
};
</script>
