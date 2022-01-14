<template>
  <div class="inline-flex items-center space-x-1 text-blue-500" :class="classes">
    <Link :as="as ? as : 'a'" href="#" class="hover:underline">{{
      package.author.name
    }}</Link>
    <div class="text-black">/</div>
    <Link
      :as="as ? as : 'a'"
      :href="route('package.show', { package: package.id })"
      class="hover:underline"
      >{{ package.title }}</Link
    >
  </div>
</template>

<script>
import { defineComponent } from "vue";
import { Link } from "@inertiajs/inertia-vue3";

export default defineComponent({
  components: {
    Link,
  },
  props: {
    package: Object,
    classes: String,
    as: String,
  },
});
</script>
