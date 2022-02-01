<template>
  <user-layout>
    <div class="p-4 w-full bg-white overflow-hidden">
      <ul v-if="packages.data && packages.data.length">
        <package-list-item
          class="mb-4"
          v-for="p in packages.data"
          v-bind:key="p.id"
          :package="p"
        ></package-list-item>
        <pagination class="mt-6" :pagination="packages"></pagination>
      </ul>
      <section v-else>根据查询条件，没有点读包</section>
    </div>
  </user-layout>
</template>

<script>
import UserLayout from "@/Layouts/UserLayout.vue";
import { defineComponent } from "vue";
import PackageListItem from "@/Pages/Package/PackageListItem";
import Pagination from "@/Components/Pagination";
export default defineComponent({
  props: {
    targetUser: Object,
    packages: Object,
  },
  components: {
    UserLayout,
    PackageListItem,
    Pagination,
  },
});
</script>
