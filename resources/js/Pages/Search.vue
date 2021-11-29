<template>
  <app-layout title="Package">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
         搜索结果:
        </h2>
      </div>
    </template>

    <div class="px-4 bg-white overflow-hidden">
      <section
        class="
          flex flex-col
          p-4
          mb-4
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
          <!-- <select
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
          </select> -->
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
                      :href="route('package.info', { package: item.id })"
                      class="
                        text-sm
                        font-semibold
                        text-blue-600
                        break-all
                        rounded
                        focus:outline-none focus:ring-2 focus:ring-blue-500
                      "
                    >
                      {{ item.name }}
                    </Link>
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
                    focus:outline-none focus:ring-2 focus:ring-blue-500
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

      <!-- <section class="flex flex-col mb-4 lg:flex-row lg:justify-between">
        <pagination :pagination="package"></pagination>
      </section> -->
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
    query: Object,
  },
  components: {
    AppLayout,
    Link,
    Pagination,
  },
  data() {
    return {
      query: {
        term: this.query.term,
      },
    };
  },
  methods: {
    filter() {
      this.$inertia.get(route("search"), pickBy(this.query), {
        preserveState: true,
      });
    },
  },

});
</script>
