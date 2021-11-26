<template>
  <app-layout title="点读包信息">
    <template #header>
      <!-- package标题行  -->
      <div class="flex item-center justify-between">
        <user-and-package-link
          :package="package"
          classes="text-xl"
        ></user-and-package-link>
        <div>
          <button
            @click="onClone"
            class="
              inline-flex
              items-center
              px-4
              py-2
              text-sm
              rounded
              shadow
              hover:shadow-md
              outline-none
              focus:outline-none
              ease-linear
              transition-all
              duration-150
              }
            "
          >
            <svg aria-hidden="true" viewBox="0 0 16 16" class="w-4 h-4">
              <path
                fill-rule="evenodd"
                d="M5 3.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm0 2.122a2.25 2.25 0 10-1.5 0v.878A2.25 2.25 0 005.75 8.5h1.5v2.128a2.251 2.251 0 101.5 0V8.5h1.5a2.25 2.25 0 002.25-2.25v-.878a2.25 2.25 0 10-1.5 0v.878a.75.75 0 01-.75.75h-4.5A.75.75 0 015 6.25v-.878zm3.75 7.378a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm3-8.75a.75.75 0 100-1.5.75.75 0 000 1.5z"
              ></path>
            </svg>
            &nbsp;<span>克隆</span>
          </button>
        </div>
      </div>
      <!-- 显示forkfrom行 -->
      <div v-if="package.parent" class="text-xs">
        克隆于
        <user-and-package-link
          :package="package.parent"
        ></user-and-package-link>
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
  },
  components: {
    AppLayout,
    Link,
    Info,
    Audio,
    UserAndPackageLink,
  },
  methods: {
    styleLink(tabName) {
      return this.tab === tabName
        ? "borderBottom: 1px solid #FD8C73; marginBottom: -1px"
        : "";
    },
    onClone() {
      this.$inertia.post(
        route("package.clone", { package: this.package.id }),
        null,
        {
          preserveState: false,
          preserveScroll: false,
        }
      );
    },
  },
});
</script>
