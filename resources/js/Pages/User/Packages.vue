<template>
  <user-layout><div>packages</div></user-layout>
</template>

<script>
import UserLayout from "@/Layouts/UserLayout.vue";
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    targetUser: Object,
    packages: Object,
  },
  components: {
    UserLayout,
  },
});
</script>
