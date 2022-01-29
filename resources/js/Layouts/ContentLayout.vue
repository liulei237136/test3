<template>
  <app-layout>
    <!-- Page Heading -->
    <header class="bg-white px-4 sm:px-6 lg:px-8">
      <!-- package标题行  -->
      <div class="flex item-center justify-between">
        <package-link :package="package" classes="text-lg"></package-link>

        <div class="inline-flex items-center space-x-4">
          <div class="inline-flex shadow-sm rounded-md" role="group">
            <button @click="toggleStar" class="buttonGroupLeftButton">
              <Icon
                :name="$page.props.user && isStared ? 'solid-star' : 'empty-star'"
                class="w-4 h-4 mr-1"
              ></Icon>
              <span>{{ $page.props.user && isStared ? "取消收藏" : "收藏" }}</span>
            </button>
            <span class="buttonGroupRightSpan">
              {{ starsCount }}
            </span>
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
            <span class="buttonGroupRightSpan">
              {{ clonesCount }}
            </span>
          </div>
        </div>
      </div>
      <!-- 显示forkfrom行 -->
      <div v-if="package.parent" class="text-xs">
        克隆于
        <package-link :package="package.parent" classes="text-sm"></package-link>
      </div>
      <!-- tabs -->
      <div class="flex items-center space-x-2 mt-4 text-lg content-tab">
        <Link
          :href="route('package.show', { package: this.package.id })"
          class="px-4 py-2 flex items-center"
          :class="{
            active:
              componentName === 'Package/ShowBasicInfo' ||
              componentName === 'Package/EditBasicInfo',
          }"
        >
          <Icon name="info" class="d-none sm:inline w-5 h-5 mr-1"></Icon>
          <span>基本信息</span></Link
        >
        <Link
          :href="route('package.audio', { package: this.package.id })"
          class="px-4 py-2 flex items-center"
          :class="{
            active:
              componentName === 'Package/ShowAudio' ||
              componentName === 'Package/EditAudio',
          }"
        >
          <Icon name="audio" class="d-none sm:inline w-5 h-5 mr-1"></Icon>
          <span>包含音频</span></Link
        >
        <Link
          :href="route('package.pulls', { package: this.package.id })"
          class="px-4 py-2 flex items-center"
          :class="{
            active: componentName === 'Package/ShowPulls',
          }"
        >
          <Icon name="pull" class="d-none sm:inline w-5 h-5 mr-1"></Icon>
          <span>拉取</span></Link
        >
      </div>
    </header>

    <!-- Page Content -->
    <main class="px-8 py-4">
      <slot></slot>
    </main>
  </app-layout>
</template>

<style scoped>
.content-tab .active {
  border-bottom: 1px solid gray;
  margin-bottom: -1px;
}
</style>

<script>
import { defineComponent } from "vue";
import JetApplicationMark from "@/Jetstream/ApplicationMark.vue";
import JetBanner from "@/Jetstream/Banner.vue";
import JetDropdown from "@/Jetstream/Dropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink.vue";
import JetNavLink from "@/Jetstream/NavLink.vue";
import JetResponsiveNavLink from "@/Jetstream/ResponsiveNavLink.vue";
import { Head, Link } from "@inertiajs/inertia-vue3";
import Icon from "@/Components/Icon.vue";
import PackageLink from "@/Components/PackageLink.vue";
import AppLayout from "./AppLayout.vue";

export default defineComponent({
  props: {
    // title: String,
  },

  components: {
    Head,
    JetApplicationMark,
    JetBanner,
    JetDropdown,
    JetDropdownLink,
    JetNavLink,
    JetResponsiveNavLink,
    Link,
    Icon,
    PackageLink,
    AppLayout,
  },

  data() {
    return {
      showingNavigationDropdown: false,
      q: this.$page.props.queryParams ? this.$page.props.queryParams.q : "",
      package: this.$page.props.package,
      starsCount: this.$page.props.package.stars_count,
      isStared: this.$page.props.package.stared,
      clonesCount: this.$page.props.package.clones_count,
      isCloned: this.$page.props.package.isCloned,
      componentName: this.$page.component,
    };
  },
  computed: {
    myPackage() {
      return this.$page.props.user && this.$page.props.user.id === this.package.author.id;
    },
  },
  methods: {
    search() {
      this.$inertia.get(route("search"), { q: this.q });
    },

    logout() {
      this.$inertia.post(route("logout"));
    },

    toggleStar() {
      //如果用户已经登录，用axios比较好
      if (this.$page.props.user) {
        if (this.isStared) {
          axios.delete(route("star-packages.destroy", { package: this.package.id }));
          this.starsCount--;
          this.isStared = false;
        } else {
          axios.post(route("star-packages.store", { package: this.package.id }));
          this.starsCount++;
          this.isStared = true;
        }
      } else {
        //如果没登录，才用inertia实现登录后跳转
        this.$inertia.post(route("star-packages.store", { package: this.package.id }));
      }
    },
    onClone() {
      this.$inertia.post(route("package.clone", { package: this.package.id }));
    },
  },
  mounted() {
    console.log(this.$page);
  },
});
</script>
