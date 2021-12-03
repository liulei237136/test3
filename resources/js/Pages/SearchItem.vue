<template>
  <div>
    <header class="mb-1 text-lg font-bold text-blue-600">
      <h2 class="truncate">
        <Link :href="route('package.info', { package: package.id })">{{
          package.name
        }}</Link>
      </h2>
    </header>
    <p :title="package.description" class="truncate text-sm text-gray-500">
      {{ package.description }}
    </p>
    <footer>
      <div class="flex items-center space-x-4 mt-2">
        <div>
          <a href="#">
            <img
              class="rounded-full"
              :src="package.author.profile_photo_url"
              :alt="package.author.name"
              width="32"
              height="32"
          /></a>
        </div>
        <div class="text-sm flex space-x-2">
          <span class="text-gray-500 ">由 </span>
          <a class="" href="#" style="outline: currentcolor none medium">{{
            package.author.name
          }}</a>
          <span class="text-gray-500 ">创立于 </span>
          <span class="ml-2 text-gray-500">{{ package.updated_at.substr(0,10) }}</span>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import { defineComponent } from "vue";
import { Link } from "@inertiajs/inertia-vue3";

export default defineComponent({
  props: {
    package: Object,
  },
  components: {
    Link,
  },
});
</script>
