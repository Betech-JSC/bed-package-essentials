<template>
    <Head title="Log in" />

    <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
        {{ status }}
    </div>

    <form class="space-y-6" @submit.prevent="submit">
        <FlashMessages />
        <Field
            v-model="form.email"
            :field="{
                label: 'Email',
            }"
        />
        <Field
            v-model="form.password"
            :field="{
                label: 'Mật khẩu',
                type: 'password',
            }"
        />
        <Field
            v-model="form.remember"
            :field="{
                type: 'checkbox',
                label: 'Ghi nhớ tôi',
            }"
        />
        <div class="space-y-2">
            <Button
                type="submit"
                :loading="form.processing"
                label="Đăng nhập"
                class="w-full"
            />

            <Link
                v-if="canResetPassword"
                :href="route('admin.password.request')"
                class="block w-full text-center underline"
            >
                Quên mật khẩu?
            </Link>
        </div>

        <div v-if="!isProduction">
            <hr class="py-3" />
            <Link
                :href="route('admin.local-login')"
                class="w-full btn btn-success"
                method="post"
            >
                Local Login
            </Link>
        </div>
    </form>
</template>

<script>
import Guest from "@Core/Layouts/Guest.vue";
import FlashMessages from "@Core/Components/FlashMessages.vue";

import { Head, Link } from "@inertiajs/inertia-vue3";
export default {
    components: {
        Head,
        Link,
        FlashMessages,
    },
    layout: Guest,

    props: {
        canResetPassword: Boolean,
        status: String,
    },

    data() {
        return {
            form: this.$inertia.form({
                email: null,
                password: null,
                remember: true,
            }),
            isProduction: import.meta.env.PROD,
        };
    },

    methods: {
        submit() {
            this.form
                .transform((data) => ({
                    ...data,
                    remember: this.form.remember ? "on" : "",
                }))
                .post(this.route("admin.login"), {
                    onFinish: () => this.form.reset("password"),
                });
        },
    },
};
</script>
