<template>
    <Teleport to="body">
        <Transition leave-active-class="duration-200">
            <div
                v-show="showModal"
                class="fixed inset-0 overflow-y-auto sm:px-0 z-30"
                scroll-region
            >
                <Transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div
                        v-show="showModal"
                        class="fixed inset-0 transform transition-all"
                        >
                        <!-- @click="onClose" -->
                        <div class="absolute inset-0 bg-gray-300 dark:bg-slate-900 opacity-90 backdrop-blur-lg" />
                    </div>
                </Transition>

                <Transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-full"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 translate-y-1/2"
                >
                    <div
                        v-show="showModal"
                        class="bg-white dark:bg-slate-900 rounded-lg overflow-hidden shadow-xl transform transition-all w-full h-[94vh] absolute bottom-0"
                    >
                        <div class="relative w-full h-full">
                            <div
                                v-if="$slots.header"
                                class="flex items-center justify-between flex-shrink-0 text-xl font-bold capitalize rounded-t-lg dark:text-gray-300 border-b dark:border-slate-700 px-8 py-3 sticky top-0 z-40 bg-white/90 dark:bg-slate-800/90 backdrop-blur-sm"
                            >
                                <slot name="header" />
                                <button
                                    class="text-primary-600 rounded-md text-2xl cursor-pointer p-1 dark:text-slate-300 z-20 absolute right-4 top-4"
                                    data-dismiss="modal-drawer"
                                    @click="onClose"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>
                            <div class="px-8 h-full">
                                <slot v-if="showModal" />
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>

<script>
export default {
    name: 'AppBottomSheet',
    inheritAttrs: false,
    // components: {
    //     AppIcon
    // },
    props: {
        target: {
            type: String,
            default: null
        },
        showModal: {
            type: Boolean,
            default: false
        },
        onClose: {
            type: Function
        }
    },
    computed() {
        // console.log('modal called')
        let id = document.querySelectorAll('div[data-id]');
        console.log('dome id', id);
    },
    // mounted() {
    //     console.log('mounted');
    // }
};
</script>
