<template>
    <div
        class="absolute inset-0 bg-gray-50 dark:bg-slate-800/90 flex items-center justify-center z-40"
    >
        <div class="flex flex-col items-center justify-center gap-10">
            <div class="loader"></div>
            <span class="text-xl font-semibold text-gray-500 animate-pulse">Loading...</span>
        </div>
    </div>
</template>

<script>
export default {
    name: 'AppLoader',
    inheritAttrs: false
};
</script>
<style scoped>
.loader {
    width: 50px;
    aspect-ratio: 1;
    border-radius: 50%;
    border: 8px solid #514b82;
    animation: l20-1 0.8s infinite linear alternate, l20-2 1.6s infinite linear;
}
@keyframes l20-1 {
    0% {
        clip-path: polygon(50% 50%, 0 0, 50% 0%, 50% 0%, 50% 0%, 50% 0%, 50% 0%);
    }
    12.5% {
        clip-path: polygon(50% 50%, 0 0, 50% 0%, 100% 0%, 100% 0%, 100% 0%, 100% 0%);
    }
    25% {
        clip-path: polygon(50% 50%, 0 0, 50% 0%, 100% 0%, 100% 100%, 100% 100%, 100% 100%);
    }
    50% {
        clip-path: polygon(50% 50%, 0 0, 50% 0%, 100% 0%, 100% 100%, 50% 100%, 0% 100%);
    }
    62.5% {
        clip-path: polygon(50% 50%, 100% 0, 100% 0%, 100% 0%, 100% 100%, 50% 100%, 0% 100%);
    }
    75% {
        clip-path: polygon(50% 50%, 100% 100%, 100% 100%, 100% 100%, 100% 100%, 50% 100%, 0% 100%);
    }
    100% {
        clip-path: polygon(50% 50%, 50% 100%, 50% 100%, 50% 100%, 50% 100%, 50% 100%, 0% 100%);
    }
}
@keyframes l20-2 {
    0% {
        transform: scaleY(1) rotate(0deg);
    }
    49.99% {
        transform: scaleY(1) rotate(135deg);
    }
    50% {
        transform: scaleY(-1) rotate(0deg);
    }
    100% {
        transform: scaleY(-1) rotate(-135deg);
    }
}
</style>
