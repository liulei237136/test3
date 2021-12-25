<template>
  <audio ref="audio" controls>
    <source :src="url" type="audio/mp3" />
  </audio>
</template>

<script>
import { defineComponent } from "vue";
import Plyr from "plyr";

export default defineComponent({
  props: {
    url: String,
  },
  mounted() {
    const player = new Plyr(this.$refs.audio, {
      controls: ["play-large", "play", "progress", "current-time"],
    });
  },
});
</script>
