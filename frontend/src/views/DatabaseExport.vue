<template>
    <div class="p-6">
        <div class="max-w-3xl mx-auto bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-bold mb-6">Database Export</h1>
            
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <h2 class="font-medium">Export complete database</h2>
                    <p class="text-sm text-gray-600">Download a SQL dump of all database tables</p>
                </div>
                <button 
                    @click="exportDatabase"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors"
                    :disabled="loading"
                >
                    <span v-if="!loading">Export Now</span>
                    <span v-else>Exporting...</span>
                </button>
            </div>
            
            <div v-if="error" class="mt-4 p-4 bg-red-50 text-red-700 rounded">
                {{ error }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const loading = ref(false);
const error = ref(null);
const router = useRouter();

const exportDatabase = async () => {
    try {
        loading.value = true;
        error.value = null;
        
        // This will trigger the file download
        window.location.href = '/database/export';
        
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to export database';
    } finally {
        loading.value = false;
    }
};
</script>