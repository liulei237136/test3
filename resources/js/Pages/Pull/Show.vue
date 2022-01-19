<template>
  <content-layout>
    <div class="max-w-7xl mx-auto">
      <h1 class="text-xl mb-2">{{ pull.title }}</h1>
    </div>
  </content-layout>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";
import { defineComponent, nextTick, onMounted, reactive, ref } from "vue";
import ContentLayout from "@/Layouts/ContentLayout.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import { useForm } from "@inertiajs/inertia-vue3";

export default defineComponent({
  props: {
    package: Object,
    canEdit: Boolean,
    pull: Object,
  },
  components: {
    Link,
    ContentLayout,
    JetInput,
    JetInputError,
  },
  setup(props, context) {
    const form = useForm({
      title: null,
      description: null,
    });
    const demo = reactive({});

    const onSubmit = () => {
      //   form.post(route("pull.store", { child: props.child.id }));
    };
    return {
      demo,
      form,
      onSubmit,
    };
  },
});
</script>
