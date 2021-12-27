<template>
  <div class="flex items-center">
    <div class="mr-1">
      <vxe-button size="mini" :content="content" @click="record"></vxe-button>
      <span class="w-12 inline-flex justify-end align-middle mr-1">
        <span class="mr-1">{{ duration }}</span>
        <!-- <span class="mr-1">{{ 999 }}</span> -->
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
    <div>
      <vxe-button
        v-if="demo.playMode === 'simple' && row.recordUrl"
        size="mini"
        content="播放"
        ref="player"
        @click="onRecordPlayButtonClick($event, row)"
      ></vxe-button>
      <audio
        v-if="demo.playMode === 'normal' && recordUrl"
        :src="recordUrl"
        controls
        ref="player"
      ></audio>
      <!-- <button v-if="showPlay" onClick="onPlayClick" class="purpleButton bg-purple-400 p-2 mr-1">播放</button>
      <button v-if="showPause" onClick="onPauseClick" class="purpleButton bg-purple-400 p-2 mr-1">暂停</button>
      <button v-if="showStop" onClick="on" class="purpleButton bg-purple-400 p-2 mr-1">停止</button> -->
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
    demo: Object,
  },
  emits: ["url"],
  data() {
    return {
      rec: null,
      status: "空闲", //空闲，录音，暂停，
      content: "录音", //录音，暂停，继续
      duration: 0,
      blob: null,
      recordUrl: "",
    };
  },
  components: {},
  methods: {
    record() {
      const { audio } = this.demo.playingAudio;
      console.log(audio);
      if (audio) {
        audio.pause();
      }
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
          that.recordUrl = (window.URL || webkitURL).createObjectURL(blob);
          that.status = "空闲";
          that.content = "录音";
          that.row.recordFile = blob;
          that.row.recordUrl = that.recordUrl;
          nextTick(() => {
            if (this.demo.playerMode === "normal") {
              that.$refs.player.play();
            } else if (this.demo.playerMode === "simple") {
              that.$refs.player.click();
            }
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
      this.content = "继续";
    },

    recResume() {
      this.rec.resume();
      this.status = "录音";
      this.content = "暂停";
    },
  },
});
</script>
