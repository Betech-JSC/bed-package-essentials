<template layout>
    <Form v-model="formData" v-slot="{ form }">
        <div class="card">
            <div class="card-header">Thông tin chung</div>
            <div class="card-body">
                <Field
                    v-model="form.name"
                    :field="{
                        type: 'text',
                        name: 'name',
                        label: 'Tên tài khoản',
                    }"
                />
                <div class="field-row">
                    <Field
                        v-model="form.email"
                        :field="{
                            type: 'email',
                            name: 'email',
                            label: 'Email',
                        }"
                    />
                    <Field
                        v-model="form.phone"
                        :field="{
                            type: 'text',
                            name: 'phone',
                            label: 'Số điện thoại',
                        }"
                    />
                </div>

                <Field
                    class="w-full"
                    v-model="form.role_id"
                    :field="{
                        name: 'role',
                        type: 'radio_list',
                        label: 'Vai trò',
                        keyBy: 'id',
                        labelBy: 'name',
                        source: {
                            model: 'Role',
                            method: 'getRoles',
                            only: ['name', 'id'],
                        },
                    }"
                />
            </div>
        </div>
        <div class="card">
            <div class="card-header">Đặt mật khẩu</div>
            <div class="card-body">
                <div class="field-row">
                    <Field
                        v-model="form.password"
                        :field="{
                            type: 'password',
                            name: 'password',
                            label: 'Mật khẩu',
                        }"
                    />
                    <Field
                        v-model="form.password_confirmation"
                        :field="{
                            type: 'password',
                            name: 'password_confirmation',
                            label: 'Xác nhận mật khẩu',
                        }"
                    />
                </div>
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
