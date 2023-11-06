<template>
  <form-workstation :is-edit="false" />
</template>

<script>
import FormWorkstation from './components/FormWorkstation';

export default {
  name: 'CreateForm',
  components: { FormWorkstation },
};
</script>
