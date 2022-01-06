<template>
  <div class="flex justify-between">
    <button class="bg-green-500 px-4 py-2 rounded" @click="onNewPull">新建一个拉取</button>
  </div>
  <ul v-if="pulls && pulls.length">
    <li v-for="pull in pulls" :key="pull.id">
      {{ pull.title }}
    </li>
  </ul>
  <div v-else>没有求拉哦</div>
</template>

<script>
import { Inertia } from "@inertiajs/inertia";
import { Link } from "@inertiajs/inertia-vue3";
import { defineComponent, nextTick, onMounted, reactive, ref } from "vue";
import axios from "axios";

export default defineComponent({
  props: {
    package: Object,
    canEdit: Boolean,
    commits: Array,
    pulls: Array,
  },
  components: {
    Link,
  },

  setup(props, context) {
    const demo = reactive({
      //   pullList: this.pulls ? this.pulls : [],
    });
    const onNewPull = () => {
        if($page.user && props.package.parent){
            alert('compare ready')
        }else{
            alert('not ready');
        }
        // Inertia.get(route('compare.package', {toPackage:}));
    };
    onMounted(() => {});
    return {
      demo,
      onNewPull,
    };
  },

});
</script>
