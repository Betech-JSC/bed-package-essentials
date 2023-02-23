<template layout>
    <WrapSetting>
        <Form
            v-model="formData"
            v-slot="{ form }"
            :config="{ canDestroy: false, addGrid: false }"
        >
            <div class="card">
                <div class="card-header">Danh sách</div>
                <div class="card-body">
                    <div>
                        <div
                            v-for="(item, index) in form.custom_vars"
                            :key="index"
                            class="my-2 space-y-2 bg-white rounded-md"
                        >
                            <div class="flex">
                                <div class="ml-auto">
                                    <div
                                        @click="confirmRemoveItem(index)"
                                        class="ml-auto border rounded cursor-pointer text-gray500 hover:text-gray-700 hover:bg-gray-100"
                                    >
                                        <material-symbols:delete-outline-sharp />
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <Field
                                    v-model="item.key"
                                    :field="{
                                        type: 'text',
                                        name: 'key',
                                        label: 'Key',
                                    }"
                                />
                                <Field
                                    v-model="item.value"
                                    :field="{
                                        type: 'textarea',
                                        name: 'value',
                                        label: 'Value',
                                    }"
                                />
                            </div>
                        </div>

                        <div class="mt-4 mb-6">
                            <Button
                                label="Thêm"
                                variant="white"
                                @click="
                                    this.formData.custom_vars.push({
                                        key: null,
                                        value: null,
                                    })
                                "
                            />
                        </div>
                    </div>
                </div>
            </div>

            <Field
                class="hidden"
                disabled
                v-model="form.id"
                :field="{ default: 'custom_vars' }"
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
            formData: {
                ...this.item,
                custom_vars: this.item.custom_vars ? JSON.parse(this.item.custom_vars) : [],
            },
        };
    },
    methods: {
        confirmRemoveItem(index) {
            if (confirm("Bạn có thực sự muốn xoá đối tượng này?")) {
                this.formData.custom_vars.splice(index, 1);
            }
        },
    },
};
</script>
