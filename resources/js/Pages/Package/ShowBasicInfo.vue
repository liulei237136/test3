<template>
  <content-layout>
    <div class="space-y-5">
      <h1>
        包名: &nbsp;&nbsp;{{ package.title
        }}<span class="ml-2 text-gray-500 text-sm">{{
          package.private ? "(私有)" : ""
        }}</span>
      </h1>

      <div>作者: &nbsp;&nbsp;{{ package.author.name }}</div>
      <div>描述:</div>
      <p>
        {{ package.description }}
      </p>
    </div>
  </content-layout>
</template>

<script>
import ContentLayout from "@/Layouts/ContentLayout.vue";
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    package: Object,
  },
  components: {
    ContentLayout,
  },
});
</script>
