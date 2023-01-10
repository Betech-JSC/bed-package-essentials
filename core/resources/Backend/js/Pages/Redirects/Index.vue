<template layout>
    <WrapSetting>
        <Table
            :schema="schema"
            :config="{
                resource: 'settings.redirects',
            }"
            :columns="['old_url', 'new_url', 'status_code', 'is_active']"
        />
    </WrapSetting>
</template>
<script>
import WrapSetting from "@Core/Components/WrapSetting.vue";
export default {
    components: { WrapSetting },
    props: ["schema"],
};
</script>
