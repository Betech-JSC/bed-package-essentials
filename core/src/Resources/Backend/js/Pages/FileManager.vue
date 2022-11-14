<template>
  <Authenticated>
    <FileManager :show="true" :selectable="false" />
  </Authenticated>
</template>

<script>
import Authenticated from '@Core/Layouts/Authenticated';
import FileManager from '@Core/Components/Fields/FileManager.vue';

export default {
  components: {
    Authenticated,
    FileManager,
  },
};
</script>
