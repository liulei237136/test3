<template>
  <div>{{ message }}</div>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
  props: ["message"],
});
</script>
