<template layout>
    <Table :schema="schema" :columns="['id', 'name', 'email', 'role_names']" />
</template>
<script>
export default {
    props: ["schema"],
};
</script>
