<template layout>
    <WrapSetting>
        <Table
            :schema="schema"
            :config="{
                resource: 'settings.meta-pages',
                hideHeader: true,
            }"
            :columns="['url', 'seo_meta_title', 'seo_meta_description']"
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
