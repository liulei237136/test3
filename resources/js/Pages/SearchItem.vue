<template>
  <div>
    <header class="mb-1 text-lg">
      <h2 class="truncate">
        <Link :href="route('package.show', { package: package.id})">{{
          package.title
        }}</Link>
      </h2>
    </header>

    <footer>
        <div class="text-sm text-gray-500 ">
            作者:
          <a href="#" class="hover:underline">{{
            package.author.name
          }}</a>
          <span class="ml-4">创建于{{ ' ' + package.created_at.substr(0,10)}} </span>
          <span v-if="package.created_at !== package.updated_at" class="ml-4">最后更新于{{ ' ' + package.updated_at.substr(0,10) + ' ' +  package.updated_at.substr(11,8) }}</span>
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
