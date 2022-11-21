<template layout>
    <DataTable :value="items" responsiveLayout="scroll" :loading="loading">
        <Column field="key" header="Mặc định">
            <template #body="{ data }">
                <span v-html="data.key"></span>
            </template>
        </Column>
        <Column field="translations" header="Bản dịch">
            <template #body="{ data }">
                <table>
                    <template
                        v-for="(locale, index) in $page.props.locale.list"
                    >
                        <tr>
                            <td class="w-10 label">
                                {{ locale.toUpperCase() }}
                            </td>
                            <td>
                                <CustomEditor
                                    v-if="isHTML(data.translations[locale])"
                                    :modelValue="data.translations[locale]"
                                    @change="data.translations[locale] = $event"
                                    :field="{ size: 'sm' }"
                                />
                                <InputText
                                    v-else
                                    v-model="data.translations[locale]"
                                    @blur="
                                        updateTranslation(
                                            $event.target.value,
                                            data.key,
                                            locale
                                        )
                                    "
                                />
                            </td>
                        </tr>
                    </template>
                </table>
            </template>
        </Column>
    </DataTable>
</template>
<script>
import CustomEditor from "@Core/Components/Form/Custom/CustomEditor.vue";
export default {
    components: {
        CustomEditor,
    },
    props: ["schema"],
    data() {
        return {
            items: null,
            loading: false,
        };
    },

    mounted() {
        this.loadLazyData();
    },

    methods: {
        loadLazyData() {
            this.loading = true;
            this.$axios
                .get(this.route(`admin.translations.index`))
                .then((res) => {
                    const data = res.data;
                    this.loading = false;
                    this.items = data;
                });
        },
        updateTranslation(value, key, locale) {
            this.$axios.post(this.route(`admin.translations.store`), {
                value,
                key,
                locale,
            });
        },
        isHTML(string) {
            return /(<([^>]+)>)/.test(string);
        },
    },
};
</script>

<style lang="scss">
.p-datatable tr td:first-child {
    max-width: 12vw;
}
</style>
