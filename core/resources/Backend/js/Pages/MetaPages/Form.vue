<template layout>
    <WrapSetting>
        <Form
            v-model="formData"
            :config="{
                resource: 'settings.meta-pages',
                canDestroy: item.can_created,
                addGrid: false,
            }"
        >
            <template #default="{ form }">
                <SeoFields
                    :modelValue="form"
                    @update:modelValue="form = $event"
                    :config="{
                        hiddenFields: ['seo_slug'],
                        metaPage: true
                    }"
                />
            </template>
        </Form>
    </WrapSetting>
</template>
<script>
export default {
    props: ["item", "schema"],
    data() {
        return {
            formData: this.item,
        };
    },
};
</script>
