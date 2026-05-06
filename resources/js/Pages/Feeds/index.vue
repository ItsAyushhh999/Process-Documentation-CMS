<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FeedsCard from './FeedsCard.vue';
import axios from 'axios';
import pusher from '@/utils/pusher.js'; // Ensure you have a pusher.js utility set up
const feedsList = ref([]);
const currentPage = ref(1);
const isLoading = ref(false);
const containerRef = ref(null);
const { user } = usePage().props.auth;

const fetchFeeds = async (isInitial = false) => {
    if (isLoading.value ) return;

    isLoading.value = true;

    try {
        let page = isInitial ? 1 : currentPage.value + 1;
        let data = [];

        while (data.length === 0) {
            const response = await axios.get(`/feeds/show?page=${page}`);
            data = response.data;

            if (!Array.isArray(data)) {
                console.error('Unexpected data format:', data);
                break;
            }

            if (data.length === 0) {
                page += 1;
            }
        }

        if (data.length > 0) {
            if (isInitial) {
                feedsList.value = data;
            } else {
                feedsList.value.push(...data);
            }
            currentPage.value = page;
        }
    } catch (error) {
        console.error('Failed to fetch feeds:', error);
    } finally {
        isLoading.value = false;
    }
};


const handleScroll = () => {
    if (!containerRef.value) return;

    const scrollTop = containerRef.value.scrollTop;
    const containerHeight = containerRef.value.clientHeight;
    const scrollHeight = containerRef.value.scrollHeight;

    const buffer = 500;

    if (scrollTop + containerHeight >= scrollHeight - buffer && !isLoading.value) {
        fetchFeeds();
    }
};

const channel = pusher.subscribe(`feed.${user.id}`);


channel.bind('my-event', (data) => {
    feedsList.value.unshift(data.feed);
});



onMounted(() => {
    fetchFeeds(true);
    if (containerRef.value) {
        containerRef.value.addEventListener('scroll', handleScroll);
    }
});

onUnmounted(() => {
    if (containerRef.value) {
        containerRef.value.removeEventListener('scroll', handleScroll);
    }
    pusher.unsubscribe('feed');
});

</script>

<template>
    <Head title="Feeds" />
    <AuthenticatedLayout>
        <div class="feeds-container" ref="containerRef">
            <div v-for="(item, index) in feedsList" :key="index" class="feed-item">
                <FeedsCard :data="item"/>
            </div>
            <div v-if="isLoading" class="loading">Loading...</div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.feeds-container {
    height: 80vh;
    overflow-y: scroll;
    overflow-x: hidden;

    /* Hide scrollbar for Chrome, Safari and Opera */
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
}

/* Hide scrollbar for Chrome, Safari, and Opera */
.feeds-container::-webkit-scrollbar {
    display: none;
}


.feed-item {
    margin-bottom: 10px;
}

.loading {
    text-align: center;
    padding: 20px;
}
</style>
