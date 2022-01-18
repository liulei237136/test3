<template>
  <content-layout>
    <div class="max-w-7xl mx-auto">
      <!-- 新建拉取行 -->
      <div class="flex justify-between mb-4">
        <!-- <button class="bg-green-500 px-4 py-2 rounded" @click="onNewPull">
          新建一个拉取
        </button> -->
        <Link
          v-if="package.parent"
          as="button"
          class="bg-green-500 px-4 py-2 rounded text-white"
          :href="comparePackageUrl"
        >
          新建一个拉取</Link
        >
      </div>
      <!-- 列表头和列表体 -->
      <div class="rounded border my-bg-gray p-4">
        <!-- list header -->
        <div class="flex items-center justify-between">
          <div class="flex items-center text-gray-500">
            <!-- <div class="flex items-center mr-4"> -->
            <!-- <a href="#" class="flex items-center mr-4"> -->
            <Link
              :href="route('package.pulls', { package: this.package.id, status: 'open' })"
              class="flex items-center pulls-status mr-4"
              :class="{ active: status === 'open' }"
            >
              <Icon name="pull" class="w-5 h-5 mr-2"></Icon>
              1 Open
            </Link>
            <!-- </a> -->
            <!-- </div> -->
            <Link
              :href="
                route('package.pulls', { package: this.package.id, status: 'closed' })
              "
              class="flex items-center pulls-status"
              :class="{ active: status === 'closed' }"
            >
              <Icon name="check" class="w-5 h-5 mr-1"></Icon>
              9 Closed
            </Link>
          </div>
          <div>right</div>
        </div>
        <!-- list -->
        <ul v-if="pulls && pulls.length">
          <li v-for="pull in pulls" :key="pull.id">
            {{ pull.title }}
          </li>
        </ul>
      </div>
    </div>
  </content-layout>
</template>

<script>
import { Inertia } from "@inertiajs/inertia";
import { Link } from "@inertiajs/inertia-vue3";
import { defineComponent, nextTick, onMounted, reactive, ref } from "vue";
import axios from "axios";
import ContentLayout from "@/Layouts/ContentLayout.vue";
import Icon from "@/Components/Icon";

export default defineComponent({
  props: {
    package: Object,
    canEdit: Boolean,
    pulls: Array,
    status: String,
  },
  components: {
    Link,
    ContentLayout,
    Icon,
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
    let comparePackageUrl = "#";
    if (props.package.parent) {
      comparePackageUrl = route("compare.package", {
        parent: props.package.parent.id,
        child: props.package.id,
      });
    } else {
      comparePackageUrl = route("compare.compare", { package: props.package.id });
    }

    return {
      demo,
      onNewPull,
      comparePackageUrl,
    };
  },
});
</script>
