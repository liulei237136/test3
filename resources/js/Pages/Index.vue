<template>
  <app-layout title="Index">
    <div class="px-4 bg-white overflow-hidden">
        {{ $t('test')}}
    </div>
  </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { pickby } from "lodash";

export default defineComponent({
  components: {
    AppLayout,
    Link,
  },
  data() {
    return {
    };
  },

});
</script>
