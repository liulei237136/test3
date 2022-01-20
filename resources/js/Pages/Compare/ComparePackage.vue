<template>
  <content-layout>
    <div class="max-w-7xl mx-auto">
      <h1 class="text-xl mb-2">比较改变</h1>
      <!-- head link -->
      <div
        class="bg-gray-100 rounded flex items-center p-2 text-sm text-gray-500 gap-x-2"
      >
        <Icon name="compare" class="w-5 h-5 mx-2"></Icon>
        <div class="flex gap-2 items-center">
          <Link
            as="button"
            type="button"
            :href="route('package.show', { package: parent.id })"
            class="px-2 py-1 rounded-lg border border-gray-300"
            >父点读包:
            <span class="text-black"
              >{{ parent.author.name }}/{{ parent.title }}</span
            ></Link
          >
          <i class="fas fa-arrow-left"></i>
          <Link
            as="button"
            type="button"
            :href="route('package.show', { package: child.id })"
            class="px-2 py-1 rounded-lg border border-gray-300"
            >子点读包:
            <span class="text-black"
              >{{ child.author.name }}/{{ child.title }}</span
            ></Link
          >
        </div>

        <span v-if="diff.result === 'front'" class="flex items-center text-green-600"
          ><Icon name="check" class="w-5 h-5"></Icon>可以自动融合</span
        >
        <span v-if="diff.result === 'conflict'" class="flex items-center text-red-600">
          <Icon name="x" class="w-5 h-5"></Icon>
          不能自动融合 <span class="text-gray-500">别担心，任然可以创建求拉</span></span
        >
      </div>

      <div class="mt-4" v-if="diff.result === 'front' || diff.result === 'conflict'">
        <button
          type="button"
          class="bg-green-500 px-4 py-2 rounded text-white"
          @click="demo.open = true"
        >
          创建拉取
        </button>
      </div>
      <div
        class="mt-2"
        v-if="(diff.result === 'front' || diff.result === 'conflict') && demo.open"
      >
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
          <textarea
            id="comment"
            v-model="form.comment"
            autocomplete="comment"
            rows="3"
            placeholder="评论"
            class="mt-2 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
          >
          </textarea>
          <jet-input-error :message="form.errors.comment" class="mt-2" />
          <div class="w-full mt-2 max-w-3xl flex justify-end">
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
      <div
        v-if="diff.result === 'front' || diff.result === 'conflict'"
        class="mt-4 flex justify-around border rounded-lg p-2"
      >
        <div>{{ diff.childBeforeCommits.length }}个保存</div>
        <div>{{ diff.deleteAudio ? diff.deleteAudio.length : 0 }}行删除</div>
        <div>{{ diff.insertAudio ? diff.insertAudio.length : 0 }}行新增</div>
        <div>{{ diff.contributersCount }}个贡献者</div>
      </div>
      <!-- commits -->
      <div class="mt-6" v-if="diff.result === 'front' || diff.result === 'conflict'">
        <h3>保存:</h3>
        <ul class="mt-2 border rounded-md">
          <li
            class="p-2 border bord-b-2"
            v-for="commit in diff.childBeforeCommits"
            :key="commit.id"
          >
            <h3>{{ commit.title }}</h3>
            <p class="text-sm">
              <Link
                href="#"
                class="hover:underline"
                :title="`查看${commit.author.name}的所有保存`"
                >{{ commit.author.name }}</Link
              >
              <span class="ml-4 text-gray-500"
                >创建于 {{ new Date(commit.created_at).toLocaleString() }}</span
              >
            </p>
          </li>
        </ul>
      </div>
      <!-- audios -->
      <div class="mt-6" v-if="diff.result === 'front' || diff.result === 'conflict'">
        显示{{ diff.deleteAudio ? diff.deleteAudio.length + "行删除" : "" }}
        {{ diff.deleteAudio && diff.insertAudio ? "和" : "" }}
        {{ diff.insertAudio ? diff.insertAudio.length + "行新增" : "" }}:
        <audio-table :audioList="audioList"></audio-table>
      </div>

      <!-- 空 -->
      <div
        class="mt-8 flex flex-col text-center"
        v-if="diff.result === 'identical' || diff.result === 'behind'"
      >
        <Icon name="compare" class="mx-auto w-8 h-8 text-gray-500"></Icon>
        <h3 class="mt-4 text-lg font-semibold">没有内容可以比较</h3>
        <p class="mt-2 text-sm text-gray-500">
          {{
            diff.result === "identical"
              ? `${parent.author.name} 的 ${parent.title} 和 ${child.author.name} 的 ${child.title} 完全一致`
              : `${parent.author.name} 的 ${parent.title} 包含 ${child.author.name} 的 ${child.title} 所有保存`
          }}
        </p>
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
import Icon from "@/Components/Icon.vue";

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
    Icon,
  },
  setup(props, context) {
    const xGrid = ref({});
    const form = useForm({
      title: null,
      comment: null,
    });
    const demo = reactive({
      open: false,
    });
    const audioList = [];
    if (props.diff.deleteAudio) {
      props.diff.deleteAudio.forEach((audio) => {
        audio.status = "deleted";
        audioList.push(audio);
      });
    }
    if (props.diff.insertAudio) {
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
