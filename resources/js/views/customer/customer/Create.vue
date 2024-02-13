<template>
  <form-customer :is-edit="false" />
</template>

<script>
import formCustomer from './components/formCustomer';

export default {
  name: 'CreateForm',
  components: { formCustomer },
};
</script>
