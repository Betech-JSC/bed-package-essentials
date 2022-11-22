<template>
    <template v-if="multiple">
        <div class="grid gap-3" :class="itemsPerRow">
            <div
                class="relative col-span-1 group"
                v-for="(file, index) in files"
                :key="index"
            >
                <div
                    class="absolute inset-0 transition-opacity duration-200 bg-white opacity-0 group-hover group-hover:opacity-50"
                ></div>

                <div
                    class="flex items-center justify-center overflow-hidden transition-colors duration-200 bg-gray-100 border border-gray-100 rounded select-none aspect-[1/1]"
                >
                    <img
                        v-if="isImage(file.static_url)"
                        :src="`${file.static_url}?w=200`"
                        class="object-contain w-full"
                    />
                    <div v-else class="flex items-center p-4 text-xs break-all">
                        {{ file.filename }}
                    </div>
                </div>
                <div
                    class="absolute flex invisible space-x-1 transition-all duration-200 opacity-0 right-2 bottom-2 group-hover:opacity-100 group-hover:visible"
                >
                    <Button
                        @click="chosenImage(file)"
                        label="Chọn"
                        class="btn-sm btn-white"
                    />
                    <Button
                        @click="removeSelectedFiles(index)"
                        label="Xóa"
                        class="btn-sm btn-white"
                    />
                </div>
            </div>
            <div
                class="relative col-span-1 group"
                v-if="files.length < maxItems"
            >
                <div
                    class="overflow-hidden text-gray-400 transition-colors duration-200 border border-gray-200 border-dashed rounded cursor-pointer select-none hover:border-gray-400 hover:text-gray-600 aspect-[1/1]"
                    @click="showMediaManager = true"
                >
                    <div class="flex items-center justify-center h-full">
                        <ph-plus-circle-light />
                    </div>
                </div>
            </div>
        </div>
    </template>
    <template v-else>
        <template v-if="Array.isArray(files) && files.length > 0">
            <div
                class="relative flex h-full max-w-xs bg-gray-200 border rounded-sm group"
            >
                <div class="absolute inset-0 flex items-center justify-center">
                    <div
                        class="absolute inset-0 transition-opacity duration-200 bg-white opacity-0 group-hover group-hover:opacity-50"
                        @click="showMediaManager = true"
                    ></div>
                    <div
                        class="absolute flex invisible space-x-2 transition-all duration-200 opacity-0 right-2 bottom-2 group-hover:opacity-100 group-hover:visible"
                    >
                        <Button
                            size="xs"
                            @click="removeSelectedFiles"
                            label="Xóa"
                            class="btn-sm btn-white"
                        />
                    </div>
                </div>
                <img
                    v-if="isImage(files[0].static_url)"
                    :src="`${files[0].static_url}?w=200`"
                    class="object-contain w-full"
                />
                <div v-else class="flex items-center p-4 text-xs break-all">
                    {{ files[0].filename }}
                </div>
            </div>
        </template>
        <template v-else>
            <div
                class="relative w-full h-full max-w-xs p-3 text-gray-700 transition-colors duration-200 bg-gray-100 rounded-sm cursor-pointer select-none hover:bg-gray-200 hover:text-gray-900"
                @click="showMediaManager = true"
            >
                <div
                    class="flex flex-col items-center justify-center w-full h-full py-4 space-y-2 text-xs font-medium text-center text-gray-600 border border-gray-400 border-dashed"
                >
                    <!-- <Icon name="image" v-if="field.icon ?? true" /> -->
                    <span>CLICK ĐỂ CHỌN</span>
                </div>
            </div>
        </template>
    </template>
    <FileManager
        v-if="showMediaManager"
        v-model:show="showMediaManager"
        @onSelect="onSelect"
        :multiple="multiple"
    />

    <Dialog
        header="Folder"
        v-model:visible="showFileModal"
        :breakpoints="{
            '960px': '75vw',
            '640px': '90vw',
        }"
        :style="{ width: '50vw' }"
        :draggable="false"
    >
        <div class="space-y-6">
            <div>
                <label
                    class="block mb-2 font-semibold tracking-wide text-gray-700 font-display"
                >
                    URL
                </label>
                <input
                    v-model="showFileModal.link"
                    type="text"
                    class="block w-full py-[0.5rem] px-[1rem] border border-gray-300 focus:border-solid focus:outline-none focus:ring-0 rounded"
                />
            </div>
            <template v-if="showFileModal.options">
                <template v-for="(option, field) in showFileModal.options">
                    <div>
                        <label
                            class="block mb-2 font-semibold tracking-wide text-gray-700 font-display"
                        >
                            {{ field }}
                        </label>
                        <input
                            v-model="showFileModal.options[field]"
                            type="text"
                            class="block w-full py-[0.5rem] px-[1rem] border border-gray-300 focus:border-solid focus:outline-none focus:ring-0 rounded"
                        />
                    </div>
                </template>
            </template>
        </div>

        <template #footer>
            <Button variant="white" @click="showFileModal = null" label="Hủy" />
            <Button
                type="button"
                class="ml-2"
                @click="editFileUpdate"
                label="Lưu"
            />
        </template>
    </Dialog>
</template>

<script>
import FileManager from "@Core/Components/FileManager.vue";

export default {
    components: {
        FileManager,
    },

    props: ["field", "modelValue"],
    emits: ["change"],

    data() {
        return {
            files: [],
            showMediaManager: false,
            showFileModal: null,
        };
    },

    computed: {
        multiple() {
            return this.field?.multiple ?? false;
        },
        accept() {
            return (
                this.field.accept ??
                "image/png, image/gif, image/jpeg ,image/webp"
            );
        },
        itemsPerRow() {
            return this.field.perRow ?? "grid-cols-4";
        },
        maxItems() {
            return this.field.max || 99;
        },
        expected() {
            return this.field.expected ?? "object";
        },
        expectedUrl() {
            return true;
        },
    },

    created() {
        this.initFiles();
    },

    methods: {
        chosenImage(file) {
            this.$bus.emit("SelectedImage", file);
        },
        initFiles() {
            if (this.multiple) {
                this.files = this.modelValue;
            } else if (
                !this.multiple &&
                this.modelValue !== null &&
                Object.keys(this.modelValue).length
            ) {
                this.files = [this.modelValue];
            }
        },
        onSelect(files) {
            this.files = files;
            if (this.multiple) {
                this.$emit("change", this.files);
            } else {
                this.$emit("change", this.files[0]);
            }
        },
        editFileUpdate() {
            const index = this.files.findIndex(
                (x) => x.url === this.showFileModal.url
            );
            this.files[index] = this.showFileModal;
            this.showFileModal = null;
            this.$emit("change", this.files);
        },
        removeSelectedFiles(index = 0) {
            this.files.splice(index, 1);
            this.$emit("change", this.files);
        },
    },
};
</script>
