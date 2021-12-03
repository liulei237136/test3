<template>
  <app-layout title="点读包信息">
    <template #header>
      <!-- package标题行  -->
      <div class="flex item-center justify-between">
        <user-and-package-link
          :package="package"
          classes="text-xl"
        ></user-and-package-link>

        <div class="inline-flex items-center space-x-4">
          <div class="inline-flex shadow-sm rounded-md" role="group">
            <button
              @click="onFavorite"
              class="rounded-l-lg border border-gray-200 bg-white text-sm font-medium px-4 py-2 text-gray-900 hover:bg-gray-100  focus:z-10  inline-flex items-center dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white"
            >
              <svg
                aria-hidden="true"
                viewBox="0 0 16 16"
                class="w-4 h-4 mr-1"
                version="1.1"
              >
                <path
                  v-if="!$page.props.user || !isFavorited"
                  fill-rule="evenodd"
                  d="M8 .25a.75.75 0 01.673.418l1.882 3.815 4.21.612a.75.75 0 01.416 1.279l-3.046 2.97.719 4.192a.75.75 0 01-1.088.791L8 12.347l-3.766 1.98a.75.75 0 01-1.088-.79l.72-4.194L.818 6.374a.75.75 0 01.416-1.28l4.21-.611L7.327.668A.75.75 0 018 .25zm0 2.445L6.615 5.5a.75.75 0 01-.564.41l-3.097.45 2.24 2.184a.75.75 0 01.216.664l-.528 3.084 2.769-1.456a.75.75 0 01.698 0l2.77 1.456-.53-3.084a.75.75 0 01.216-.664l2.24-2.183-3.096-.45a.75.75 0 01-.564-.41L8 2.694v.001z"
                ></path>
                <path
                  v-else
                  fill-rule="evenodd"
                  d="M8 .25a.75.75 0 01.673.418l1.882 3.815 4.21.612a.75.75 0 01.416 1.279l-3.046 2.97.719 4.192a.75.75 0 01-1.088.791L8 12.347l-3.766 1.98a.75.75 0 01-1.088-.79l.72-4.194L.818 6.374a.75.75 0 01.416-1.28l4.21-.611L7.327.668A.75.75 0 018 .25z"
                ></path>
              </svg>
              <span v-if="!$page.props.user || !isFavorited">收藏</span>
              <span v-else>取消收藏</span>
            </button>
            <a
              href="#"
              class="rounded-r-md border border-gray-200 bg-white text-sm font-medium px-4 py-2 hover:text-blue-700 text-gray-900 hover:bg-gray-100  focus:z-10 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white"
            >
              40
            </a>
          </div>
          <div>
            <button
              @click="onClone"
              class="inline-flex items-center px-4 py-2 text-sm rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 }"
            >
              <svg aria-hidden="true" viewBox="0 0 16 16" class="w-4 h-4 mr-1">
                <path
                  fill-rule="evenodd"
                  d="M5 3.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm0 2.122a2.25 2.25 0 10-1.5 0v.878A2.25 2.25 0 005.75 8.5h1.5v2.128a2.251 2.251 0 101.5 0V8.5h1.5a2.25 2.25 0 002.25-2.25v-.878a2.25 2.25 0 10-1.5 0v.878a.75.75 0 01-.75.75h-4.5A.75.75 0 015 6.25v-.878zm3.75 7.378a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm3-8.75a.75.75 0 100-1.5.75.75 0 000 1.5z"
                ></path>
              </svg>
              <span>克隆</span>
            </button>
          </div>
        </div>
      </div>
      <!-- 显示forkfrom行 -->
      <div v-if="package.parent" class="text-xs">
        克隆于
        <user-and-package-link :package="package.parent"></user-and-package-link>
      </div>
      <!-- tabs -->
      <div class="flex items-center space-x-2 mt-8 text-lg">
        <Link
          :href="route('package.info', { package: package.id })"
          class="px-4 py-2 flex items-center"
          :style="styleLink('Info')"
        >
          <svg
            aria-hidden="true"
            viewBox="0 0 16 16"
            version="1.1"
            class="d-none sm:inline w-5 h-5"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M1.75 0A1.75 1.75 0 000 1.75v12.5C0 15.216.784 16 1.75 16h12.5A1.75 1.75 0 0016 14.25V1.75A1.75 1.75 0 0014.25 0H1.75zM1.5 1.75a.25.25 0 01.25-.25h12.5a.25.25 0 01.25.25v12.5a.25.25 0 01-.25.25H1.75a.25.25 0 01-.25-.25V1.75zM11.75 3a.75.75 0 00-.75.75v7.5a.75.75 0 001.5 0v-7.5a.75.75 0 00-.75-.75zm-8.25.75a.75.75 0 011.5 0v5.5a.75.75 0 01-1.5 0v-5.5zM8 3a.75.75 0 00-.75.75v3.5a.75.75 0 001.5 0v-3.5A.75.75 0 008 3z"
            ></path>
          </svg>
          &nbsp;基本信息</Link
        >
        <Link
          :href="route('package.audio', { package: package.id })"
          class="px-4 py-2 flex items-center"
          :style="styleLink('Audio')"
        >
          <svg
            aria-hidden="true"
            class="d-none sm:inline w-5 h-5"
            fill="currentColor"
            viewBox="0 0 18 18"
          >
            <path
              d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"
            />
          </svg>
          &nbsp;包含音频</Link
        >
      </div>
    </template>

    <div class="py-12">
      <div class="mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <component :is="tab" :package="package"></component>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import Info from "./BasicInfo.vue";
import Audio from "./Audio.vue";
import UserAndPackageLink from "@/Components/UserAndPackageLink";

export default defineComponent({
  props: {
    package: Object,
    tab: String,
    isFavorited: Boolean,
    favoritesCount: Number,
  },
  components: {
    AppLayout,
    Link,
    Info,
    Audio,
    UserAndPackageLink,
  },
  data() {
    return {
      isFavorited: this.isFavorited,
      favoritesCount: this.favoritesCount,
    };
  },
  methods: {
    styleLink(tabName) {
      return this.tab === tabName
        ? "borderBottom: 1px solid #FD8C73; marginBottom: -1px"
        : "";
    },
    onFavorite() {
        this.$inertia.post(route('package.toggle_favorite', {package: this.package.id}));
    },
    onClone() {
      this.$inertia.post(route("package.clone", { package: this.package.id }), null, {
        preserveState: false,
        preserveScroll: false,
      });
    },
  },
});
</script>
