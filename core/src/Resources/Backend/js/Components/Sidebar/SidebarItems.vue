<template>
    <div
        class="flex items-center flex-shrink-0 px-4 py-5 border-b border-gray-800"
    >
        <Link href="/" class="flex items-center w-full p-2 space-x-2">
            <img src="/assets/images/jam-logo.png" class="max-w-[9rem]" />
        </Link>
    </div>
    <nav class="flex-1 px-2 space-y-1 navs pt-5 pb-4">
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
        @apply flex items-center w-full gap-2 px-6 py-2 text-white transition duration-150 text-base;

        svg {
            @apply h-6 w-6;
        }
        &:not(.active) {
            @apply opacity-70;
            &:hover {
                @apply opacity-100;
            }
        }
        &.active {
            @apply bg-gray-800 text-primary;
        }
    }
}
</style>
