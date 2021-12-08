<template>
  <div class="flex items-center">
    <div style="width: 320px">
      <audio v-if="url" :src="url" controls ref="audio" class="mr-1"></audio>
      <!-- <button v-if="showPlay" onClick="onPlayClick" class="purpleButton bg-purple-400 p-2 mr-1">播放</button>
      <button v-if="showPause" onClick="onPauseClick" class="purpleButton bg-purple-400 p-2 mr-1">暂停</button>
      <button v-if="showStop" onClick="on" class="purpleButton bg-purple-400 p-2 mr-1">停止</button> -->
    </div>
    <div v-if="canEdit">
      <vxe-button size="mini" :content="content" @click="record"></vxe-button>
      <span class="w-16 inline-flex justify-end align-middle mr-1">
        <span class="mr-1">{{ duration }}</span>
        秒
      </span>
      <vxe-button
        size="mini"
        status="warning"
        content="停止"
        @click="recStop"
        :disabled="status === '空闲'"
      ></vxe-button>
    </div>
  </div>
</template>

<script>
import { defineComponent, nextTick } from "vue";

import Recorder from "recorder-core";
import "recorder-core/src/engine/mp3";
import "recorder-core/src/engine/mp3-engine";

export default defineComponent({
  props: {
    row: Object,
    canEdit: Boolean,
  },
  emits: ["url"],
  data() {
    return {
      rec: null,
      status: "空闲", //空闲，录音，暂停，
      content: "录音", //录音，暂停，恢复
      duration: 0,
      blob: null,
      url: this.row.url,
    };
  },
  components: {},
  methods: {
    record() {
      if (this.status === "空闲") {
        this.recOpen(this.recStart);
      } else if (this.status === "录音") {
        this.recPause();
      } else if (this.status === "暂停") {
        this.recResume();
      }
    },
    recOpen(success) {
      let that = this;
      this.rec = Recorder({
        type: "mp3",
        sampleRate: 16000,
        bitRate: 16,
        onProcess: function (
          buffers,
          powerLevel,
          bufferDuration,
          bufferSampleRate,
          newBufferIdx,
          asyncEnd
        ) {
          that.duration = parseInt(bufferDuration / 1000);
        },
      });

      this.rec.open(
        function () {
          success && success();
        },
        function (msg, isUserNotAllow) {
          console.log((isUserNotAllow ? "UserNotAllow，" : "") + "无法录音:" + msg);
        }
      );
    },

    recStop() {
      const that = this;
      that.rec.stop(
        function (blob, duration) {
          console.log(
            blob,
            (window.URL || webkitURL).createObjectURL(blob),
            "时长:" + duration + "ms"
          );
          that.rec.close();
          that.rec = null;
          that.blob = blob;
          that.url = (window.URL || webkitURL).createObjectURL(blob);
          that.status = "空闲";
          that.content = "录音";
          that.row.blob = blob;
          that.row.url = that.url;
          nextTick(function () {
            that.$refs.audio.play();
          });
        },
        function (msg) {
          console.log("录音失败:" + msg);
          this.rec.close();
          this.rec = null;
          this.status = "空闲";
          this.content = "录音";
        }
      );
    },

    recStart() {
      this.rec.start();
      this.status = "录音";
      this.content = "暂停";
    },

    recPause() {
      this.rec.pause();
      this.status = "暂停";
      this.content = "恢复";
    },

    recResume() {
      this.rec.resume();
      this.status = "录音";
      this.content = "暂停";
    },
  },
});
</script>
