<template>
  <content-layout>
    <div>
      <h1>比较改变</h1>
      <div>
        <package-link as="button" :package="parent"></package-link>
        <i class="fas fa-arrow-left text-gray-500 mx-4 text-sm"></i>
        <package-link as="button" :package="child"></package-link>
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
