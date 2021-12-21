<template>
  <app-layout title="点读包信息">
    <template #header>
      <!-- package标题行  -->
      <div class="flex item-center justify-between">
        <user-and-package-link
          :package="package"
          classes="text-xl"
        ></user-and-package-link>

        <div class="inline-flex items-center space-x-4">
          <div class="inline-flex shadow-sm rounded-md" role="group">
            <button @click="onFavorite" class="buttonGroupLeftButton">
              <Icon
                :name="$page.props.user && isFavor ? 'solid-star' : 'empty-star'"
                class="w-4 h-4 mr-1"
              ></Icon>
              <span>{{ $page.props.user && isFavor ? "取消收藏" : "收藏" }}</span>
            </button>
            <a href="#" class="buttonGroupRightLink">
              {{ favorCount }}
            </a>
          </div>
          <div class="inline-flex shadow-sm rounded-md" role="group">
            <button
              @click="onClone"
              :class="{
                buttonGroupLeftButton: !myPackage,
                buttonGroupLeftButtonDisabled: myPackage,
              }"
              :title="myPackage ? '不能克隆自己的项目' : ''"
              :disabled="myPackage"
            >
              <Icon class="w-4 h-4 mr-1" name="clone"></Icon>
              <span>克隆</span>
            </button>
            <a href="#" class="buttonGroupRightLink">
              {{ package.children_count }}
            </a>
          </div>
        </div>
      </div>
      <!-- 显示forkfrom行 -->
      <div v-if="package.parent" class="text-xs">
        克隆于
        <user-and-package-link :package="package.parent"></user-and-package-link>
      </div>
      <!-- tabs -->
      <div class="flex items-center space-x-2 mt-4 text-lg">
        <Link
          :href="route('package.show', { package: package.id, tab: 'info' })"
          class="px-4 py-2 flex items-center"
          :style="styleLink('Info')"
        >
          <Icon name="info" class="d-none sm:inline w-5 h-5 mr-1"></Icon>
          <span>基本信息</span></Link
        >
        <Link
          :href="route('package.show', { package: package.id, tab: 'audio' })"
          class="px-4 py-2 flex items-center"
          :style="styleLink('Audio')"
        >
          <Icon name="audio" class="d-none sm:inline w-5 h-5 mr-1"></Icon>
          <span>包含音频</span></Link
        >
      </div>
    </template>

    <div class="pt-1">
      <div class="mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <component
            :is="tab"
            :package="package"
            :canEdit="canEdit"
            :commitId="commit"
          ></component>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import { Link } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import Info from "./BasicInfo.vue";
import Audio from "./Audio.vue";
import UserAndPackageLink from "@/Components/UserAndPackageLink";
import Icon from "@/Components/Icon.vue";

export default defineComponent({
  props: {
    package: Object,
    isFavorited: Boolean,
    favoritesCount: Number,
    canEdit: Boolean,
    commit: String,
  },
  components: {
    AppLayout,
    Link,
    Info,
    Audio,
    UserAndPackageLink,
    Icon,
  },
  data() {
    return {
      isFavor: this.isFavorited,
      favorCount: this.favoritesCount,
      tab: null,
    };
  },
  computed: {
    myPackage() {
      return this.$page.props.user && this.$page.props.user.id === this.package.author.id;
    },
  },
  mounted() {
    // console.log(this.$page);
    this.tab = this.$page.url.endsWith("info") ? "Info" : "Audio";
  },
  methods: {
    styleLink(tabName) {
      return this.tab === tabName
        ? "borderBottom: 1px solid #FD8C73; marginBottom: -1px"
        : "";
    },
    onFavorite() {
      //如果用户已经登录，用axios比较好
      if (this.$page.props.user) {
        this.isFavor ? this.favorCount-- : this.favorCount++;

        this.isFavor = !this.isFavor;

        axios.post(route("package.toggle_favorite", { package: this.package.id }));
      } else {
        //如果没登录，才用inertia实现登录后跳转
        this.$inertia.post(
          route("package.toggle_favorite", { package: this.package.id })
        );
      }
    },
    onClone() {
      this.$inertia.post(route("package.clone", { package: this.package.id }));
    },
  },
});
</script>
