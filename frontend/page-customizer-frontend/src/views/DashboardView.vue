<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-md">
      <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">Page Customizer</h1>
        <div class="flex gap-4">
          <button
            @click="activeTab = 'editor'"
            :class="[
              'px-4 py-2 rounded-md',
              activeTab === 'editor' ? 'bg-blue-500 text-white' : 'bg-gray-200'
            ]"
          >
            Editor
          </button>
          <button
            @click="activeTab = 'analytics'"
            :class="[
              'px-4 py-2 rounded-md',
              activeTab === 'analytics' ? 'bg-blue-500 text-white' : 'bg-gray-200'
            ]"
          >
            Analytics
          </button>
          <button
            @click="logout"
            class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600"
          >
            Logout
          </button>
        </div>
      </div>
    </nav>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 py-8">
      <PageEditor v-if="activeTab === 'editor'" />
      <AnalyticsDashboard v-else-if="activeTab === 'analytics'" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import PageEditor from '@/components/PageEditor.vue';
import AnalyticsDashboard from '@/components/AnalyticsDashboard.vue';

const router = useRouter();
const authStore = useAuthStore();
const activeTab = ref('editor');

onMounted(async () => {
  await authStore.fetchUser();
});

async function logout() {
  await authStore.logout();
  router.push('/login');
}
</script>