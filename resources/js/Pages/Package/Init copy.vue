<template>
  <app-layout title="Package">
    <template #header>
      <div class="flex items-center space-x-2">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          初始化点读包
        </h2>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5 flex flex-col justify-between">
          <div>
            <Uploader :p="p" buttonText="上传mp3完成初始化" class="text-xl "></Uploader>
          </div>
          <div class="mb-4">或者</div>
          <div>
            <Link
              :href="route('package.edit', { package: p })"
              class="
              mt-5
                bg-purple-500
                text-white
                active:bg-purple-600
                font-bold
                uppercase
                text-xl
                px-4
                py-2
                rounded
                shadow
                hover:shadow-md
                outline-none
                focus:outline-none
                mr-1
                mb-1
                ease-linear
                transition-all
                duration-150
              "
              >直接开始编辑</Link
            >
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineComponent } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
import JetButton from "@/Jetstream/Button.vue";
import Uploader from "./Uploader.vue";

export default defineComponent({
  components: {
    JetButton,
    AppLayout,
    Uploader,
    Link,
  },

  props: ["package"],

  data() {
    return {
      p: this.package,
    };
  },

  methods: {},
});
</script>
