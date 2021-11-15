<template>
  <form @submit.prevent="updatePackage" class="ml-2">
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

    <!-- category -->
    <div class="mb-5">
      <jet-label for="category" value="分类" />
      <div>
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
          <option value="毛毛虫">毛毛虫</option>
          <option value="卷之友">卷之友</option>
        </select>
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

    <audio-table :package="p"></audio-table>

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
  components: {
    JetInput,
    JetLabel,
    JetInputError,
    JetButton,
  },
  data() {
    return {
      p: this.package,
      form: this.$inertia.form({
        _method: "PATCH",
        name: this.package.name,
        category: this.package.category,
        description: this.package.description,
      }),
    };
  },
});
</script>
