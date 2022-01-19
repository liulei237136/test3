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
      <div v-if="!demo.open">
        <button
          type="button"
          class="bg-green-500 px-4 py-2 rounded text-white"
          @click="demo.open = true"
        >
          创建拉取
        </button>
      </div>
      <div v-else class="flex items-start">
        <button
          v-if="$page.props.jetstream.managesProfilePhotos"
          class="flex mt-1 mr-2 text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"
        >
          <img
            class="h-8 w-8 rounded-full object-cover"
            :src="$page.props.user.profile_photo_url"
            :alt="$page.props.user.name"
          />
        </button>
        <form class="w-full max-w-3xl" @submit.prevent="onSubmit">
          <!-- <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" ref="input"> -->
          <jet-input
            id="title"
            type="text"
            class="mt-2 block w-full"
            v-model="form.title"
            autocomplete="title"
            required
            placeholder="标题"
          />
          <jet-input-error :message="form.errors.title" class="mt-2" />
          <!-- <textarea name="comment" id="comment" cols="30" rows="10"></textarea> -->
          <textarea
            id="description"
            v-model="form.description"
            autocomplete="description"
            rows="3"
            placeholder="描述"
            class="mt-2 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
          >
          </textarea>
          <jet-input-error :message="form.errors.description" class="mt-2" />
          <div class="w-full mt-2 max-w-3xl flex justify-end">
            <!-- <jet-button
              :class="{ 'opacity-25': form.processing }"
              class="mt-3"
              :disabled="form.processing"
            >
              保存
            </jet-button> -->
            <!-- <button
              type="button"
              class="bg-green-500 p-2 rounded"
              :class="{ 'text-white': !form.title }"
              :disabled="!form.title"
            >
              创建一个求拉
            </button> -->
            <!-- <Link href="#" class="bg-green-500 p-2 rounded text-white" :disabled="true">
              创建一个求拉</Link
            > -->
            <button
              type="submit"
              class="bg-green-500 p-2 rounded text-white disabled:opacity-50 disabled:cursor-default"
              :disabled="!form.title || form.processing"
            >
              创建一个求拉
            </button>
          </div>
        </form>
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
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import { useForm } from "@inertiajs/inertia-vue3";

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
    JetInput,
    JetInputError,
  },
  setup(props, context) {
    const xGrid = ref({});
    const form = useForm({
      title: null,
      description: null,
    });
    const demo = reactive({
      open: false,
    });
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

    const onSubmit = () => {
      form.post(route("pull.store", { child: props.child.id }));
    };
    return {
      xGrid,
      audioList,
      demo,
      form,
      onSubmit,
    };
  },
});
</script>
