<template>
  <app-layout title="Package List">
    <template #header>
      <!-- <div class="py-4 flex items-center space-x-2"> -->
      <h2 class="py-4 font-semibold text-xl text-gray-800">点读包列表</h2>
      <!-- </div> -->
    </template>
    <div class="mx-auto max-w-7xl">
      <div>
        <ul>
          <li v-for="p in package.data" :key="p.id" class="">
            <div>
              <Link :href="route('package.show', { package: p.id })">{{ p.title }}</Link>
            </div>
          </li>
        </ul>
        <div v-if="package.data.length === 0">没有找到点读包</div>
      </div>
      <pagination class="mt-6" :pagination="package.meta" />
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineComponent } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
import Pagination from "@/Components/Pagination";

export default defineComponent({
  components: {
    Link,
    AppLayout,
    Pagination,
  },

  props: {
    package: Object,
  },
});
</script>
