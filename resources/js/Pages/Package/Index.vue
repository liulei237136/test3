<template>
  <app-layout title="Package">
    <template #header>
      <div class="flex items-center space-x-2">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          点读包列表
        </h2>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <section
            class="
              flex flex-col
              p-4
              mb-4
              space-y-4
              bg-white
              shadow
              sm:rounded
              lg:flex-row
              lg:items-center
              lg:justify-between
              lg:space-y-0
              lg:p-2
            "
          >
            <div
              class="
                flex flex-col
                space-y-4
                sm:flex-row
                sm:space-y-0 sm:space-x-2
              "
            >
              <select
                aria-label="package category"
                id="category"
                v-model="query.category"
                class="
                  pr-10
                  pl-3
                  w-full
                  h-11
                  rounded
                  border-gray-300
                  shadow-sm
                  lg:h-9
                  lg:text-sm
                  sm:w-44
                  focus:outline-none
                  focus:ring-blue-500
                  focus:border-blue-500
                "
              >
                <option
                  v-for="(category, index) in allCategories"
                  :value="category.value"
                  :key="index"
                >
                  {{ category.label }}
                </option>
              </select>

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
                  lg:h-9
                  lg:text-sm
                  sm:w-44
                  focus:outline-none
                  focus:ring-blue-500
                  focus:border-blue-500
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

              <!-- <button
                type="button"
                @click="filter()"
                class="
                  inline-flex
                  items-center
                  px-4
                  h-11
                  font-medium
                  text-gray-700
                  bg-white
                  rounded
                  border border-gray-300
                  shadow-sm
                  lg:h-9
                  lg:text-sm
                  hover:bg-gray-50
                  focus:outline-none
                  focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                "
              >
                过滤
              </button> -->
            </div>

            <div class="flex flex-col">
              <label
                for="search"
                class="text-sm font-medium text-gray-700 sr-only"
                >Search</label
              >
              <input
                v-model="query.term"
                @keydown.enter="filter()"
                type="search"
                id="search"
                class="
                  w-full
                  h-11
                  rounded
                  border-gray-300
                  shadow-sm
                  lg:h-9
                  lg:text-sm
                  lg:w-64
                  focus:ring-blue-500
                  focus:border-blue-500
                "
                placeholder="Search for..."
                autocomplete="off"
              />
            </div>
          </section>

          <section class="mb-4">
            <table class="min-w-full bg-white shadow table-fixed sm:rounded">
              <tbody class="divide-y divide-gray-100">
                <tr
                  class="align-top group"
                  v-for="(item, index) in package.data"
                  :key="item.id"
                >
                  <td class="p-2 text-left">
                    <div class="flex space-x-4">
                      <div>
                        <Link
                          href="#"
                          class="
                            text-sm
                            font-semibold
                            text-blue-600
                            break-all
                            rounded
                            focus:outline-none
                            focus:ring-2 focus:ring-blue-500
                          "
                        >
                          {{ item.title }}
                        </Link>
                        <!-- <p class="text-xs text-gray-500 break-all">
                          {{ item.file_name }}
                        </p> -->
                      </div>
                    </div>
                  </td>
                  <td class="hidden p-2 text-left lg:table-cell">
                    <a
                      href="#"
                      class="
                        text-blue-600
                        rounded
                        lg:text-sm
                        focus:outline-none
                        focus:ring-2 focus:ring-blue-500
                      "
                    >
                      {{ item.author.name }}
                    </a>
                  </td>
                  <td class="hidden p-2 text-left lg:table-cell">
                    <span class="text-gray-600 lg:text-sm">{{
                      item.created_at
                    }}</span>
                  </td>
                </tr>

                <tr class="align-top" v-if="!package.data.length">
                  <td colspan="4" class="p-2 text-sm text-gray-700">
                    No package files found.
                  </td>
                </tr>
              </tbody>
            </table>
          </section>

          <section class="flex flex-col mb-4 lg:flex-row lg:justify-between">
            <pagination :pagination="package.meta"></pagination>
          </section>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { pickBy } from "lodash";
import Pagination from "@/Components/Pagination";

export default defineComponent({
  props: {
    package: Object,
    categories: Array,
    months: Array,
    queryParams: Object,
  },
  components: {
    AppLayout,
    Link,
    Pagination,
  },
  data() {
    return {
      query: {
        category: this.queryParams.category,
        month: this.queryParams.month,
        term: this.queryParams.term,
      },
    };
  },
  methods: {
    filter() {
      this.$inertia.get(route("package.index"), pickBy(this.query), {
        preserveState: true,
      });
    },
  },
  computed: {
    allCategories() {
      return [{ value: null, label: "所有点读笔" }, ...this.categories];
    },
    allMonths() {
      return [{ value: null, label: "所有时间" }, ...this.months];
    },
  },
  watch: {
      'query.category': function(){
          this.filter();
      },
      'query.month': function(){
          this.filter();
      }
  }
});
</script>
