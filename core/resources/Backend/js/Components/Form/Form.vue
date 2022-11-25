<template>
    <form
        @submit.prevent="submit"
        :canaction="canStore"
        :class="{
            'grid grid-cols-12 gap-y-4 lg:gap-x-6 lg:p-6':
                config?.addGrid !== false,
        }"
    >
        <div class="space-y-6 col-span-full lg:col-span-8">
            <div class="card" v-if="form.deleted_at">
                <TrashedMessage v-if="form.deleted_at" />
            </div>
            <div class="card" v-if="hasFlash">
                <FlashMessages />
            </div>
            <slot :form="form" :errors="form.errors" :submit="submit" />
            <div
                v-if="showActions && (canDestroyResource || canStore)"
                class="flex justify-between card-footer"
            >
                <Button
                    v-if="canDestroyResource && !form.deleted_at && form.id"
                    label="Xóa"
                    class="btn-danger-link"
                    @click="destroy"
                />
                <Button
                    v-if="canStore"
                    label="Lưu"
                    type="submit"
                    :loading="form.processing"
                    class="ml-auto"
                />
            </div>
        </div>
        <div
            v-if="$slots.aside"
            class="col-span-full lg:col-span-4"
            :class="{ 'order-first': !!config.reverse }"
        >
            <slot name="aside" :form="form" />
        </div>
    </form>
</template>

<script>
import FlashMessages from "@Core/Components/FlashMessages.vue";
import TrashedMessage from "@Core/Components/TrashedMessage.vue";
export default {
    components: { FlashMessages, TrashedMessage },
    props: {
        modelValue: {
            type: Object,
        },
        config: {
            type: Object,
            default: () => ({}),
        },
    },
    emits: ["update:modelValue"],
    data() {
        return {
            form: this.$inertia.form(this.modelValue),
        };
    },
    watch: {
        form: {
            deep: true,
            handler(value) {
                this.$emit("update:modelValue", value);
            },
        },
    },
    computed: {
        admin() {
            return this.bouncer(this.$page.props.admin);
        },
        showActions() {
            return this.config?.showActions ?? true;
        },
        currentResource() {
            return this.config.resource ?? this.getResource();
        },
        canDestroyResource() {
            const canDestroy = this.config.canDestroy ?? true;
            return (
                canDestroy && this.admin.can("destroy", this.currentResource)
            );
        },
        canStore() {
            return (
                this.config.canStore ??
                this.admin.can("store", this.currentResource)
            );
        },
        hasFlash() {
            const { errors, flash } = this.$page.props;
            return (
                this.config?.showFlashMessages ??
                (flash.success !== null ||
                    flash.error !== null ||
                    Object.keys(errors).length > 0)
            );
        },
    },
    methods: {
        pick(obj, fields) {
            return fields.reduce(
                (acc, cur) => ((acc[cur] = obj[cur]), acc),
                {}
            );
        },

        validateAsync(...fields) {
            this.$inertia.post(
                this.route(`admin.${this.currentResource}.store`, {
                    id: this.form?.id,
                }),
                this.pick(this.form, fields),
                {
                    preserveScroll: true,
                    headers: { "X-Dry-Run": true },
                    onError: (errors) => this.form.setError(errors),
                    onSuccess: () => this.form.clearErrors(...fields),
                }
            );
        },

        submit() {
            this.$inertia.post(
                this.route(`admin.${this.currentResource}.store`, {
                    id: this.form?.id,
                }),
                this.form
            );
        },

        destroy() {
            if (confirm("Bạn chắc chắn muốn xoá đối tượng này?")) {
                this.$inertia.post(
                    this.route(`admin.${this.currentResource}.destroy`, {
                        id: this.form.id,
                    })
                );
            }
        },

        restore() {
            if (confirm("Bạn muốn khôi phục đối tượng này?")) {
                this.$inertia.post(
                    this.route(`admin.${this.currentResource}.restore`, {
                        id: this.form.id,
                    })
                );
            }
        },
    },
};
</script>
