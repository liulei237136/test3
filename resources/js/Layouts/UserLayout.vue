<template>
  <app-layout>
    <div class="flex items-center space-x-2 mt-4 text-lg user-tab">
      <Link
        :href="route('user.show', { user: this.targetUserId })"
        class="px-4 py-2 flex items-center"
        :class="{
          active: componentName === 'User/Overview',
        }"
      >
        <Icon name="info" class="d-none sm:inline w-5 h-5 mr-1"></Icon>
        <span>概览</span></Link
      >
      <Link
        :href="route('user.show', { user: this.targetUserId, tab: 'packages' })"
        class="px-4 py-2 flex items-center"
        :class="{
          active: componentName === 'User/Packages',
        }"
      >
        <Icon name="audio" class="d-none sm:inline w-5 h-5 mr-1"></Icon>
        <span>我创建的点读包</span></Link
      >
      <Link
        :href="route('user.show', { user: this.targetUserId, tab: 'stars' })"
        class="px-4 py-2 flex items-center"
        :class="{
          active: componentName === 'User/Stars',
        }"
      >
        <Icon name="pull" class="d-none sm:inline w-5 h-5 mr-1"></Icon>
        <span>我的收藏</span></Link
      >
    </div>
    <div>
      <slot></slot>
    </div>
  </app-layout>
</template>

<style scoped>
.user-tab a:hover {
  border-bottom: 1px solid lightgray;
  margin-bottom: -1px;
}
.user-tab .active {
  border-bottom: 1px solid orange;
  margin-bottom: -1px;
}
</style>

<script>
import { defineComponent } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
import Icon from "@/Components/Icon.vue";
import AppLayout from "./AppLayout.vue";

export default defineComponent({
  components: {
    Link,
    Icon,
    AppLayout,
  },

  data() {
    return {
      componentName: this.$page.component,
      targetUserId: this.$page.props.targetUser.id,
    };
  },
});
</script>
