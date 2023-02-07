<template>
    <div
        class="fixed top-0 bottom-0 right-0 z-50 overflow-hidden bg-white"
        v-show="show"
        :class="embed ? 'left-0 overflow-auto' : 'left-from-sidebar'"
    >
        <input
            type="file"
            class="hidden"
            accept="image/png, image/gif, image/jpeg, image/svg+xml, application/pdf ,image/webp"
            multiple="true"
            ref="file"
            @change="fileChange"
        />
        <div class="topbar" v-if="!embed">
            <h1 class="flex items-center font-semibold text-gray-700">
                <div
                    class="p-4 -ml-4 cursor-pointer hover:text-gray-900"
                    @click="$emit('update:show', false)"
                    v-if="selectable"
                >
                    <ph-caret-left />
                </div>
                File Manager
            </h1>
            <div class="flex ml-auto space-x-3">
                <input
                    type="text"
                    placeholder="Nhập tên tệp..."
                    class="flex-inline w-[400px] py-[0.5rem] px-[1rem] border border-gray-300 focus:border-solid focus:outline-none focus:ring-0 rounded hover:border-gray-400 focus:border-gray-500"
                    @input="onChange"
                />
                <Button
                    @click.prevent="showFolderModal = true"
                    class="space-x-2 btn-outline-primary"
                >
                    <ph-plus-circle-light />
                    <span> Tạo Folder </span>
                </Button>
                <Button
                    v-if="isDeleteFolder"
                    @click="deleteFolder"
                    class="space-x-2 btn-outline-primary"
                >
                    <carbon:subtract-alt />
                    <span> Xóa Folder </span>
                </Button>
                <Button @click.prevent="browse" class="space-x-2 btn-primary">
                    <ph:upload-simple />
                    <span> Chọn Tệp </span>
                </Button>
            </div>
        </div>
        <div
            class="flex items-stretch flex-1 h-full overflow-hidden"
            @dragover.prevent="isDragging = true"
        >
            <div
                class="fixed inset-0 overflow-hidden border-4 border-dashed before:absolute before:bg-green-300 before:bg-opacity-25 before:inset-0 before:z-10 left-from-sidebar"
                :class="
                    isDragging
                        ? 'z-10 border-green-300 before:visible visible'
                        : 'z-0 border-transparent before:invisible invisible'
                "
                @dragleave.prevent="isDragging = false"
                @drop.prevent="
                    (isDragging = false), (dragCounter = 0), drop($event)
                "
            ></div>

            <!-- Details sidebar -->
            <aside
                class="hidden p-8 pb-16 overflow-y-auto bg-white border-l border-r border-gray-200 w-80 lg:block"
            >
                <template v-if="embed">
                    <Button
                        @click.prevent="browse"
                        class="w-full space-x-2 btn-primary"
                    >
                        <ph:upload-simple />
                        <span> Chọn Tệp </span>
                    </Button>
                    <hr class="my-2" />
                </template>
                <Field
                    v-if="tree && tree.length"
                    :field="{
                        key: 'FileManager',
                        label: false,
                        type: 'tree',
                        maxLevel: 10,
                        expandDefaultLevel: 2,
                        keyBy: 'slug',
                        labelBy: 'name',
                        childrenBy: 'children',
                        options: tree,
                        draggable: false,
                    }"
                />
            </aside>
            <main
                class="overflow-y-auto grow"
                :class="
                    !Object.keys(searchFiles).length
                        ? 'flex items-center justify-center'
                        : 'flex-1'
                "
            >
                <h1 v-if="!Object.keys(searchFiles).length" class="text-xl">
                    Kéo thả hoặc
                    <a @click="browse" class="link">click vào đây</a> để chọn
                    tệp
                </h1>
                <div
                    v-if="Object.keys(searchFiles).length"
                    class="px-4 pt-8 pb-16 mx-auto space-y-4 max-w-7xl sm:px-6 lg:px-8"
                >
                    <ul
                        role="list"
                        class="grid grid-cols-3 gap-4 lg:grid-cols-4 2xl:grid-cols-6"
                    >
                        <li
                            class="relative"
                            v-if="data"
                            v-for="(file, index) in searchFiles"
                            :key="file.static_url"
                        >
                            <div
                                class="group w-full rounded bg-gray-100 overflow-hidden aspect-[1/1] flex cursor-pointer justify-center items-center border border-transparent hover:border-gray-400 relative outline outline-offset-2 outline-2 outline-transparent"
                                :class="{
                                    'outline-black':
                                        selectedFiles.includes(file),
                                }"
                                @click="onSelect(file)"
                            >
                                <Thumbnail
                                    :file="file"
                                    @remove="onRemove(file)"
                                />
                            </div>
                            <p
                                class="block mt-2 text-sm font-medium text-gray-900 truncate pointer-events-none"
                            >
                                {{ file.filename }}
                            </p>
                            <p
                                class="block space-x-1 text-xs font-medium text-gray-500 pointer-events-none"
                            >
                                <span class="uppercase">
                                    {{ file.extension }}
                                </span>
                                <span>•</span>
                                <span>
                                    {{ file.formatted_file_size }}
                                </span>
                            </p>
                        </li>
                    </ul>

                    <Pagination
                        v-if="data"
                        :links="data.links"
                        @changePage="getFiles($event)"
                    />
                </div>
            </main>
        </div>
        <div
            v-if="multiple && selectedFiles.length > 0"
            class="absolute bottom-0 left-0 right-0 flex items-center justify-center w-full h-16 space-x-2 bg-white border-t"
        >
            <Button @click="selectedFiles = []"> Bỏ chọn </Button>
            <Button @click="submitFileSelect()">
                Chọn ({{ selectedFiles.length }})
            </Button>
        </div>
        <Dialog
            header="Folder"
            v-model:visible="showFolderModal"
            :breakpoints="{
                '960px': '75vw',
                '640px': '90vw',
            }"
            :style="{ width: '50vw' }"
            :draggable="false"
        >
            <Field
                v-model="folderForm.name"
                :field="{
                    rules: 'required',
                    label: 'Tên Folder',
                }"
            />
            <template #footer>
                <Button
                    variant="white"
                    @click="showFolderModal = false"
                    label="Hủy"
                />
                <Button
                    type="button"
                    class="ml-2"
                    @click="
                        createFolder(folderForm.name);
                        showFolderModal = false;
                    "
                    label="Lưu"
                />
            </template>
        </Dialog>
    </div>
