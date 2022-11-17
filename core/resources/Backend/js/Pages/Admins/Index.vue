<template layout>
    <Table :schema="schema" :columns="['id', 'name', 'email']" />
</template>
<script>
export default {
    props: ["schema"],
};
</script>
