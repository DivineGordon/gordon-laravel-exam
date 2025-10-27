import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/api/axios';

export const useAnalyticsStore = defineStore('analytics', () => {
  const analytics = ref(null);
  const loading = ref(false);

  async function fetchAnalytics(period = 'daily') {
    loading.value = true;
    try {
      const response = await api.get('/analytics', { params: { period } });
      analytics.value = response.data;
    } finally {
      loading.value = false;
    }
  }

  async function exportCSV() {
    const response = await api.get('/analytics/export', {
      responseType: 'blob',
    });
    
    // Create download link
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `analytics-${new Date().toISOString().split('T')[0]}.csv`);
    document.body.appendChild(link);
    link.click();
    link.remove();
  }

  return { analytics, loading, fetchAnalytics, exportCSV };
});