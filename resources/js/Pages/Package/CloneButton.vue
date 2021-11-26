<template>
  <jet-button @click="onClone"> <slot></slot> </jet-button>
</template>

<script>
import { defineComponent } from "vue";
import JetButton from "@/Jetstream/Button.vue";

export default defineComponent({
  props: {
    package: Object,
  },
  components: {
    JetButton,
  },
  data() {
    return {
      showModal: false,
      modalContent: "",
      afterModalShow: null,
    };
  },
  methods: {
    onClone() {
      this.$inertia.post(
        route("package.clone", { package: this.package.id }),
        null,
        {
          preserveState: false,
          preserveScroll: false,
        }
      );
    },
  },
});
</script>
