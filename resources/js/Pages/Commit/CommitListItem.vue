<template>
  <div :class="classes">
    <h3>
      <Link :href="route('commit.show', { package: p.id, commit: commit.id })">{{
        commit.title
      }}</Link>
    </h3>
    <div class="flex items-center">
      <Link :href="route('users.show', { user: commit.author.id })">
        <button
          class="flex mt-1 mr-2 text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"
        >
          <img
            class="h-8 w-8 rounded-full object-cover"
            :src="commit.author.profile_photo_url"
            :alt="commit.author.name"
          />
        </button>
      </Link>

      <p class="text-sm">
        <Link
          :href="
            route('package.commit.index', { package: p.id, author: commit.author.id })
          "
          class="hover:underline"
          :title="`查看${commit.author.name}的所有保存`"
          >{{ commit.author.name }}</Link
        >
        <span class="ml-4 text-gray-500"
          >创建于 {{ new Date(commit.created_at).toLocaleString() }}</span
        >
      </p>
    </div>
  </div>
</template>

<script>
import { defineComponent } from "vue";
import { Link } from "@inertiajs/inertia-vue3";

export default defineComponent({
  components: {
    Link,
  },
  props: {
    p: Object,
    commit: Object,
    classes: String,
  },
});
</script>
