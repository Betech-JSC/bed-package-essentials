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
            return this.field.placeholder || `${this.tt('models.field.choose')} ${this.field.label}`;
        },
        options() {
            let options = [];
            this.field.options?.forEach((option) => {
                options.push({
                    id: option[this.keyBy].toString(),
                    [this.labelBy]: option[this.labelBy].toString(),
                });
            });
            return options;
        },
        selectedOptions() {
            let options = [];
            this.modelValue?.forEach((option) => {
                options.push({
                    id: option[this.keyBy].toString(),
                    [this.labelBy]: option[this.labelBy].toString(),
                });
            });
            return options.filter((x) =>
                this.pluck(this.options, this.keyBy).includes(x[this.keyBy])
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
