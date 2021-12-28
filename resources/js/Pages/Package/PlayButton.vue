<template>
  <vxe-button v-if="canShow" :content="content" size="mini" @click="onClick"></vxe-button>
</template>

<script>
import { computed, defineComponent, onMounted, reactive, ref } from "vue";
export default defineComponent({
  emits: ["play", "stop"],
  props: {
    row: Object,
    demo: Object,
    mode: Object,
  },
  setup(props, { emit }) {
    const demo = reactive({
      content: "播放",
    });

    const onClick = () => {
      demo.content === "播放" ? "停止" : "播放";
      emit(demo.content === "播放" ? "play" : "stop");
    };

    computed({
      canShow() {
        return (
          demo.playerMode === "simple" &&
          ((mode === "source" && row.url) ||
            (mode === "local" && row.localFile) ||
            (mode === "record" && row.recordFile))
        );
      },
    });

    return { onClick };
  },
});
</script>
