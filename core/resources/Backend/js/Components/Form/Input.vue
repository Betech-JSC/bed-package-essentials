<template>
    <template
        v-if="
            field.type === undefined ||
            field.type === 'text' ||
            field.type === 'number' ||
            field.type === 'email' ||
            field.type === 'datetime-local' ||
            field.type === 'date' ||
            field.type === 'time'
        "
    >
        <InputText
            :type="field.type ?? 'text'"
            v-model="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            @blur="validateAsync(field.name)"
            v-model:disabled="disabled"
            :placeholder="field.placeholder || ''"
        />
    </template>
    <template v-else-if="field.type === 'decimal'">
        <InputNumber
            class="w-full"
            :min="0"
            :minFractionDigits="0"
            :maxFractionDigits="2"
            :modelValue="parseFloat(modelValue)"
            @blur="validateAsync(field.name)"
            @input="$emit('update:modelValue', $event.value || 0)"
            v-model:disabled="disabled"
        />
    </template>
    <template v-else-if="field.type === 'textarea'">
        <Textarea
            :modelValue="modelValue"
            v-model:disabled="disabled"
            @input="$emit('update:modelValue', $event.target.value)"
            :rows="field.rows || 3"
        />
    </template>
    <template v-else-if="field.type === 'richtext'">
        <CustomEditor
            :modelValue="modelValue"
            @change="$emit('update:modelValue', $event)"
            :field="field"
        />
    </template>
    <template v-else-if="field.type === 'money' || field.type === 'prefix'">
        <InputNumber
            class="w-full"
            :min="0"
            :prefix="field.prefix || 'ATN '"
            :modelValue="parseFloat(modelValue)"
            @input="$emit('update:modelValue', $event.value || 0)"
            @blur="validateAsync(field.name)"
            v-model:disabled="disabled"
        />
    </template>
    <template v-else-if="field.type === 'password'">
        <Password
            class="w-full"
            v-model="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            @blur="validateAsync(field.name)"
            :feedback="field.feedback || false"
            v-model:disabled="disabled"
            toggleMask
        ></Password>
    </template>
    <template v-else-if="field.type === 'checkbox'">
        <div class="flex space-x-2">
            <Checkbox
                :binary="true"
                :inputId="fieldId"
                :modelValue="Boolean(Number(modelValue))"
                @input="$emit('update:modelValue', Boolean($event))"
                @blur="validateAsync(field.name)"
                v-model:disabled="disabled"
            />
            <label v-if="field.label" :for="fieldId" class="label">{{
                field.label
            }}</label>
        </div>
    </template>
    <template v-else-if="field.type === 'select_button'">
        <CustomSelectButton
            :field="field"
            :model-value="modelValue"
            @change="$emit('update:modelValue', $event)"
            v-model:disabled="disabled"
        />
    </template>
    <template
        v-else-if="field.type === 'select_single' || field.type === 'dropdown'"
    >
        <CustomDropdown
            :field="field"
            v-model="modelValue"
            @change="$emit('update:modelValue', $event)"
            v-model:disabled="disabled"
        />
    </template>
    <template v-else-if="field.type === 'select_multiple'">
        <CustomMultipleSelect
            class="w-full"
            :field="field"
            v-model="modelValue"
            @change="$emit('update:modelValue', $event)"
            v-model:disabled="disabled"
        />
    </template>
    <template v-else-if="field.type === 'select_tree'">
        <CustomTreeSelect
            :field="field"
            v-model="modelValue"
            @change="$emit('update:modelValue', $event)"
            v-model:disabled="disabled"
        />
    </template>
    <template v-else-if="field.type === 'tree'">
        <TreeViewItem v-model="modelValue" :field="field" />
    </template>
    <template v-else-if="field.type === 'radio_list'">
        <CustomRadioList
            class="mt-2"
            :field="field"
            v-model="modelValue"
            @change="$emit('update:modelValue', $event)"
            v-model:disabled="disabled"
        />
    </template>
    <template v-else-if="field.type === 'select_date'">
        <CustomSelectDate
            :field="field"
            v-model="modelValue"
            @change="$emit('update:modelValue', $event)"
            v-model:disabled="disabled"
        />
    </template>
    <template v-else-if="field.type === 'file_upload'">
        <InputUpload
            :field="field"
            v-model="modelValue"
            @change="$emit('update:modelValue', $event)"
            v-model:disabled="disabled"
        />
    </template>

    <template v-else-if="field.type === 'tags'">
        <CustomTags
            :field="field"
            :model-value="modelValue"
            @change="$emit('update:modelValue', $event)"
            v-model:disabled="disabled"
        />
    </template>
</template>

<script>
import CustomDropdown from "@Core/Components/Form/Custom/CustomDropdown.vue";
import CustomEditor from "@Core/Components/Form/Custom/CustomEditor.vue";
import CustomMultipleSelect from "@Core/Components/Form/Custom/CustomMultipleSelect.vue";
import CustomRadioList from "@Core/Components/Form/Custom/CustomRadioList.vue";
import CustomSelectButton from "@Core/Components/Form/Custom/CustomSelectButton.vue";
import CustomSelectDate from "@Core/Components/Form/Custom/CustomSelectDate.vue";
import CustomTags from "@Core/Components/Form/Custom/CustomTags.vue";
import CustomTreeSelect from "@Core/Components/Form/Custom/CustomTreeSelect.vue";

import InputUpload from "@Core/Components/Form/InputUpload.vue";
import TreeViewItem from "@Core/Components/TreeViewItem.vue";

export default {
    props: ["field", "modelValue", "disabled"],
    emits: ["update:modelValue"],
    components: {
        CustomDropdown,
        CustomMultipleSelect,
        CustomRadioList,
        CustomSelectButton,
        CustomTreeSelect,
        CustomSelectDate,
        CustomEditor,
        CustomTags,
        TreeViewItem,
        InputUpload,
    },
    computed: {
        fieldId() {
            return "ID" + Math.random().toString(36).substr(2, 9).toUpperCase();
        },
    },

    created() {
        if (
            (this.modelValue === undefined || this.modelValue === null) &&
            this.field.default !== undefined
        ) {
            this.$emit("update:modelValue", this.field.default);
        }
    },
    methods: {
        validateAsync(name) {
            if (name && this.$parent.$parent.validateAsync) {
                if (name.includes("password")) {
                    return this.$parent.$parent.validateAsync(
                        "password",
                        "password_confirmation"
                    );
                }
                return this.$parent.$parent.validateAsync(name);
            }
        },
    },
};
</script>
