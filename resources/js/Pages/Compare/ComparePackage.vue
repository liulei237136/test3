<template>
  <content-layout>
    <div>
      <h1 class="text-xl mb-2">比较改变</h1>
      <div class="mb-2">
        <package-link as="button" :package="parent"></package-link>
        <i class="fas fa-arrow-left text-gray-500 mx-4 text-sm"></i>
        <package-link as="button" :package="child"></package-link>
        <span v-if="diff.pass === true">这个求拉可以自动融合</span>
      </div>
      <div>
        <Link as="button" type="button" href="#">创建拉取</Link>
      </div>
      <div class="flex justify-around">
        <div>{{ commits.length }}个保存</div>
        <div>1row delete</div>
        <div>1row insert</div>
        <div>1贡献者</div>
      </div>
      <div>
        <h3>commits</h3>
        <ul v-if="diff.pass === true" class="border border-2 rounded-md">
          <li v-for="commit in diff.commits" :key="commit.id">
            <h3>{{ commit.title }}</h3>
            <div>{{ commit.author.name }} 保存于 {{ commit.created_at }}</div>
          </li>
        </ul>
      </div>
    </div>
  </content-layout>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";
import { defineComponent, nextTick, onMounted, reactive, ref } from "vue";
import ContentLayout from "@/Layouts/ContentLayout.vue";
import PackageLink from "@/Components/PackageLink.vue";

export default defineComponent({
  props: {
    parent: Object,
    child: Object,
    package: Object,
    canEdit: Boolean,
    diff: Object,
  },
  components: {
    Link,
    ContentLayout,
    PackageLink,
  },
  setup(props, context) {
    return {};
  },
});
</script>
