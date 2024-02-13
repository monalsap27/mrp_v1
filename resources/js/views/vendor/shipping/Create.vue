<template>
  <form-delivery :is-edit="false" />
</template>

<script>
import formDelivery from './components/formDelivery';

export default {
  name: 'CreateForm',
  components: { formDelivery },
};
</script>
