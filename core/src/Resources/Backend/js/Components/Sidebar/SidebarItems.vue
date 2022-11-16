<template>
    <div class="flex items-center flex-shrink-0 px-4">
        <Link href="/" class="flex items-center w-full p-2 space-x-2">
            <Avatar
                label="J"
                size="large"
                shape="circle"
                class="w-1/3 bg-white"
            />
            <div class="text-xl text-yellow-400">JAMstack Vietnam</div>
        </Link>
    </div>
    <nav class="flex-1 px-2 mt-5 space-y-1 navs">
        <SidebarMain />
        <hr />
        <Link
            v-if="adminAbilities.can('index', 'files')"
            :href="route('admin.files.index')"
            :class="{ active: isUrl('admin.files.*') }"
            class="item"
        >
            <ph:image />
            <span>{{ $t("models.table_list.files") }}</span>
        </Link>
        <Link
            v-if="adminAbilities.can('index', 'admins')"
            :href="route('admin.admins.index')"
            :class="{ active: isUrl('admin.admins.*') }"
            class="item"
        >
            <heroicons-outline:user-circle />
            <span>{{ $t("models.table_list.admins") }}</span>
        </Link>
        <Link
            v-if="adminAbilities.can('index', 'roles')"
            :href="route('admin.roles.index')"
            :class="{ active: isUrl('admin.roles.*') }"
            class="item"
        >
            <heroicons-outline:user-group />
            <span>{{ $t("models.table_list.roles") }}</span>
        </Link>
        <Link
            v-if="adminAbilities.can('index', 'translations')"
            :href="route('admin.translations.index')"
            :class="{ active: isUrl('admin.translations.*') }"
            class="item"
        >
            <ph-translate />
            <span>{{ $t("models.table_list.translations") }}</span>
        </Link>
        <Link
            v-if="adminAbilities.can('index', 'settings')"
            :href="route('admin.settings.form', { id: 'general' })"
            :class="{ active: isUrl('admin.settings.*') }"
            class="item"
        >
            <heroicons-outline:cog-8-tooth />
            <span>{{ $t("models.table_list.settings") }}</span>
        </Link>
    </nav>
</template>
<script>
export default {
    computed: {
        adminAbilities() {
            return this.bouncer(this.$page.props.admin);
        },
    },
};
</script>
<style lang="scss">
.navs {
    hr {
        @apply opacity-10;
    }
    .item {
        @apply flex items-center px-2 py-2 text-sm font-medium text-gray-300 rounded-md hover:bg-gray-700 hover:text-white space-x-2;
        svg {
            @apply h-6 w-6;
        }
        &:not(.active) {
            @apply bg-opacity-70;
            &:hover {
                @apply bg-opacity-100;
            }
        }
        &.active {
            @apply bg-gray-800 text-primary;
        }
    }
}
</style>
