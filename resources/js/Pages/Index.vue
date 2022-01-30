<template>
  <app-layout title="Index">
    <div class="p-4 max-w-7xl mx-auto bg-white overflow-hidden">
      <ul v-if="packages.data && packages.data.length">
        <package-list-item
          class="mb-4"
          v-for="p in packages.data"
          v-bind:key="p.id"
          :package="p"
        ></package-list-item>
        <pagination class="mt-6" :pagination="packages"></pagination>
      </ul>
    </div>
  </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import Pagination from "@/Components/Pagination";
import PackageListItem from "@/Pages/Package/PackageListItem";
export default defineComponent({
  props: {
    packages: Object,
  },
  components: {
    AppLayout,
    PackageListItem,
    Pagination,
  },
});
</script>
