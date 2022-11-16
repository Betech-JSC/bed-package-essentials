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
                    <template v-for="(locale, index) in $page.props.locales">
                        <tr>
                            <td class="w-10 label">
                                {{ locale.toUpperCase() }}
                            </td>
                            <td>
                                <InputText
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
export default {
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
    },
};
</script>
