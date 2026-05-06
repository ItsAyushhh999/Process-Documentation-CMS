<script setup>
// import { route } from '@inertiajs/vue3';
import { Link, usePage } from '@inertiajs/vue3';
import AppIcon from './AppIcon.vue';

const base_url = route().t.url;
const props = defineProps({
    item: {
        type: Object,
        default: null
    },
    isMinimized: {
        type: Boolean,
        default: false
    }
});

const authorizedIds = usePage().props.config.app.authorized_id;

const userId = usePage().props.auth.user.id;

const isAuthorizedForDevopsFeeds = props.item.name === 'Devops Feeds'
    ? authorizedIds.includes(userId)
    : true;

</script>

<template>
     <template v-if="isAuthorizedForDevopsFeeds">
    <Link
        :href="`${item.path}`"
        class="py-3 flex rounded-md hover:bg-primary-50/40 dark:hover:bg-primary-500/50 transition-all items-center font-medium tracking-wide capitalize relative dark:hover:text-slate-100 whitespace-nowrap"
        aria-current="page"
        :class="[
            $page.url == item.path
                ? 'bg-primary-50/80 text-primary-500 dark:bg-primary-500/40 dark:text-slate-200'
                : 'hover:text-primary-700',
            isMinimized ? 'px-4 justify-center' : 'pr-4 pl-6 gap-x-4 '
        ]"
    >
        <div
            v-show="$page.url == item.path && !isMinimized"
            class="w-1 absolute top-1/2 -translate-y-1/2 left-1 h-6 bg-primary-500/60 dark:bg-slate-300 block rounded-full transition-all"
        ></div>
        <div class="w-6">
            <AppIcon :name="item.icon" />
        </div>
        <span v-if="!isMinimized ">
            {{ item.name }}
        </span>
    </Link>
    </template>
</template>
