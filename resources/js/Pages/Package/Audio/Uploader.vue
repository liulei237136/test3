<template>
  <button
    type="button"
    id="select-audio"
    class="
      inline-flex
      items-center
      px-3
      h-11
      font-medium
      text-gray-700
      bg-white
      rounded
      shadow-sm
      lg:h-7
      lg:text-sm
    "
  >
    添加音频文件
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
  props: ['p'],

  emits: ["closed"],
  mounted: function () {
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
        // hideUploadButton: false,
      })
      .use(XHRUpload, {
        endpoint: `/packages/${this.p.id}/audio`,
        formData: true,
        fieldName: "file",
        headers: {
          "X-CSRF-TOKEN": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
        },
      });

    uppy.on("complete", (result) => {
      console.log("successful files:", result.successful);
      console.log("failed files:", result.failed);
    });

    uppy.on("dashboard:modal-closed", function () {
      window.location.reload();
      //   this.$emit("closed");
    //   this.$inertia.reload();
    });
  },
};
</script>
