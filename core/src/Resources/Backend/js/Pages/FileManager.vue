<template>
  <Authenticated>
    <FileManager :show="true" :selectable="false" />
  </Authenticated>
</template>

<script>
import Authenticated from '@Core/Layouts/Authenticatedl.vue';
import FileManager from '@Core/Components/FileManager.vue';

export default {
  components: {
    Authenticated,
    FileManager,
  },
};
</script>
