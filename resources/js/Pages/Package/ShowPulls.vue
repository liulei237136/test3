<template>
  <content-layout>
    <!-- 新建拉取行 -->
    <div class="flex justify-between">
      <button class="bg-green-500 px-4 py-2 rounded" @click="onNewPull">
        新建一个拉取
      </button>
    </div>
    <!-- 列表头和列表体 -->
    <div class="rounded border my-bg-gray" >
        <!-- list header -->
      <div>我是列表头</div>
      <!-- list -->
      <ul v-if="pulls && pulls.length">
        <li v-for="pull in pulls" :key="pull.id">
          {{ pull.title }}
        </li>
      </ul>
    </div>
  </content-layout>
</template>

<script>
import { Inertia } from "@inertiajs/inertia";
import { Link } from "@inertiajs/inertia-vue3";
import { defineComponent, nextTick, onMounted, reactive, ref } from "vue";
import axios from "axios";
import ContentLayout from "@/Layouts/ContentLayout.vue";

export default defineComponent({
  props: {
    package: Object,
    canEdit: Boolean,
    pulls: Array,
  },
  components: {
    Link,
    ContentLayout,
  },

  setup(props, context) {
    const demo = reactive({
      //   pullList: this.pulls ? this.pulls : [],
    });
    const onNewPull = () => {
      if ($page.user && props.package.parent) {
        alert("compare ready");
      } else {
        alert("not ready");
      }
      // Inertia.get(route('compare.package', {toPackage:}));
    };
    onMounted(() => {});
    return {
      demo,
      onNewPull,
    };
  },
});
</script>
