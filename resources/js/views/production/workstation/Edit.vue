<template>
  <form-workstation :is-edit="true" />
</template>

<script>
import formWorkstation from './components/formWorkstation';

export default {
  name: 'EditForm',
  components: { formWorkstation },
};
</script>
