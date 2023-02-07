<template layout>
    <WrapSetting :configs="configs">
        <Table
            :schema="schema"
            :config="{
                resource: 'settings.meta-pages',
                hideHeader: !can_created,
            }"
            :columns="['url', 'seo_meta_title', 'seo_meta_description']"
        />
    </WrapSetting>
</template>

<script>
import WrapSetting from "@Core/Components/WrapSetting.vue";
export default {
    components: { WrapSetting },
    props: ["schema", "can_created", "configs"],
};
</script>
