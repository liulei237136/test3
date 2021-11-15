<template>
  <button
    type="button"
    id="select-audio"
    class="
      bg-purple-500
      text-white
      active:bg-purple-600
      font-bold
      uppercase
      text-xs
      px-4
      py-2
      rounded
      shadow
      hover:shadow-md
      outline-none
      focus:outline-none
      mr-1
      mb-1
      ease-linear
      transition-all
      duration-150
    "
  >
    {{ buttonText }}
  </button>
</template>

<script>
// Import the plugins
import Uppy from "@uppy/core";
import XHRUpload from "@uppy/xhr-upload";
import Dashboard from "@uppy/dashboard";
import zh from "@uppy/locales/lib/zh_CN";

// And their styles (for UI plugins)
// With webpack and `style-loader`, you can import them like this:
import "@uppy/core/dist/style.css";
import "@uppy/dashboard/dist/style.css";

export default {
  name: "AudioUploader",
  props: ["p", "buttonText"],
  mounted: function () {
    const csrf = document
      .querySelector('meta[name="csrf-token"]')
      .getAttribute("content");
    const uppy = new Uppy({
      id: "audioUploader",
      locale: zh,
      autoProceed: true,
      restrictions: {
        allowedFileTypes: [".mp3"],
      },
    })
      .use(Dashboard, {
        trigger: "#select-audio",
      })
      .use(XHRUpload, {
        endpoint: `/packages/${this.p.id}/audio`,
        formData: true,
        fieldName: "file",
        headers: {
          "X-CSRF-TOKEN": csrf,
        },
      });

    uppy.on("complete", (result) => {
      this.$inertia.replace(route("package.audio.edit", { package: this.p }));
      console.log(result);
      //   location =route('package.edit', {package: this.p})
    });
  },
};
</script>
