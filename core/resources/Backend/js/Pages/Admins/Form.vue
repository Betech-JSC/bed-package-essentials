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
                    v-model="form.role"
                    :field="{
                        name: 'role',
                        type: 'radio_list',
                        label: 'Vai trò',
                        labelBy: 'roles',
                        keyBy: 'name',
                        labelBy: 'title',
                        source: {
                            model: 'JamstackVietnam\Core\Models\Role',
                            method: 'getRoles',
                            only: ['name', 'title'],
                        },
                    }"
                />
            </div>
        </div>
        <div class="card">
            <div class="card-header">Đổi mật khẩu</div>
            <div class="card-body">
                <div class="field-row">
                    <Field
                        v-model="form.new_password"
                        :field="{
                            type: 'password',
                            name: 'new_password',
                            label: 'Mật khẩu mới',
                        }"
                    />
                    <Field
                        v-model="form.new_password_confirmation"
                        :field="{
                            type: 'password',
                            name: 'new_password_confirmation',
                            label: 'Xác nhận mật khẩu mới',
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
