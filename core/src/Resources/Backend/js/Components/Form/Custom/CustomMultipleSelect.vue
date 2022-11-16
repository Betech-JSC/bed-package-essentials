<template>
    <MultiSelect
        :model-value="selectedOptions"
        @change="selectChange($event.value)"
        :options="options"
        :loading="field.loading"
        :optionLabel="labelBy"
        :placeholder="placeholder"
        display="chip"
    />
</template>

<script>
export default {
    props: ["field", "modelValue"],
    emits: ["change"],

    computed: {
        keyBy() {
            return this.field.keyBy || "id";
        },
        labelBy() {
            return this.field.labelBy || "label";
        },
        placeholder() {
            return this.field.placeholder || `Chọn ${this.field.label}`;
        },
        options() {
            return this.field.options?.map((option) => {
                option.key = option[this.keyBy];
                option.label = option[this.labelBy];
                return option;
            });
        },
        selectedOptions() {
            let options = [];
            this.modelValue?.forEach((option) => {
                options.push({
                    key: option[this.keyBy].toString(),
                    id: option[this.keyBy].toString(),
                    label: option[this.labelBy].toString(),
                    name: option[this.labelBy].toString(),
                });
            });
            return options;
        },
    },

    methods: {
        selectChange(value) {
            this.$emit("change", value);
        },
    },
};
</script>
