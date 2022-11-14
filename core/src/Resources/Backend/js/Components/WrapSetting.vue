<template>
    <div class="grid grid-cols-12 gap-y-4 lg:gap-x-6 lg:p-6">
        <SettingBar class="col-span-full lg:col-span-4" />
        <div class="col-span-full lg:col-span-8">
            <slot v-bind="{ ...$props, ...$attrs }" />
        </div>
    </div>
</template>

<script>
export default {
    props: ["schema"],
};
</script>
