<template>
  <app-layout title="编辑点读包的音频">
    <template #header>
      <div class="flex items-center space-x-2">
        <Link
          :href="route('package.editInfo', {'package':package.id})"
          class="purpleButton"
          :class="[
            'bg-purple-500',
            { 'bg-purple-800': currentTab === 'EditPackageInfo' },
          ]"
          >编辑源信息</Link>
        <Link
          :href="route('package.editAudio', {'package':package.id})"
          class="purpleButton"
          :class="[
            'bg-purple-500',
            { 'bg-purple-800': currentTab === 'EditPackageAudio' },
          ]"
          >编辑音频</Link>
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
import EditPackageInfo from "./EditPackageInfo.vue";
import EditPackageAudio from "./EditPackageAudio.vue";

export default defineComponent({
  props: {
    package: Object,
    tab: String,
    audio: Array,
  },
  components: {
    AppLayout,
    Link,
    EditPackageInfo,
    EditPackageAudio,
  },
  data() {
    return {
        // p: this.package,
        // a: this.audio,
      currentTab: this.tab || "EditPackageInfo",
    };
  },
});
</script>
