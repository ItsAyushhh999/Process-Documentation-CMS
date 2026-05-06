<script setup>
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FeedsCard from './FeedsCard.vue';
import axios from 'axios';
const feedsList = ref([]);
const isLoading = ref(true); // Set loading to true initially
// Function to fetch feeds from the API
const fetchFeeds = async () => {
    isLoading.value = true; // Start loading
    try {
        const response = await axios.get('/dailyFeeds/show');
        const data = response.data;
        console.log(data);
        // Validate data format
        if (!Array.isArray(data)) {
            console.error('Unexpected data format:', data);
            return; // Exit if the data format is not as expected
        }
        feedsList.value = data; // Set feedsList directly, assuming the response is an array
    } catch (error) {
        console.error('Failed to fetch feeds:', error);
    } finally {
        isLoading.value = false; // End loading
    }
};

setTimeout(function () {
    location.reload();
}, 600000); //1000 milli second = 1second
// Fetch feeds on component mount
onMounted(fetchFeeds);
</script>

<template>

    <AuthenticatedLayout>
        <div class="feeds-container">
            <div v-for="(item, index) in feedsList" :key="index" class="feed-item">
                <FeedsCard :data="item" />
            </div>
            <div v-if="isLoading" class="loading">Loading...</div>
            <div v-if="!isLoading && feedsList.length === 0" class="no-feeds">
                No feeds available.
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.feeds-container {
    height: 80vh;
    overflow-y: auto; /* Changed to auto for better user experience */
    overflow-x: hidden;
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
}
.feeds-container::-webkit-scrollbar {
    display: none; /* Hide scrollbar for Chrome, Safari, and Opera */
}
.feed-item {
    margin-bottom: 10px;
}
.loading {
    text-align: center;
    padding: 20px;
}
.no-feeds {
    text-align: center;
    padding: 20px;
    color: gray; /* Optional: styling for no feeds message */
}
</style>
