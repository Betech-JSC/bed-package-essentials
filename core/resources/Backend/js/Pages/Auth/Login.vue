<template>
  <Head title="Log in" />
  <!-- <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <img
                class="mx-auto h-[100px] w-auto"
                src="https://i.ibb.co/xJXHKkW/Logo-2.png"
                alt="Your Company"
            />
            <h2
                class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900"
            >
                Sign in to your account
            </h2>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-sm">
            <form class="space-y-6" @submit.prevent="submit">
                <div>
                    <Field
                        v-model="form.email"
                        :field="{
                            type: 'email',
                            name: 'email',
                        }"
                    />
                </div>

                <div>
                    <div class="mt-2">
                        <Field
                            v-model="form.password"
                            :field="{
                                type: 'password',
                                name: 'password',
                            }"
                        />
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    >
                        Sign in
                    </button>
                </div>
            </form>
        </div>
    </div> -->
  <section class="h-screen">
    <div class="container h-full px-6 py-24">
      <div
        class="flex h-full flex-wrap items-center justify-center lg:justify-between"
      >
        <!-- Left column container with background-->
        <div class="mb-12 md:mb-0 md:w-8/12 lg:w-7/12">
          <img
            src="https://tecdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.svg"
            class="w-full"
            alt="Phone image"
          />
        </div>

        <!-- Right column container with form -->
        <div class="md:w-8/12 lg:ms-6 lg:w-4/12">
          <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <img
              class="mx-auto h-[100px] w-auto"
              src="https://i.ibb.co/xJXHKkW/Logo-2.png"
              alt="Your Company"
            />
            <h2
              class="mt-5 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900"
            >
              Sign in
            </h2>
          </div>
          <form class="space-y-6" @submit.prevent="submit">
            <!-- Email input -->
            <Field
              v-model="form.email"
              :field="{
                type: 'email',
                name: 'email',
                placeholder:"Vui lòng email"
              }"
            />

            <!-- Password input -->
            <Field
              v-model="form.password"
              :field="{
                type: 'password',
                name: 'password',
                placeholder:"Vui lòng nhập password"
              }"
            />

            <!-- Submit button -->
            <button
              type="submit"
              class="inline-block w-full rounded bg-primary px-7 pb-2.5 pt-3 text-sm font-medium uppercase leading-normal text-white shadow-primary-3 transition duration-150 ease-in-out hover:bg-primary-accent-300 hover:shadow-primary-2 focus:bg-primary-accent-300 focus:shadow-primary-2 focus:outline-none focus:ring-0 active:bg-primary-600 active:shadow-primary-2 dark:shadow-black/30 dark:hover:shadow-dark-strong dark:focus:shadow-dark-strong dark:active:shadow-dark-strong"
              data-twe-ripple-init
              data-twe-ripple-color="light"
            >
              Sign in
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import FlashMessages from "@Core/Components/FlashMessages.vue";
import Guest from "@Core/Layouts/Guest.vue";

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
