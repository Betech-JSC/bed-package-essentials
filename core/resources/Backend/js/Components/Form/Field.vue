<template>
    <div class="field group">
        <label
            v-if="!hideLabel && field.type !== 'checkbox'"
            :for="fieldId"
            class="flex items-center label"
        >
            <span>
                {{
                    field.label ||
                    tt("models." + currentResource + "." + field.name)
                }}
            </span>
            <small
                class="invisible ml-auto font-normal normal-case group-hover:visible"
                v-if="
                    !field.type ||
                    field.type === 'text' ||
                    field.type === 'textarea' ||
                    field.type === 'richtext'
                "
            >
                {{ charCount }} ký tự, {{ wordCount }} từ
            </small>
        </label>
        <Input
            :id="fieldId"
            :class="{ 'p-invalid': !!fieldError }"
            v-bind="{ ...$props, ...$attrs }"
            v-model:field="fieldConfig"
            @update:modelValue="$emit('update:modelValue', $event)"
        />
        <small v-if="field.help" v-html="field.help" class="leading-4"></small>
        <small class="p-error" v-if="fieldError">
            {{ fieldError }}
        </small>
    </div>
</template>
<script>
export default {
    props: {
        field: {
            type: Object,
        },
        modelValue: {
            type: [Object, Array, Number, String, Boolean],
        },
        disabled: {
            type: Boolean,
            default: false,
        },
    },
    emits: ["update:modelValue"],
    computed: {
        fieldError() {
            return this.$parent.form?.errors[this.field.name];
        },
        fieldId() {
            return Math.random().toString(36).substr(2, 9);
        },
        wordCount() {
            if (typeof this.modelValue !== "string") return 0;
            return this.modelValue.trim().split(/\s+/).length;
        },
        charCount() {
            if (!this.modelValue) return 0;
            return this.modelValue.length;
        },
        currentResource() {
            return this.field.resource ?? this.getResource();
        },
        hideLabel() {
            return this.label === false;
        },
    },
    data() {
        return {
            fieldConfig: this.field,
        };
    },
    created() {
        if (this.field.source) {
            this.fieldConfig.loading = true;
            this.fetchSource();
        }
    },
    methods: {
        fetchSource() {
            this.$axios
                .post(this.route("admin.helper.model-data"), this.field.source)
                .then((res) => {
                    const keyBy = this.field.keyBy || "id";
                    this.fieldConfig.options = res.data.map((item) => {
                        item[keyBy] = item[keyBy].toString();
                        return item;
                    });
                    this.fieldConfig.loading = false;
                });
        },
    },
};
</script>
