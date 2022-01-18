<template>
  <content-layout>
    <div class="max-w-7xl mx-auto">
      <h1 class="text-xl mb-2">比较改变</h1>
      <div class="mb-2">
        <package-link as="button" :package="parent"></package-link>
        <i class="fas fa-arrow-left text-gray-500 mx-4 text-sm"></i>
        <package-link as="button" :package="child"></package-link>
        <span v-if="diff.pass === true">这个求拉可以自动融合</span>
      </div>
      <div>
        <Link
          as="button"
          class="bg-green-500 px-4 py-2 rounded text-white"
          type="button"
          href="#"
          >创建拉取</Link
        >
      </div>
      <div v-if="diff.pass === true" class="mt-2 flex justify-around">
        <div>{{ diff.commits.length }}个保存</div>
        <div>{{ diff.deleteAudio.length }}行删除</div>
        <div>{{ diff.insertAudio.length }}行新增</div>
        <div>{{ diff.contributers_count }}个贡献者</div>
      </div>
      <!-- commits -->
      <div>
        <h3>保存:</h3>
        <ul v-if="diff.pass === true" class="border border-2 rounded-md">
          <li class="p-2 border bord-b-2" v-for="commit in diff.commits" :key="commit.id">
            <h3>{{ commit.title }}</h3>
            <p>
              <span>{{ commit.author.name }}</span>
              <span class="ml-4 text-gray-500">创建于 {{ commit.created_at }}</span>
            </p>
          </li>
        </ul>
      </div>
      <!-- audios -->
      <div class="mt-4">
        显示{{ diff.deleteAudio.length }}行删除和{{ diff.insertAudio.length }}行新增:
        <audio-table :audioList="audioList"></audio-table>
      </div>
    </div>
  </content-layout>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";
import { defineComponent, nextTick, onMounted, reactive, ref } from "vue";
import ContentLayout from "@/Layouts/ContentLayout.vue";
import PackageLink from "@/Components/PackageLink.vue";
import AudioTable from "@/Components/AudioTable.vue";

export default defineComponent({
  props: {
    parent: Object,
    child: Object,
    package: Object,
    canEdit: Boolean,
    diff: Object,
  },
  components: {
    Link,
    ContentLayout,
    PackageLink,
    AudioTable,
  },
  setup(props, context) {
    const xGrid = ref({});
    const audioList = [];
    if (props.diff.pass) {
      props.diff.deleteAudio.forEach((audio) => {
        audio.status = "deleted";
        audioList.push(audio);
      });
      props.diff.insertAudio.forEach((audio) => {
        audio.status = "inserted";
        audioList.push(audio);
      });
    }
    return {
      xGrid,
      audioList,
    };
  },
});
</script>
