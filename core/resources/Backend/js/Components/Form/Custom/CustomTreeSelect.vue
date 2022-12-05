<template>
    <TreeSelect
        :model-value="selectedOptions"
        @change="selectChange($event)"
        :options="options"
        :display="!field.mode || field.mode === 'single' ? 'comma' : 'chip'"
        :selectionMode="field.mode || 'single'"
        :placeholder="placeholder"
        :loading="field.loading"
        :metaKeySelection="false"
    >
    </TreeSelect>
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
            return this.field.labelBy || "title";
        },
        placeholder() {
            return this.field.placeholder || `Chọn ${this.field.label}`;
        },
        options() {
            return this.field.options?.map((option) => this.transformOption(option));
            return this.field.options?.map((option) => {
                option.key = option[this.keyBy];
                option.label = option[this.labelBy];
                return option;
            });
        },
        selectedOptions() {
            let options = [];
            this.modelValue?.forEach((option) => {
                options[option] = true;
            });
            return options;
        },
    },

    methods: {
        selectChange(value) {
            this.$emit("change", Object.keys(value));
        },
        transformOption(option){
            let newOption = option;
            newOption.key = newOption[this.keyBy];
            newOption.label = newOption[this.labelBy];
            if (newOption.children && newOption.children.length){
                newOption.children.map(x => this.transformOption(x));
            }
            return newOption;
        }
    },
};
</script>
