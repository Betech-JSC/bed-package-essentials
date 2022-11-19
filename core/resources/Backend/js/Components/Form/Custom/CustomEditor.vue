<template>
    <Tiny
        api-key="7w40acxv5in6m21n1k9ny0tqej2d44gwyj6557igizadb1r6"
        :init="{ ...initConfig, height: size }"
        :modelValue="value"
        @update:modelValue="$emit('change', $event)"
    />
</template>

<script>
import Tiny from "@tinymce/tinymce-vue";
import templates from "@Core/templates";
const MAX_FILE_SIZE = 5;

export default {
    components: {
        Tiny,
    },
    props: {
        value: {
            type: String,
        },
        limit: {
            type: Number,
            default: 5000,
        },
        field: {
            type: Object,
        },
    },

    emits: ["change"],

    computed: {
        size() {
            return {
                sm: 300,
                md: 600,
                lg: 1000,
            }[this.field.size ?? "md"];
        },
    },

    data() {
        return {
            initConfig: {
                selector: "textarea#open-source-plugins",
                plugins:
                    "template importcss searchreplace autolink autosave save image link media table lists wordcount charmap quickbars code fullscreen",
                quickbars_insert_toolbar: "filemanager quicktable",
                menubar: "file edit view insert format tools table",
                toolbar:
                    "undo redo formatselect bold italic forecolor template fullscreen link filemanager image alignleft aligncenter alignright alignjustify bullist numlist removeformat code",

                templates,
                toolbar_sticky: true,
                autosave_ask_before_unload: false,
                autosave_interval: "30s",
                autosave_prefix: "{path}{query}-{id}-",
                autosave_restore_when_empty: false,
                autosave_retention: "2m",
                image_advtab: true,
                height: 600,
                image_caption: true,
                quickbars_selection_toolbar:
                    "bold italic quicklink h2 h3 blockquote quickimage quicktable",
                noneditable_noneditable_class: "mceNonEditable",
                toolbar_mode: "sliding",
                contextmenu: "link image imagetools table",
                skin: "oxide",
                content_css:
                    "https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css",
                importcss_append: true,
                content_style:
                    "body {margin:1rem;};img { max-width: 100%; height: auto; }",
                paste_data_images: true,
                convert_urls: false,
                images_upload_handler(blobInfo, success, failure, progress) {
                    const data = new FormData();
                    const file = new File(
                        [blobInfo.blob()],
                        blobInfo.filename()
                    );
                    data.append("files[]", file);
                    data.append(
                        "folder",
                        window.location.pathname.split("/")[1]
                    );

                    this.$axios
                        .post(route("admin.media.store"), data)
                        .then((response) => {
                            if (response.status === 200) {
                                success(response.data.data[0].static_url);
                            }
                        });
                },

                setup: function (editor) {
                    // editor.ui.registry.addButton("filemanager", {
                    //     icon: "gallery",
                    //     tooltip: "File Manager",
                    //     onAction: () =>
                    //         editor.windowManager.openUrl({
                    //             title: "File Manager",
                    //             url: `/admin/medias/embed`,
                    //             height: 640,
                    //             width: 1000,
                    //         }),
                    // });
                },
            },
        };
    },
};
</script>

<style lang="scss">
.tox-tinymce {
    border-radius: 3px !important;
    border: 1px solid rgb(206 212 218) !important;
}
.tox-statusbar__branding {
    display: none !important;
}
.tox-dialog {
    width: 90vw !important;
    max-width: 90vw !important;
    height: 90vh !important;
    max-height: 90vh !important;
}
.tox:not(.tox-tinymce-inline) .tox-editor-header {
    box-shadow: none !important;
    border-bottom: 1px solid rgb(206 212 218) !important;
}
.tox .tox-mbtn {
    height: 20px !important;
    margin: 0px !important;
}
.tox .tox-mbtn__select-label {
    margin: 0px !important;
    font-size: 12px !important;
}
.mce-content-body {
    padding: 1rem !important;
}
</style>
