<template>
  <app-layout title="Package">
    <!-- <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          搜索结果:
        </h2>
      </div>
    </template> -->

    <div class="max-w-3xl mt-1 mx-auto bg-white overflow-hidden">
      <section class="flex items-center justify-between border-b py-4">
        <div class="text-lg font-bold">{{ package ? package.total : 0 }}条结果</div>
        <div>
          <select
            @change="filter"
            aria-label="package sort"
            v-model="query"
            class="h-11 rounded border-gray-300 shadow-sm lg:h-9 lg:text-sm sm:w-44 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
          >
            <option disabled value="">请选择排序方法</option>
            <option :value="{ sort: 'match', order: 'desc' }">最匹配</option>
            <option :value="{ sort: 'stars', order: 'desc' }">最多收藏</option>
            <option :value="{ sort: 'stars', order: 'asc' }">最少收藏</option>
            <option :value="{ sort: 'clones', order: 'desc' }">最多克隆</option>
            <option :value="{ sort: 'clones', order: 'asc' }">最少克隆</option>
            <option :value="{ sort: 'updated', order: 'desc' }">最近更新</option>
            <option :value="{ sort: 'updated', order: 'asc' }">最少更新</option>
          </select>
        </div>
      </section>
      <!-- <section
        class="
          flex flex-col
          space-y-4
          bg-white
          shadow
          sm:rounded
          lg:flex-row lg:items-center lg:justify-between lg:space-y-0 lg:p-2
        "
      >
        <div
          class="flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-2"
        >
          <select
            aria-label="package date"
            id="date"
            v-model="query.month"
            class="
              pr-10
              pl-3
              w-full
              h-11
              rounded
              border-gray-300
              shadow-sm
              lg:h-9 lg:text-sm
              sm:w-44
              focus:outline-none focus:ring-blue-500 focus:border-blue-500
            "
          >
            <option
              v-for="(month, index) in allMonths"
              :value="month.value"
              :key="index"
            >
              {{ month.label }}
            </option>
          </select>
        </div>
      </section> -->

      <section v-if="package" class="mt-2">
        <search-item
          class="mb-4"
          v-for="p in package.data"
          v-bind:key="p.id"
          :package="p"
        ></search-item>
      </section>
      <section v-if="!package" class="pt-4">请输入查询关键词</section>

      <section
        v-if="package"
        class="flex flex-col mt-8 mb-4 lg:flex-row lg:justify-between"
      >
        <pagination :pagination="package"></pagination>
      </section>
    </div>
  </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { pickBy } from "lodash";
import Pagination from "@/Components/Pagination";
import SearchItem from "./SearchItem.vue";

export default defineComponent({
  props: {
    package: Object,
    queryParams: Object,
  },
  components: {
    AppLayout,
    Link,
    SearchItem,
    Pagination,
  },
  data() {
    return {
      query: {
        sort: this.queryParams.sort,
        order: this.queryParams.order,
      },
    };
  },
  methods: {
    filter() {
      this.query.q = this.queryParams.q;
      this.$inertia.get(route("search"), this.query, {
        preserveState: true,
      });
    },
  },
});
</script>
