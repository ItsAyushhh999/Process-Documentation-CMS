<template>
    <div class="rounded-lg bg-white border border-slate-200 dark:bg-neutral-800 dark:text-slate-300 dark:border-neutral-700">
        <div class="flex">
            <slot name="header"/>
        </div>
        <div class="p-5">
            <slot />
        </div>
        <footer class="p-5 flex gap-3">
            <slot name="footer"/>
        </footer>
    </div>
</template>
<script>
    export default {
        name: "AppCard",
        inheritAttrs: false,
    }
</script>