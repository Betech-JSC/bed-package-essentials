<template layout>
    <Form v-model="formData" v-slot="{ form }">
        <div class="card">
            <div class="card-header">Thông tin chung</div>
            <div class="card-body">
                <Field
                    disabled
                    v-model="form.name"
                    :field="{
                        type: 'text',
                        name: 'name',
                        label: 'Loại tài khoản',
                    }"
                />
                <Field
                    v-model="form.title"
                    :field="{
                        type: 'text',
                        name: 'title',
                        label: 'Tên vai trò',
                    }"
                />
            </div>
        </div>
    </Form>
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
