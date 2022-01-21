<template>
  <app-layout>
    <div>{{ user.name }}</div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    user: Object,
  },
  components: {
    AppLayout,
  },
});
</script>
