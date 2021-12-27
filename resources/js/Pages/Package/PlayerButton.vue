<template>
  <vxe-button
    v-if="demo.playMode === 'simple' && row.recordUrl"
    size="mini"
    content="播放"
    @click="onRecordPlayButtonClick($event, row)"
  ></vxe-button>
</template>

<script>
import { defineComponent, nextTick } from "vue";

export default defineComponent({
  props: {
    url: String,
  },
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
});
</script>
