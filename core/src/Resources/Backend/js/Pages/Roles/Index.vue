<template layout>
    <Table
        :schema="schema"
        :columns="['id', 'title', 'created_at']"
    />
</template>
<script>
export default {
    props: ["schema"],
};
</script>
