<template layout>
    <WrapSetting>
        <Form
            v-model="formData"
            v-slot="{ form }"
            :config="{ canDestroy: false, addGrid: false }"
        >
            <div class="card">
                <div class="card-header">Thông tin chung</div>
                <div class="card-body">
                    <Field
                        v-model="form.robots_txt"
                        :field="{
                            rows: 10,
                            type: 'textarea',
                            name: 'robots_txt',
                            label: 'robots.txt',
                        }"
                    />
                    <a
                        :href="route().t.url + `/robots.txt`"
                        target="_blank"
                        class="link-append"
                    >
                        {{ route().t.url + "/robots.txt" }}
                    </a>
                </div>
            </div>

            <Field
                class="hidden"
                disabled
                v-model="form.id"
                :field="{ default: route().params.id }"
            />
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
