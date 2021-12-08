<template>
  <div v-if="!canEdit" class="space-y-5">
    <h1>包名: &nbsp;&nbsp;{{ package.name }}</h1>
    <div>作者: &nbsp;&nbsp;{{ package.author.name }}</div>
    <div>描述:</div>
    <p>
      {{ package.description }}
    </p>
  </div>
  <form v-if="canEdit" @submit.prevent="updatePackage" class="ml-2">
    <!-- Name -->
    <div class="mb-5">
      <jet-label for="name" value="名称" />
      <jet-input
        id="name"
        type="text"
        class="mt-2 block w-full max-w-lg"
        v-model="form.name"
        autocomplete="name"
        required
      />
      <jet-input-error :message="form.errors.name" class="mt-2" />
    </div>
    <!-- private -->
    <div>
      <div class="mb-2">
        <label for="public">
          <input id="public" type="radio" v-model="form.isPrivate" :value="false" />
          <span class="ml-2"
            >公开<span class="text-sm text-gray-600"
              >(任何人都可以看到这个点读包,您可以选择谁可以向它提交)</span
            ></span
          >
        </label>
      </div>
      <div>
        <label for="private">
          <input id="private" type="radio" v-model="form.isPrivate" :value="true" />
          <span class="ml-2"
            >私有<span class="text-sm text-gray-600"
              >(您可以选择谁可以查看并提交到这个点读包)</span
            ></span
          >
        </label>
      </div>
    </div>
    <!-- description -->
    <div class="mb-5">
      <jet-label for="description" value="描述" />
      <div>
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
          required
        >
        </textarea>
        <jet-input-error :message="form.errors.description" class="mt-2" />
      </div>
    </div>

    <div class="col-span-6 sm:col-span-4">
      <jet-button
        :class="{ 'opacity-25': form.processing }"
        class="mt-3"
        :disabled="form.processing"
      >
        保存
      </jet-button>
    </div>
  </form>
</template>

<script>
import { defineComponent } from "vue";
import CloneButton from "./CloneButton.vue";

// import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import JetButton from "@/Jetstream/Button.vue";
// import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";

export default defineComponent({
  props: {
    package: Object,
  },
  data() {
    return {
      canEdit:
        this.$page.props.user &&
        this.$page.props.user.id === this.package.author.id,
      form: this.$inertia.form({
        _method: "PATCH",
        name: this.package.name,
        private: this.package.private,
        description: this.package.description,
      }),
    };
  },
  components: {
    CloneButton,
    JetInput,
    JetLabel,
    JetInputError,
    JetButton,
  },
  methods: {
    updatePackage() {
      this.form.post(route("package.update", { package: this.package.id }), {
        preserveScroll: true,
      });
    },
  },
});
</script>
