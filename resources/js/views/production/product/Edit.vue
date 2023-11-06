<template>
  <form-product :is-edit="true" />
</template>

<script>
import formProduct from './components/formProduct';

export default {
  name: 'CreateForm',
  components: { formProduct },
};
</script>
