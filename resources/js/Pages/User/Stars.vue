<template>
  <user-layout><div>stars</div></user-layout>
</template>

<script>
import UserLayout from "@/Layouts/UserLayout.vue";
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    targetUser: Object,
  },
  components: {
    UserLayout,
  },
});
</script>
