<template>
  <app-layout title="Package">
    <template #header>
      <h2 class="py-4 font-semibold text-xl text-gray-800 leading-tight">
        点读包还没有音频，选择下面的方式来初始化
      </h2>
    </template>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div v-show="processing" class="flex">
        <progress :value="percent" class="w-full" max="100">{{ percent }}%</progress>
        &nbsp;{{ percent }}%
      </div>
      <div
        v-show="!processing"
        class="md:flex-row md:space-x-4 md:items-center md:space-y-0 w-full flex flex-col text-left space-y-4"
      >
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
              route('package.show', { package: p.id, tab: 'audio' }),
              {},
              { replace: true }
            )
          "
          >直接编辑</vxe-button
        >
      </div>
    </div>
  </app-layout>
</template>
<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineComponent } from "vue";

export default defineComponent({
  components: {
    AppLayout,
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
    async uploadAudio(e) {
      const files = e.target.files;
      const lengthOfFiles = files.length;
      const audio_ids = [];
      let count = 0;

      if (!lengthOfFiles) return;

      this.processing = true;
      for (let file of files) {
        count++;
        const data = new FormData();
        data.append("file", file);
        data.append("name", file.name);
        try {
          const result = await axios.post(route("audio.store"), data, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          });
          audio_ids.push(result.data.data.id);
        } catch (e) {
          console.log(e);
        }

        //todo error handle
        this.percent = Math.ceil((count / lengthOfFiles) * 100);
      }

      this.$inertia.post(route("commit.store"), {
        package: this.package.id,
        title: "初次保存",
        audio_ids,
      });
      //   try {
      //     const result = await axios.post(route("commit.store"), {
      //       package: this.package.id,
      //       title: "初次保存",
      //       path: [],
      //       ids,
      //     });
      //     // await this.$inertia.get(
      //     //   route("package.audio", {
      //     //     package: this.package.id,
      //     //   }),
      //     //   {},
      //     //   { replace: true }
      //     // );
      //   } catch (e) {
      //     console.log(e);
      //   }
    },
  },
});
</script>