</template>
<script>
import Pagination from "@Core/Components/Pagination.vue";
import Thumbnail from "@Core/Components/Thumbnail.vue";
import { onMounted, onUnmounted } from "vue";

const MAX_SIZE_OF_IMAGE = 5;
const MAX_SIZE_OF_VIDEO = 50;

export default {
    components: { Thumbnail, Pagination },
    props: {
        show: {
            default: false,
        },
        multiple: {
            default: false,
        },
        selectable: {
            default: true,
        },
    },

    setup(props, { emit }) {
        const close = () => {
            emit("update:show", false);
        };

        const closeOnEscape = (e) => {
            if (e.key === "Escape" && props.show) {
                close();
            }
        };

        onMounted(() => document.addEventListener("keydown", closeOnEscape));
        onUnmounted(() => {
            document.removeEventListener("keydown", closeOnEscape);
        });

        return {
            close,
        };
    },

    data() {
        return {
            uploadingFiles: [],
            selectedFiles: [],
            isDragging: false,
            timer: null,
            data: null,
            tree: null,
            currentPath: "/",
            showFolderModal: null,
            folderForm: {
                name: null,
            },
            search: null,
            embed: this.$page.props.route.query.embed,
            isDeleteFolder: false
        };
    },

    created() {
        this.getFiles();
    },

    mounted() {
        this.$bus.on("treeSelectedItemFileManager", (item) => {
            this.selectedItem(item);
        });
    },

    beforeDestroy() {
        this.$bus.off("treeSelectedItemFileManager");
    },

    watch: {
        show(value) {
            if (value && this.data === null) {
                this.getFiles();
            }
        },
        data() {
            this.isDeleteFolder = this.data.isDeleteFolder;
            this.tree = this.data.tree;
        }
    },

    computed: {
        searchFiles() {
            if (!this.data || !this.data.files) return [];
            if (!this.search) return this.data.files;
            return Object.values(this.data.files).filter((x) =>
                x.search_name.includes(this.search)
            );
        },
    },

    methods: {
        selectedItem(item) {
            this.currentPath = item.path;
            this.getFiles();
        },
        getFiles(params = {}) {
            this.$axios
                .get(
                    this.route("admin.files.index", {
                        page: 1,
                        search: null,
                        path: this.currentPath,
                        ...params,
                    })
                )
                .then((res) => {
                    this.data = res.data;
                    if (!this.tree) {
                        this.tree = res.data.tree;
                    }
                });
        },
        async copyUrl(file) {
            try {
                await navigator.clipboard.writeText(
                    this.staticUrl(file.static_url)
                );
            } catch ($e) {}
        },
        onSelect(file) {
            if (this.embed) {
                const src = "/static/" + new URL(file.static_url).pathname;
                window.parent.postMessage({
                    mceAction: "insertContent",
                    content: `<img src="${src}">`,
                });
                window.parent.postMessage({
                    mceAction: "close",
                });
                return;
            }

            if (!this.selectable) return;

            if (!this.multiple) {
                this.selectedFiles[0] = file;
                this.submitFileSelect();
            } else {
                if (!this.selectedFiles.includes(file)) {
                    this.selectedFiles.push(file);
                } else {
                    const fileIndex = this.selectedFiles.indexOf(file);
                    this.selectedFiles.splice(fileIndex, 1);
                }
            }
        },
        submitFileSelect() {
            this.$emit("on-select", this.selectedFiles);
            this.selectedFiles = [];
            this.$emit("update:show", false);
        },
        browse() {
            this.$refs.file.click();
        },
        drop(event) {
            this.uploadFiles(event.dataTransfer.files);
        },
        fileChange() {
            this.uploadFiles(this.$refs.file.files);
        },
        uploadFiles(images) {
            if (images.length === 0) return;

            for (const image of images) {
                const fileCheck = this.fileCheck(image);
                if (!fileCheck.valid) {
                    alert(
                        `Dung lượng file tối đa là ${fileCheck.maxSize}MB. Vui lòng thử lại.`
                    );
                    this.$refs.file.value = "";
                    return false;
                }
            }

            var formData = new FormData();
            for (let index = 0; index < images.length; index++) {
                const image = images[index];

                if (this.isImage(image.name)) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.uploadingFiles.push({
                            filename: image.name,
                            base64_code: e.target.result,
                        });
                    };
                    reader.readAsDataURL(image);
                } else {
                    this.uploadingFiles.push({
                        filename: image.name,
                        base64_code: null,
                        size: image.size,
                    });
                }
                formData.append("files[" + index + "]", image);
                formData.append("path", this.currentPath);
            }
            this.$refs.file.value = "";
            this.postFiles(formData);
        },
        postFiles(formData) {
            this.$axios
                .post(this.route("admin.files.store"), formData)
                .then((response) => {
                    if (response.status === 200) {
                        this.getFiles();
                    }
                });
        },
        onRemove(file) {
            this.$axios
                .post(this.route("admin.files.destroy", { id: 0 }), {
                    files: [file],
                })
                .then((response) => {
                    if (response.status === 200) {
                        this.getFiles();
                    }
                });
        },
        fileCheck(file) {
            const maxSize = this.isImage(file.name)
                ? MAX_SIZE_OF_IMAGE
                : MAX_SIZE_OF_VIDEO;
            const fileSize = file.size / 1024 / 1024;

            return { valid: fileSize <= maxSize, maxSize };
        },
        onChange(event) {
            if (this.timer) {
                clearTimeout(this.timer);
                this.timer = null;
            }

            this.timer = setTimeout(() => {
                this.search = event.target.value;
            }, 150);
        },
        deleteFolder() {
            this.$axios
                .post(
                this.route("admin.files.folders.delete", {
                    path: this.currentPath
                })
                )
                .then((res) => {});

            this.currentPath = "/";
            this.getFiles();
            this.isDeleteFolder = false;
        },
        createFolder(name) {
            this.$axios
                .post(this.route("admin.files.folders.create"), {
                    name: name,
                    path: this.currentPath,
                })
                .then((res) => {
                    this.tree = res.data;
                });

            this.currentPath = "/";
            this.getFiles();
        },
    },
};
</script>
<style lang="scss" scoped>
.left-from-sidebar {
    left: var(--sidebar-width);
}
.topbar {
    @apply absolute flex items-center flex-shrink-0 w-full px-4 bg-white border-b sm:px-10 md:px-12;
    height: var(--topbar-height);
}

.topbar + div {
    @apply fixed right-0;
    top: var(--topbar-height);
    height: calc(100% - var(--topbar-height));
    left: var(--sidebar-width);
}
</style>
