<template layout>
    <WrapSetting>
        <Form
            v-model="formData"
            :config="{ canDestroy: false, reverse: true, addGrid: false }"
        >
            <template #default="{ form }">
                <div class="card">
                    <div class="card-body">
                        <Field
                            v-model="form.general_business_name"
                            :field="{
                                name: 'general_business_name',
                                label: 'Tên doanh nghiệp',
                            }"
                        />
                        <Field
                            v-model="form.general_site_name"
                            :field="{
                                name: 'general_site_name',
                                label: 'Tên website',
                            }"
                        />
                        <Field
                            v-model="form.general_email"
                            :field="{
                                name: 'general_email',
                                label: 'Email liên hệ',
                            }"
                        />
                    </div>
                </div>
                <SeoFields
                    :modelValue="form"
                    @update:modelValue="form = $event"
                    :config="{
                        hiddenFields: ['seo_slug'],
                    }"
                />
                <Field
                    class="hidden"
                    disabled
                    v-model="form.id"
                    :field="{ default: 'general' }"
                />
            </template>
        </Form>
    </WrapSetting>
</template>
<script>
import WrapSetting from "@Core/Components/WrapSetting.vue";
export default {
    components: { WrapSetting },
    props: ["item", "schema"],
    data() {
        return {
            formData: this.item,
        };
    },
};
</script>
