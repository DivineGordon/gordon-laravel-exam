import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '@/api/axios';

export const usePageStore = defineStore('page', () => {
  const page = ref(null);
  const themes = ref([]);

  async function fetchPage() {
    const response = await api.get('/my-page');
    page.value = response.data;
  }

  async function updateContent(content: any) {
    const response = await api.put('/my-page', { content });
    page.value = response.data;
  }

  async function updateTheme(theme_id: number) {
    const response = await api.put('/my-page', { theme_id });
    page.value = response.data;
  }

  async function publish(is_published: boolean) {
    const response = await api.put('/my-page', { is_published });
    page.value = response.data;
  }

  async function uploadLogo(file: File) {
        const formData = new FormData();
      
      formData.append('logo', file);
    const response = await api.post('/my-page/logo', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    await fetchPage(); // Refresh page data
    return response.data;
  }

  async function uploadBackground(file: File) {
    const formData = new FormData();
    formData.append('background', file);
    const response = await api.post('/my-page/background', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    await fetchPage(); // Refresh page data
    return response.data;
  }

  async function fetchThemes() {
    const response = await api.get('/themes');
    themes.value = response.data;
  }

  return {
    page,
    themes,
    fetchPage,
    updateContent,
    updateTheme,
    publish,
    uploadLogo,
    uploadBackground,
    fetchThemes,
  };
});