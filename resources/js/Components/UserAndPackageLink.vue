<template>
  <div class="inline-flex items-center space-x-1">
    <a href="#" class="text-blue-500 hover:underline" :class="classes">{{
      package.author.name
    }}</a>
    <div>/</div>
    <Link :href="package.url" class="text-blue-500 hover:underline" :class="classes">{{
      package.name
    }}</Link>
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
      'package': Object,
      'classes': String,
  },
});
</script>
