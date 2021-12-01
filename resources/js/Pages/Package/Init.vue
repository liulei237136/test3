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
        <div
          class="
            bg-white
            overflow-hidden
            shadow-xl
            sm:rounded-lg
            p-5
            flex flex-col
            space-y-4
            md:flex-row md:space-x-4 md:items-center md:space-y-0
          "
        >
          <div v-show="isUploading" class="w-full flex">
            <progress :value="percent" class="w-full" max="100">
              {{ percent }}%
            </progress>
            &nbsp;{{ percent }}%
          </div>
          <div>
            <vxe-button
              v-show="!isUploading"
              icon="fa fa-plus"
              status="perfect"
              @click="$refs.input.click()"
              >上传MP3来初始化</vxe-button
            >
            <input
              type="file"
              ref="input"
              accept=".mp3"
              hidden
              multiple
              @change="uploadAudio"
            />
          </div>
          <div v-show="!isUploading" class="hidden md:block">|</div>
          <div v-show="!isUploading">
            <Link
              :href="route('package.audio', { package: p.id })"
              class="whiteButton"
              >直接编辑</Link
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

export default defineComponent({
  components: {
    AppLayout,
    Link,
  },

  props: ["package"],

  data() {
    return {
      p: this.package,
      isUploading: false,
      percent: 0,
    };
  },
  methods: {
    uploadAudio(e) {
      const files = e.target.files;
      const lengthOfFiles = files.length;
      let count = 0;
      if (!lengthOfFiles) return;
      this.isUploading = true;
      for (let file of files) {
        const data = new FormData();
        data.append("file", file);
        axios
          .post(
            route("package.audio.init_upload", { package: this.p.id }),
            data,
            {
              headers: {
                "Content-Type": "multipart/form-data",
              },
            }
          )
          .then((res) => {})
          .catch((err) => console.log(err))
          //todo error handle
          .finally(() => {
            count++;
            this.percent = Math.ceil((count / lengthOfFiles) * 100);
            if (count === lengthOfFiles) {
              this.$inertia.get(route("package.audio", { package: this.p }));
            }
          });
      }
    },
  },
});
</script>
