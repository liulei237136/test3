<template>
  <app-layout title="Package">
    <template #header>
      <div class="flex items-center space-x-2">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          点读包还没有音频，选择下面的方式来初始化
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
          "
        >
          <div v-show="processing" class="w-full flex">
            <progress :value="percent" class="w-full" max="100">
              {{ percent }}%
            </progress>
            &nbsp;{{ percent }}%
          </div>
          <div v-show="!processing" class="md:flex-row md:space-x-4 md:items-center md:space-y-0 w-full flex flex-col text-left
            space-y-4">
            <vxe-button
              icon="fa fa-upload"
              status="perfect"
              size="medium"
              @click="$refs.input.click()"
              >上传MP3</vxe-button
            >
            <input
              type="file"
              ref="input"
              accept=".mp3"
              hidden
              multiple
              @change="uploadAudio"
            />
          <!-- <div class="hidden md:block">|</div>
          <vxe-button icon="fa fa-copy" status="perfect" @click="copy"
            >复制其他点读包音频</vxe-button
          > -->
          <div class="hidden md:block">|</div>
          <vxe-button
            icon="fa fa-edit"
            status="perfect"
            @click="
              $inertia.get(
                route('package.audio', { package: p.id }),
                {},
                { replace: true }
              )
            "
            >直接编辑</vxe-button
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
      processing: false,
      percent: 0,
    };
  },
  methods: {
    uploadAudio(e) {
      const files = e.target.files;
      const lengthOfFiles = files.length;
      let count = 0;
      if (!lengthOfFiles) return;
      this.processing = true;
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
              this.$inertia.get(
                route("package.audio", { package: this.p }),
                {},
                { replace: true }
              );
            }
          });
      }
    },
  },
});
</script>
