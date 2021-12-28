<template>
  <audio v-if="url" :src="url" @play="onPlay($event, row)" controls></audio>
</template>

<script>
import { defineComponent, nextTick } from "vue";

export default defineComponent({
  props: {
    url: String,
    row: Object,
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
