<template>
    <div class="flex flex-shrink-0 p-4 bg-gray-700">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center">
                <Avatar
                    :label="admin && admin.name ? admin.name.charAt(0) : 'JAM'"
                    size="large"
                    shape="circle"
                    class="text-white bg-primary"
                />
                <div class="ml-3">
                    <div class="text-base font-medium text-white">
                        {{ admin ? admin.name : "" }}
                    </div>
                    <div
                        class="text-sm font-medium text-gray-400 cursor-pointer group-hover:text-gray-300"
                    >
                        <div @click="showAdminForm = true">Đổi mật khẩu</div>
                    </div>

                    <Form
                        v-model="adminForm"
                        v-slot="{ form }"
                        class="card"
                        :config="{
                            resource: 'admins',
                            showActions: false,
                            showFlashMessages: false,
                            addGrid: false
                        }"
                    >
                        <Dialog
                            header="Cập nhật thông tin"
                            v-model:visible="showAdminForm"
                            :breakpoints="{
                                '960px': '75vw',
                                '640px': '90vw',
                            }"
                            :style="{ width: '50vw' }"
                            :draggable="false"
                        >
                            <Field
                                v-model="adminForm.name"
                                :field="{
                                    type: 'text',
                                    name: 'name',
                                    label: 'Tên',
                                }"
                            />
                            <div class="mt-6 field-row">
                                <Field
                                    v-model="form.password"
                                    :field="{
                                        type: 'password',
                                        name: 'password',
                                        label: 'Mật khẩu mới',
                                    }"
                                />
                                <Field
                                    v-model="form.password_confirmation"
                                    :field="{
                                        type: 'password',
                                        name: 'password_confirmation',
                                        label: 'Xác nhận mật khẩu mới',
                                    }"
                                />
                            </div>
                            <template #footer>
                                <Button
                                    label="Hủy"
                                    @click="showAdminForm = false"
                                />
                                <Button
                                    label="Lưu"
                                    icon="pi pi-check"
                                    @click="submitAdminForm"
                                    class="btn-primary"
                                    autofocus
                                />
                            </template>
                        </Dialog>
                    </Form>
                </div>
            </div>
            <Link
                class="flex-shrink-0"
                method="post"
                :href="route('admin.logout')"
            >
                <heroicons-outline:logout
                    class="w-10 h-10 p-1 text-gray-400 hover:text-white"
                    aria-hidden="true"
                />
            </Link>
        </div>
    </div>
</template>

<script>
export default {
    props: ["admin"],
    data() {
        return {
            adminForm: this.$inertia.form(this.getEmptyForm()),
            showAdminForm: false,
        };
    },
    methods: {
        submitAdminForm() {
            this.$axios
                .put(this.route(`admin.updateInformation`), this.adminForm)
                .then((res) => {
                    this.adminForm.reset();
                    this.showAdminForm = false;
                    this.$toast.add({
                        severity: "success",
                        summary: "Thành công",
                        detail: `Cập nhật thông tin thành công`,
                        life: 2000,
                    });
                    window.location.href = window.location.href;
                })
                .catch(({ response }) => {
                    this.$toast.add({
                        severity: "error",
                        summary: "Thất bại",
                        detail: response.data.message,
                        life: 2000,
                    });
                });
        },

        getEmptyForm() {
            return {
                id: this.admin?.id,
                name: this.admin?.name,
                password: null,
                password_confirmation: null,
            };
        },
    },
};
</script>
