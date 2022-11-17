<template>
    <div>
        <v-lazy-image :src="`${file.static_url}?w=200`" />
        <a
            class="absolute top-0 right-0 invisible space-x-1 text-white uppercase bg-black group-hover:visible w-[20px] h-[20px] flex items-center justify-center rounded-sm"
            :href="file.static_url"
            target="_blank"
        >
            <ph:arrow-square-up-right />
        </a>
        <Button
            class="absolute invisible right-1 bottom-1 group-hover:visible btn-white btn-sm"
            label="Xóa"
            @click="$emit('remove', file)"
        />
    </div>
</template>

<script>
import VLazyImage from "v-lazy-image";
export default {
    components: { VLazyImage },
    props: ["file"],
};
</script>

<style scoped>
.v-lazy-image {
    filter: blur(10px);
    transition: filter 0.5s cubic-bezier(0.65, 0, 0.35, 1);
    will-change: filter;
}
.v-lazy-image-loaded {
    filter: blur(0);
}
</style>
