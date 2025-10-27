<template>
  <div
    class="border rounded-lg overflow-hidden"
    :style="{
      backgroundColor: theme?.background_color || '#ffffff',
      color: theme?.text_color || '#000000'
    }"
  >
    <!-- Hero Section -->
    <div
      class="p-8 text-center"
      :style="{ backgroundColor: theme?.primary_color || '#1a1a1a' }"
    >
      <h1 class="text-4xl font-bold mb-4">
        {{ content.hero_title || 'Hero Title' }}
      </h1>
      <p class="text-xl">
        {{ content.hero_subtitle || 'Hero Subtitle' }}
      </p>
    </div>

    <!-- About Section -->
    <div
      class="p-8"
      :style="{ backgroundColor: theme?.secondary_color || '#8b4513' }"
    >
      <h2 class="text-2xl font-bold mb-4">
        {{ content.about_title || 'About' }}
      </h2>
      <p>{{ content.about_text || 'About text goes here...' }}</p>
    </div>

    <!-- Contact Section -->
    <div class="p-8">
      <h2
        class="text-2xl font-bold mb-4"
        :style="{ color: theme?.accent_color || '#ffffff' }"
      >
        {{ content.contact_title || 'Contact' }}
      </h2>
      <p>{{ content.contact_text || 'Contact information...' }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  content: any;
  theme: any;
}>();
</script>