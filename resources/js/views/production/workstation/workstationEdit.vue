<template>
  <form-workstation :is-edit="true" />
</template>

<script>
import FormWorkstation from './components/FormWorkstation';

export default {
  name: 'EditForm',

  components: { FormWorkstation },
};
</script>
