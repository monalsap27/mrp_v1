<template>
  <form-customer :is-edit="true" />
</template>

<script>
import formCustomer from './components/formCustomer';

export default {
  name: 'EditForm',
  components: { formCustomer },
};
</script>
