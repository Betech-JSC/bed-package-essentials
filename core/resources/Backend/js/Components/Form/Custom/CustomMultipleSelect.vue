<template>
    <MultiSelect
        :model-value="selectedOptions"
        @change="selectChange($event.value)"
        :options="options"
        :loading="field.loading"
        :placeholder="placeholder"
        :optionLabel="labelBy"
        display="chip"
        :filter="field.filter || true"
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
            let options = [];
            this.field.options?.forEach((option) => {
                options.push({
                    [this.keyBy]: option[this.keyBy].toString(),
                    [this.labelBy]: option[this.labelBy].toString(),
                });
            });
            return options;
        },
        selectedOptions() {
            const selectedIds = this.modelValue?.map((option) => {
                if (typeof option === "object") {
                    return option[this.keyBy].toString();
                } else {
                    return option;
                }
            });
            return this.options.filter((x) =>
                selectedIds.includes(x[this.keyBy])
            );
        },
    },

    methods: {
        selectChange(value) {
            this.$emit("change", value);
        },
    },
};
</script>
