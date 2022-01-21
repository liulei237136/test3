<template>
  <div :class="classes">
    <h3>{{ commit.title }}</h3>
    <p class="text-sm">
      <Link
        href="#"
        class="hover:underline"
        :title="`查看${commit.author.name}的所有保存`"
        >{{ commit.author.name }}</Link
      >
      <span class="ml-4 text-gray-500"
        >创建于 {{ new Date(commit.created_at).toLocaleString() }}</span
      >
    </p>
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
    commit: Object,
    classes: String,
  },
});
</script>
