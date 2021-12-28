<template>
  <div class="space-y-5">
    <h1>包名: &nbsp;&nbsp;{{ package.name }}</h1>
    <div>作者: &nbsp;&nbsp;{{ package.author.name }}</div>
    <div>描述:</div>
    <p>
      {{ package.description }}
    </p>
  </div>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    package: Object,
  },
});
</script>
