<template>
  <app-layout title="编辑点读包的音频">
    <template #header>
      <div class="flex items-center space-x-2">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          编辑点读包包含的音频
        </h2>

        <uppy-trigger v-on:closed="alert('closed')"  :p="p"></uppy-trigger>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import UppyTrigger from "./Uploader.vue";
// import { pickBy } from "lodash";

export default defineComponent({
  props: {
    package: Object,
    // fileTypes: Array,
    // months: Array,
    // queryParams: Object,
  },
  components: {
    AppLayout,
    UppyTrigger,
    Link,
  },
  data() {
    return {
        p: this.package
    //   query: {
    //     fileType: this.queryParams.fileType,
    //     month: this.queryParams.month,
    //   },
    };
  },
  methods: {
    // filter() {
    //   this.$inertia.get(route("media.index"), pickBy(this.query));
    // },
  },
  computed: {
    // allFileTypes() {
    //   return [{ value: null, label: "Any type" }, ...this.fileTypes];
    // },
    // allMonths() {
    //   return [{ value: null, label: "Any date" }, ...this.months];
    // },
  },
});
</script>
