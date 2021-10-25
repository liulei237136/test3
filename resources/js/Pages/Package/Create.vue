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
          <form
            @submit.prevent="createPackage"
            class="space-y-5"
          >
            <!-- Name -->
            <div>
              <jet-label for="name" value="名称" />
              <jet-input
                id="name"
                type="text"
                class="mt-2 block w-full max-w-lg"
                v-model="form.name"
                autocomplete="name"
              />
              <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>

            <!-- category -->
            <div>
              <jet-label for="category" value="分类" />
              <div >
                <select
                  id="category"
                  name="category"
                  v-model="form.category"
                  autocomplete="country"
                  class="
                    mt-2
                    max-w-md
                    block
                    focus:ring-indigo-500 focus:border-indigo-500
                    w-full
                    shadow-sm
                    sm:max-w-xs sm:text-sm
                    border-gray-300
                    rounded-md
                  "
                >
                  <option value="小达人">小达人</option>
                  <option  value="毛毛虫">毛毛虫</option>
                  <option value="卷之友">卷之友</option>
                </select>
              </div>
            </div>

                      <!-- description -->
            <div>
              <jet-label for="description" value="描述" />
              <div >
                <textarea
                  id="description"
                  name="description"
                  v-model="form.description"
                  autocomplete="description"
                  rows="3"
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
                >
                </textarea>
              </div>
            </div>

            <div class="col-span-6 sm:col-span-4 ">
              <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                Saved.
              </jet-action-message>

              <jet-button
                :class="{ 'opacity-25': form.processing }"
                class="mt-3"
                :disabled="form.processing"
              >
                Save
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
        _method: "POST",
        name: "",
        category: "小达人",
        description: "",
      }),
    };
  },

  methods: {
    createPackage() {
      this.form.post(route("package.store"), {
        errorBag: "createPackage",
        preserveScroll: true,
        onSuccess: () => this.reset(),
      });
    },

    reset() {
      this.form.reset();
    },
  },
});
</script>
