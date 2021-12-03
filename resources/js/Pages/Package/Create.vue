<template>
  <app-layout title="Package">
    <template #header>
      <div class="flex items-center space-x-2">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          新建点读包
        </h2>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
          <form @submit.prevent="form.post(route('package.store'),{replace:true})" class="space-y-5">
            <!-- Name -->
            <div>
              <jet-label for="name" value="名称" />
              <jet-input
                id="name"
                type="text"
                class="mt-2 block w-full max-w-lg"
                v-model="form.name"
                ref="name"
                autocomplete="name"
                required
              />
              <!-- <jet-input-error :message="form.errors.name" class="mt-2" /> -->
            </div>
            <!-- private -->
            <div>
              <div class="mb-2">
                <label for="public">
                  <input
                    id="public"
                    type="radio"
                    v-model="form.isPrivate"
                    :value="false"
                  />
                  <span class="ml-2"
                    >公开<span class="text-sm text-gray-600"
                      >(任何人都可以看到这个点读包,您可以选择谁可以向它提交)</span
                    ></span
                  >
                </label>
              </div>
              <div>
                <label for="private">
                  <input
                    id="private"
                    type="radio"
                    v-model="form.isPrivate"
                    :value="true"
                  />
                  <span class="ml-2"
                    >私有<span class="text-sm text-gray-600"
                      >(您可以选择谁可以查看并提交到这个点读包)</span
                    ></span
                  >
                </label>
              </div>
            </div>

            <!-- description -->
            <div>
              <jet-label for="description" value="描述" />
              <div>
                <textarea
                  id="description"
                  name="description"
                  v-model="form.description"
                  autocomplete="description"
                  rows="3"
                  ref="description"
                  class="
                    mt-2
                    block
                    max-w-lg
                    focus:ring-indigo-500 focus:border-indigo-500
                    w-full
                    shadow-sm
                    sm:text-sm
                    border-gray-300
                    rounded-md
                  "
                  required
                >
                </textarea>
                <!-- <jet-input-error
                  :message="form.errors.description"
                  class="mt-2"
                /> -->
              </div>
            </div>

            <div class="col-span-6 sm:col-span-4">
              <jet-button
                class="mt-3"
                :disable="form.processing"
                :class="{ 'opacity-25': form.processing }"
              >
                创建
              </jet-button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import { defineComponent } from "vue";
import JetButton from "@/Jetstream/Button.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";

export default defineComponent({
  components: {
    JetActionMessage,
    JetButton,
    JetInput,
    JetInputError,
    JetLabel,
    AppLayout,
  },

  props: ["user"],

  data() {
    return {
        form: this.$inertia.form({
            name: '',
            description: '',
            isPrivate: false,
        })
    };
  },

});
</script>
