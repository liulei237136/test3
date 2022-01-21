<template>
  <content-layout>
    <div v-if="package.commits.length">
      点读包{{ package.title }}中{{ package.author.name }}的保存
      <ul class="mt-2">
        <li v-for="commit in package.commits" :key="commit.id">
          <commit-list-item :commit="commit" :p="package"></commit-list-item>
        </li>
      </ul>
    </div>
    <div v-else>没有保存</div>
  </content-layout>
</template>

<script>
import ContentLayout from "@/Layouts/ContentLayout.vue";
import { defineComponent } from "vue";
import CommitListItem from "./CommitListItem.vue";

export default defineComponent({
  props: {
    package: Object,
  },
  components: {
    ContentLayout,
    CommitListItem,
  },
});
</script>
