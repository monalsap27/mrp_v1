<template>
  <form-product :is-edit="false" />
</template>

<script>
import formProduct from './components/formProduct';

export default {
  name: 'CreateForm',
  components: { formProduct },
};
</script>
