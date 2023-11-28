<template>
  <form-workstation :is-edit="false" />
</template>

<script>
import formWorkstation from './components/formWorkstation';

export default {
  name: 'CreateForm',
  components: { formWorkstation },
};
</script>
