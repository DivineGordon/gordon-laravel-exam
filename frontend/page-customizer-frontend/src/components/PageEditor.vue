<template>
  <div v-if="!loading" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Editor Panel -->
    <div class="bg-white rounded-lg shadow-md p-6">
      <h2 class="text-2xl font-bold mb-6">Edit Your Page</h2>

      <!-- Content Fields -->
      <div class="space-y-4 mb-6">
        <div>
          <label class="block text-sm font-medium mb-2">Hero Title</label>
          <input
            v-model="editableContent.hero_title"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
          />
        </div>

        <div>
          <label class="block text-sm font-medium mb-2">Hero Subtitle</label>
          <input
            v-model="editableContent.hero_subtitle"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
          />
        </div>

        <div>
          <label class="block text-sm font-medium mb-2">About Title</label>
          <input
            v-model="editableContent.about_title"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
          />
        </div>

        <div>
          <label class="block text-sm font-medium mb-2">About Text</label>
          <textarea
            v-model="editableContent.about_text"
            rows="4"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
          ></textarea>
        </div>

        <div>
          <label class="block text-sm font-medium mb-2">Contact Title</label>
          <input
            v-model="editableContent.contact_title"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
          />
        </div>

        <div>
          <label class="block text-sm font-medium mb-2">Contact Text</label>
          <textarea
            v-model="editableContent.contact_text"
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
          ></textarea>
        </div>
      </div>

      <!-- Theme Selection -->
      <div class="mb-6">
        <label class="block text-sm font-medium mb-2">Select Theme</label>
        <div class="grid grid-cols-2 gap-3">
          <button
            v-for="theme in pageStore.themes"
            :key="theme.id"
            @click="selectTheme(theme.id)"
            :class="[
              'p-4 rounded-md border-2 transition-all',
              selectedThemeId === theme.id
                ? 'border-blue-500 bg-blue-50'
                : 'border-gray-200 hover:border-gray-300'
            ]"
          >
            <div class="flex items-center gap-2 mb-2">
              <div
                class="w-6 h-6 rounded"
                :style="{ backgroundColor: theme.primary_color }"
              ></div>
              <div
                class="w-6 h-6 rounded"
                :style="{ backgroundColor: theme.secondary_color }"
              ></div>
              <div
                class="w-6 h-6 rounded"
                :style="{ backgroundColor: theme.accent_color }"
              ></div>
            </div>
            <p class="text-sm font-medium">{{ theme.name }}</p>
          </button>
        </div>
      </div>

      <!-- Image Uploads -->
      <div class="space-y-4 mb-6">
        <div>
          <label class="block text-sm font-medium mb-2">Logo</label>
          <input
            type="file"
            accept="image/*"
            @change="handleLogoUpload"
            class="w-full"
          />
        </div>

        <div>
          <label class="block text-sm font-medium mb-2">Background Image</label>
          <input
            type="file"
            accept="image/*"
            @change="handleBackgroundUpload"
            class="w-full"
          />
        </div>
      </div>

      <!-- Actions -->
      <div class="flex gap-4">
        <button
          @click="saveChanges"
          :disabled="saving"
          class="flex-1 bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 disabled:opacity-50"
        >
          {{ saving ? 'Saving...' : 'Save Changes' }}
        </button>

        <button
          @click="togglePublish"
          :class="[
            'flex-1 py-2 px-4 rounded-md',
            pageStore.page?.is_published
              ? 'bg-yellow-500 hover:bg-yellow-600'
              : 'bg-green-500 hover:bg-green-600',
            'text-white'
          ]"
        >
          {{ pageStore.page?.is_published ? 'Unpublish' : 'Publish' }}
        </button>
      </div>

      <!-- Public Link -->
      <div v-if="pageStore.page?.is_published" class="mt-4 p-4 bg-green-50 rounded-md">
        <p class="text-sm font-medium mb-2">Your page is live!</p>
        <a
          :href="publicUrl"
          target="_blank"
          class="text-blue-500 hover:underline text-sm break-all"
        >
          {{ publicUrl }}
        </a>
      </div>
    </div>

    <!-- Preview Panel -->
    <div class="bg-white rounded-lg shadow-md p-6">
      <h2 class="text-2xl font-bold mb-6">Preview</h2>
      <PagePreview :content="editableContent" :theme="selectedTheme" />
    </div>
  </div>

  <div v-else class="text-center py-12">
    <p class="text-gray-500">Loading...</p>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { usePageStore } from '@/stores/page';
import PagePreview from './PagePreview.vue';

const pageStore = usePageStore();

const loading = ref(true);
const saving = ref(false);
const editableContent = ref<any>({});
const selectedThemeId = ref<number | null>(null);

const selectedTheme = computed(() => {
  return pageStore.themes.find((t: any) => t.id === selectedThemeId.value);
});

const publicUrl = computed(() => {
  if (!pageStore.page) return '';
  return `http://localhost:8000/api/pages/${pageStore.page.slug}`;
});

onMounted(async () => {
  await Promise.all([pageStore.fetchPage(), pageStore.fetchThemes()]);
  
  if (pageStore.page) {
    editableContent.value = { ...pageStore.page.content };
    selectedThemeId.value = pageStore.page.theme_id;
  }
  
  loading.value = false;
});

function selectTheme(themeId: number) {
  selectedThemeId.value = themeId;
}

async function saveChanges() {
  saving.value = true;
  try {
    await pageStore.updateContent(editableContent.value);
    if (selectedThemeId.value !== pageStore.page?.theme_id) {
      await pageStore.updateTheme(selectedThemeId.value!);
    }
    alert('Changes saved successfully!');
  } catch (error) {
    alert('Failed to save changes');
  } finally {
    saving.value = false;
  }
}

async function togglePublish() {
  const newStatus = !pageStore.page?.is_published;
  await pageStore.publish(newStatus);
  alert(newStatus ? 'Page published!' : 'Page unpublished');
}

async function handleLogoUpload(event: Event) {
  const file = (event.target as HTMLInputElement).files?.[0];
  if (!file) return;

  try {
    await pageStore.uploadLogo(file);
    alert('Logo uploaded successfully!');
  } catch (error) {
    alert('Failed to upload logo');
  }
}

async function handleBackgroundUpload(event: Event) {
  const file = (event.target as HTMLInputElement).files?.[0];
  if (!file) return;

  try {
    await pageStore.uploadBackground(file);
    alert('Background uploaded successfully!');
  } catch (error) {
    alert('Failed to upload background');
  }
}
</script>