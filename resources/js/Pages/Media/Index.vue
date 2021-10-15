<template>
  <app-layout title="Media">
    <template #header>
      <div class="flex items-center space-x-2">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Media Lib
        </h2>

        <uppy-trigger v-on:closed="alert('closed')"></uppy-trigger>
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
                aria-label="Media type"
                id="type"
                v-model="query.fileType"
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
                <option v-for="(type,index) in allFileTypes" :value="type.value" :key="index">
                  {{ type.label }}
                </option>
              </select>

              <select
                aria-label="Media date"
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
                <option v-for="(month,index) in allMonths" :value="month.value" :key="index">
                  {{ month.label }}
                </option>
              </select>

              <button
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
                Filter
              </button>
            </div>

            <div class="flex flex-col">
              <label
                for="search"
                class="text-sm font-medium text-gray-700 sr-only"
                >Search</label
              >
              <input
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
              />
            </div>
          </section>

          <section class="flex flex-col mb-4 lg:flex-row lg:justify-between">
            <div class="hidden space-x-2 lg:flex">
              <select
                aria-label="Action"
                name="action"
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
                  sm:w-48
                  focus:outline-none
                  focus:ring-blue-500
                  focus:border-blue-500
                "
              >
                <option selected value="">Bulk actions</option>
                <option value="delete">Delete permanently</option>
              </select>

              <button
                type="button"
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
                Apply
              </button>
            </div>

            <div class="inline-flex justify-center items-center">
              <div class="hidden mr-2 text-sm text-gray-600 lg:block">
                10 items
              </div>

              <div class="flex space-x-1 items-top">
                <Link
                  href="#"
                  as="span"
                  class="
                    inline-flex
                    justify-center
                    items-center
                    w-11
                    h-11
                    text-gray-700
                    bg-white
                    rounded
                    border border-gray-200
                    shadow-sm
                    outline-none
                    hover:bg-gray-50
                    lg:h-9
                    lg:w-9
                    lg:text-sm
                    focus:ring-1 focus:ring-blue-500
                    focus:border-blue-500
                  "
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4 lg:h-3 lg:w-3"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M11 19l-7-7 7-7m8 14l-7-7 7-7"
                    />
                  </svg>
                </Link>
                <Link
                  href="#"
                  as="span"
                  class="
                    inline-flex
                    justify-center
                    items-center
                    w-11
                    h-11
                    text-gray-700
                    bg-white
                    rounded
                    border border-gray-200
                    shadow-sm
                    outline-none
                    hover:bg-gray-50
                    lg:h-9
                    lg:w-9
                    focus:ring-1 focus:ring-blue-500
                    focus:border-blue-500
                  "
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4 lg:h-3 lg:w-3"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M15 19l-7-7 7-7"
                    />
                  </svg>
                </Link>

                <div
                  class="
                    flex flex-col
                    space-y-2
                    md:flex-row
                    md:space-y-0
                    md:items-center
                    md:space-x-1
                  "
                >
                  <input
                    type="text"
                    value="1"
                    class="
                      px-2
                      w-11
                      h-11
                      text-center
                      rounded
                      border border-gray-400
                      shadow-sm
                      lg:h-9
                      lg:w-9
                      lg:text-sm
                      focus:ring-blue-500
                      focus:border-blue-500
                    "
                  />
                  <div class="px-2 text-gray-600 lg:text-sm">of 2</div>
                </div>

                <Link
                  href="#"
                  as="span"
                  class="
                    inline-flex
                    justify-center
                    items-center
                    w-11
                    h-11
                    text-gray-700
                    bg-white
                    rounded
                    border border-gray-300
                    shadow-sm
                    outline-none
                    hover:bg-gray-50
                    lg:h-9
                    lg:w-9
                    focus:ring-1 focus:ring-blue-500
                    focus:border-blue-500
                  "
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4 lg:h-3 lg:w-3"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 5l7 7-7 7"
                    />
                  </svg>
                </Link>

                <Link
                  href="#"
                  as="span"
                  class="
                    inline-flex
                    justify-center
                    items-center
                    w-11
                    h-11
                    text-gray-700
                    bg-white
                    rounded
                    border border-gray-300
                    shadow-sm
                    outline-none
                    hover:bg-gray-50
                    lg:h-9
                    lg:w-9
                    focus:ring-1 focus:ring-blue-500
                    focus:border-blue-500
                  "
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4 lg:h-3 lg:w-3"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M13 5l7 7-7 7M5 5l7 7-7 7"
                    />
                  </svg>
                </Link>
              </div>
            </div>
          </section>

          <section class="mb-4">
            <table class="min-w-full bg-white shadow table-fixed sm:rounded">
              <thead>
                <tr class="border-b border-gray-200">
                  <th class="px-2 w-10 text-center">
                    <input
                      type="checkbox"
                      class="
                        w-6
                        h-6
                        text-blue-600
                        rounded
                        border-gray-300
                        lg:w-4
                        lg:h-4
                        focus:ring-blue-500
                      "
                    />
                  </th>
                  <th class="text-left">
                    <Link
                      href="#"
                      class="
                        flex
                        relative
                        z-10
                        items-center
                        p-2
                        space-x-2
                        font-normal
                        text-blue-600
                        group
                        lg:text-sm
                        focus:outline-none
                        focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500
                      "
                    >
                      File
                    </Link>
                  </th>
                  <th class="hidden w-48 text-left lg:table-cell">
                    <span
                      class="
                        inline-block
                        p-2
                        font-normal
                        text-blue-600
                        lg:text-sm
                      "
                      >Author</span
                    >
                  </th>
                  <th class="hidden w-28 text-left lg:table-cell">
                    <Link
                      href="#"
                      class="
                        flex
                        relative
                        z-10
                        items-center
                        p-2
                        space-x-2
                        font-normal
                        text-blue-600
                        group
                        lg:text-sm
                        focus:outline-none
                        focus:ring-2 focus:ring-opacity-50 focus:ring-blue-500
                      "
                    >
                      Date
                    </Link>
                  </th>
                </tr>
              </thead>

              <tbody class="divide-y divide-gray-100">
                <tr
                  class="align-top group"
                  v-for="(item, index) in media.data"
                  :key="item.id"
                >
                  <td class="p-2 w-10 text-center">
                    <input
                      type="checkbox"
                      class="
                        w-6
                        h-6
                        text-blue-600
                        rounded
                        border-gray-300
                        lg:w-4
                        lg:h-4
                        focus:ring-blue-500
                      "
                    />
                  </td>
                  <td class="p-2 text-left">
                    <div class="flex space-x-4">
                      <div
                        class="
                          overflow-hidden
                          flex-shrink-0
                          w-12
                          rounded
                          lg:w-16
                        "
                      >
                        <img :src="item.preview_url" class="object-cover" />
                      </div>
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
                          {{ item.name }}
                        </Link>
                        <p class="text-xs text-gray-500 break-all">
                          {{ item.file_name }}
                        </p>

                        <div
                          class="
                            flex
                            items-center
                            mt-2
                            space-x-2
                            lg:invisible
                            group-hover:visible
                          "
                        >
                          <Link
                            href="#"
                            class="
                              text-xs text-blue-600
                              rounded
                              hover:text-blue-700
                              focus:outline-none
                              focus:ring-2 focus:ring-blue-500
                            "
                          >
                            Edit
                          </Link>
                          <span class="text-xs text-gray-300">|</span>
                          <button
                            class="
                              text-xs text-red-600
                              rounded
                              hover:text-red-700
                              focus:outline-none
                              focus:ring-2 focus:ring-blue-500
                            "
                          >
                            Delete
                          </button>
                          <span class="text-xs text-gray-300">|</span>
                          <a
                            :href="item.url"
                            class="
                              text-xs text-blue-600
                              rounded
                              hover:text-blue-700
                              focus:outline-none
                              focus:ring-2 focus:ring-blue-500
                            "
                          >
                            View
                          </a>
                        </div>
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

                <tr class="align-top" v-if="!media.data.length">
                  <td colspan="4" class="p-2 text-sm text-gray-700">
                    No media files found.
                  </td>
                </tr>
              </tbody>
            </table>
          </section>

          <section class="flex flex-col mb-4 lg:flex-row lg:justify-between">
            <div class="hidden space-x-2 lg:flex">
              <select
                aria-label="Action"
                name="action"
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
                  sm:w-48
                  focus:outline-none
                  focus:ring-blue-500
                  focus:border-blue-500
                "
              >
                <option selected value="">Bulk actions</option>
                <option value="delete">Delete permanently</option>
              </select>

              <button
                type="button"
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
                Apply
              </button>
            </div>

            <div class="inline-flex justify-center items-center">
              <div class="hidden mr-2 text-sm text-gray-600 lg:block">
                10 items
              </div>

              <div class="flex space-x-1 items-top">
                <Link
                  href="#"
                  as="span"
                  class="
                    inline-flex
                    justify-center
                    items-center
                    w-11
                    h-11
                    text-gray-700
                    bg-white
                    rounded
                    border border-gray-200
                    shadow-sm
                    outline-none
                    hover:bg-gray-50
                    lg:h-9
                    lg:w-9
                    lg:text-sm
                    focus:ring-1 focus:ring-blue-500
                    focus:border-blue-500
                  "
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4 lg:h-3 lg:w-3"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M11 19l-7-7 7-7m8 14l-7-7 7-7"
                    />
                  </svg>
                </Link>
                <Link
                  href="#"
                  as="span"
                  class="
                    inline-flex
                    justify-center
                    items-center
                    w-11
                    h-11
                    text-gray-700
                    bg-white
                    rounded
                    border border-gray-200
                    shadow-sm
                    outline-none
                    hover:bg-gray-50
                    lg:h-9
                    lg:w-9
                    focus:ring-1 focus:ring-blue-500
                    focus:border-blue-500
                  "
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4 lg:h-3 lg:w-3"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M15 19l-7-7 7-7"
                    />
                  </svg>
                </Link>

                <div
                  class="
                    flex flex-col
                    space-y-2
                    md:flex-row
                    md:space-y-0
                    md:items-center
                    md:space-x-1
                  "
                >
                  <input
                    type="text"
                    value="1"
                    class="
                      px-2
                      w-11
                      h-11
                      text-center
                      rounded
                      border border-gray-400
                      shadow-sm
                      lg:h-9
                      lg:w-9
                      lg:text-sm
                      focus:ring-blue-500
                      focus:border-blue-500
                    "
                  />
                  <div class="px-2 text-gray-600 lg:text-sm">of 2</div>
                </div>

                <Link
                  href="#"
                  as="span"
                  class="
                    inline-flex
                    justify-center
                    items-center
                    w-11
                    h-11
                    text-gray-700
                    bg-white
                    rounded
                    border border-gray-300
                    shadow-sm
                    outline-none
                    hover:bg-gray-50
                    lg:h-9
                    lg:w-9
                    focus:ring-1 focus:ring-blue-500
                    focus:border-blue-500
                  "
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4 lg:h-3 lg:w-3"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 5l7 7-7 7"
                    />
                  </svg>
                </Link>

                <Link
                  href="#"
                  as="span"
                  class="
                    inline-flex
                    justify-center
                    items-center
                    w-11
                    h-11
                    text-gray-700
                    bg-white
                    rounded
                    border border-gray-300
                    shadow-sm
                    outline-none
                    hover:bg-gray-50
                    lg:h-9
                    lg:w-9
                    focus:ring-1 focus:ring-blue-500
                    focus:border-blue-500
                  "
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-4 h-4 lg:h-3 lg:w-3"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M13 5l7 7-7 7M5 5l7 7-7 7"
                    />
                  </svg>
                </Link>
              </div>
            </div>
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
import UppyTrigger from "./MediaLibUploader.vue";
import { pickBy } from "lodash";

export default defineComponent({
  props: {
    media: Object,
    fileTypes: Array,
    months: Array,
    queryParams: Object,
  },
  components: {
    AppLayout,
    UppyTrigger,
    Link,
  },
  data() {
    return {
      query: {
        fileType: this.queryParams.fileType,
        month: this.queryParams.month,
      },
    };
  },
  methods: {
    filter() {
      this.$inertia.get(route("media.index"), pickBy(this.query));
    },
  },
  computed: {
    allFileTypes() {
      return [{ value: null, label: "Any type" }, ...this.fileTypes];
    },
    allMonths() {
      return [{ value: null, label: "Any date" }, ...this.months];
    },
  },
});
</script>
