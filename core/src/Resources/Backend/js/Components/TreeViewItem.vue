<template>
    <Draggable
        tag="ul"
        :modelValue="items"
        @update:modelValue="$emit('update:modelValue', $event)"
        item-key="hash_id"
        handle=".handle"
        :animation="200"
        group="category"
        :disabled="!draggable"
        ghostClass="ghost"
        class="tree-view-list"
    >
        <template #item="{ element }">
            <li class="flex-col select-none tree-view-item">
                <div
                    class="flex cursor-pointer hover:bg-blue-50"
                    :class="{
                        'text-blue-500':
                            selectable &&
                            modelValue !== undefined &&
                            modelValue.some((x) => x[keyBy] === element[keyBy]),
                    }"
                >
                    <div
                        class="flex items-center w-full px-1 group handle"
                        @click="onClickItem(element)"
                    >
                        <div @click="onClickIcon(element)">
                            <ph-folder-notch-fill
                                v-if="!elementIsActive(element)"
                            />
                            <ph-folder-notch-open-fill v-else />
                        </div>
                        <label
                            class="flex-1 py-1"
                            @click="onClickName(element)"
                        >
                            {{ element[labelBy] }}
                        </label>
                    </div>
                </div>
                <TreeViewItem
                    :key="element.hash_id"
                    v-show="currentLevel < maxLevel"
                    :level="currentLevel + 1"
                    class="tree-view-list"
                    :class="[
                        {
                            'max-h-0 overflow-hidden':
                                !elementIsActive(element),
                        },
                        selectable ? 'ml-4' : 'ml-8',
                    ]"
                    :active="!elementIsActive(element)"
                    :field="field"
                    v-model="modelValue"
                    v-model:options="element[childrenBy]"
                />
            </li>
        </template>
    </Draggable>
</template>
<script>
import Draggable from "vuedraggable";

export default {
    props: ["modelValue", "field", "level", "active", "options"],
    emits: ["update:modelValue", "change", "update:options"],
    components: {
        Draggable,
    },
    computed: {
        keyBy() {
            return this.field.keyBy || "id";
        },
        labelBy() {
            return this.field.labelBy || "label";
        },
        childrenBy() {
            return this.field.childrenBy ?? "children";
        },
        currentLevel() {
            return this.level === undefined ? 1 : this.level;
        },
        currentLevelIsActive() {
            if (this.active !== undefined) {
                return this.active;
            } else if (this.currentLevel <= this.expandDefaultLevel) {
                return true;
            } else {
                return false;
            }
        },
        maxLevel() {
            return this.field.maxLevel ?? 3;
        },
        expandDefaultLevel() {
            return this.field.expandDefaultLevel ?? 1;
        },
        draggable() {
            return this.field.draggable ?? false;
        },
        selectable() {
            return this.field.selectable ?? false;
        },
        busKey() {
            return "treeSelectedItem" + (this.field.key ?? "");
        },
    },
    data() {
        return {
            items: [],
        };
    },

    watch: {
        "field.options"() {
            this.fetchSource();
        },
    },

    created() {
        this.fetchSource();
    },

    methods: {
        fetchSource() {
            if (this.currentLevel === 1) {
                if (this.field.options !== undefined) {
                    this.items = this.field.options;
                } else {
                    axios
                        .post(route("helper.model-data"), this.field.source)
                        .then((res) => {
                            this.items = res.data;
                        });
                }
            } else {
                this.items = this.options;
            }
        },
        elementIsActive(element) {
            if (element.active !== undefined) {
                return element.active;
            } else if (this.currentLevel + 1 <= this.expandDefaultLevel) {
                return true;
            } else {
                return false;
            }
        },
        onClickItem(element) {
            if (!this.selectable) {
                this.$bus.emit(this.busKey, element);
                element.active = !this.elementIsActive(element);
            }
        },
        onClickIcon(element) {
            if (this.selectable) {
                element.active = !this.elementIsActive(element);
            }
        },
        onClickName(element) {
            if (this.selectable) {
                this.$bus.emit(this.busKey, element);
            }
        },
    },
};
</script>
<style lang="scss" scoped>
.ghost {
    @apply opacity-50 border-t;
}
</style>
