<template>
  <app-layout title="点读包信息">
    <template #header>
      <div class="flex items-center space-x-2">
        <Link
          :href="route('package.showInfo', {'package':package.id})"
          class="purpleButton text-xl px-4 py-2"
          :class="[
            'bg-purple-500',
            { 'bg-purple-800': currentTab === 'ShowInfo' },
          ]"
          >基本信息</Link>
        <Link
          :href="route('package.showAudio', {'package':package.id})"
          class="purpleButton text-xl px-4 py-2"
          :class="[
            'bg-purple-500',
            { 'bg-purple-800': currentTab === 'ShowAudio' },
          ]"
          >包含音频</Link>
      </div>
    </template>

    <div class="py-12">
      <div class="mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <component :is="currentTab" :package="package" :audio="audio"> </component>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import ShowInfo from "./ShowInfo.vue";
import ShowAudio from "./ShowAudio.vue";

export default defineComponent({
  props: {
    package: Object,
    tab: String,
    audio: Array,
  },
  components: {
    AppLayout,
    Link,
    ShowInfo,
    ShowAudio,
  },
  data() {
    return {
      currentTab: this.tab || "ShowInfo",
    };
  },
});
</script>
