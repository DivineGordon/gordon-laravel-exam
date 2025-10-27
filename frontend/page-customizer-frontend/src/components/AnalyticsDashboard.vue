<template>
  <div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold">Analytics Dashboard</h2>
      <div class="flex gap-4">
        <select
          v-model="selectedPeriod"
          @change="loadAnalytics"
          class="px-4 py-2 border border-gray-300 rounded-md"
        >
          <option value="daily">Daily</option>
          <option value="weekly">Weekly</option>
          <option value="monthly">Monthly</option>
        </select>
        <button
          @click="exportData"
          class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600"
        >
          Export CSV
        </button>
      </div>
    </div>

    <div v-if="!analyticsStore.loading && analyticsStore.analytics">
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-blue-50 p-6 rounded-lg">
          <h3 class="text-sm font-medium text-gray-600 mb-2">Total Views</h3>
          <p class="text-3xl font-bold text-blue-600">
            {{ analyticsStore.analytics.total_views }}
          </p>
        </div>

        <div class="bg-green-50 p-6 rounded-lg">
          <h3 class="text-sm font-medium text-gray-600 mb-2">Unique Visitors</h3>
          <p class="text-3xl font-bold text-green-600">
            {{ analyticsStore.analytics.unique_visitors }}
          </p>
        </div>

        <div class="bg-purple-50 p-6 rounded-lg">
          <h3 class="text-sm font-medium text-gray-600 mb-2">Returning Visitors</h3>
          <p class="text-3xl font-bold text-purple-600">
            {{ analyticsStore.analytics.returning_visitors }}
          </p>
        </div>
      </div>

      <!-- Chart -->
      <div class="mb-8">
        <h3 class="text-lg font-semibold mb-4">Views Over Time</h3>
        <div class="border rounded-lg p-4">
          <canvas ref="chartCanvas"></canvas>
        </div>
      </div>

      <!-- Views Table -->
      <div>
        <h3 class="text-lg font-semibold mb-4">Detailed View Data</h3>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                  Date
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                  Views
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="item in analyticsStore.analytics.views_by_date" :key="item.date">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ item.date }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ item.views }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div v-else-if="analyticsStore.loading" class="text-center py-12">
      <p class="text-gray-500">Loading analytics...</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, nextTick } from 'vue';
import { useAnalyticsStore } from '@/stores/analytics';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const analyticsStore = useAnalyticsStore();
const selectedPeriod = ref('daily');
const chartCanvas = ref<HTMLCanvasElement | null>(null);
let chartInstance: Chart | null = null;

onMounted(async () => {
  await loadAnalytics();
});

watch(
  () => analyticsStore.analytics,
  async () => {
    await nextTick();
    renderChart();
  }
);

async function loadAnalytics() {
  await analyticsStore.fetchAnalytics(selectedPeriod.value);
}

function renderChart() {
  if (!chartCanvas.value || !analyticsStore.analytics) return;

  // Destroy existing chart
  if (chartInstance) {
    chartInstance.destroy();
  }

  const ctx = chartCanvas.value.getContext('2d');
  if (!ctx) return;

  const data = analyticsStore.analytics.views_by_date || [];

  chartInstance = new Chart(ctx, {
    type: 'line',
    data: {
      labels: data.map((item: any) => item.date),
      datasets: [
        {
          label: 'Page Views',
          data: data.map((item: any) => item.views),
          borderColor: 'rgb(59, 130, 246)',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          tension: 0.4,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: true,
          position: 'top',
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1,
          },
        },
      },
    },
  });
}

async function exportData() {
  await analyticsStore.exportCSV();
}
</script>