<template>
  <vxe-grid ref="xGrid" v-bind="gridOptions" v-on="gridEvents">
    <template #source_audio="{ row }">
      <audio
        class="w-64 h-8"
        v-if="row.url"
        :src="row.url"
        @play="onAudioPlayEvent($event, row)"
        controls
      ></audio>
    </template>
  </vxe-grid>
</template>

<script>
import { defineComponent, onMounted, reactive, ref } from "vue";
import XEUtils from "xe-utils";

export default defineComponent({
  props: {
    audioList: Array,
  },

  setup(props, context) {
    const xGrid = ref({});

    const demo = reactive({
      audioList: [],
      playingAudio: {},
    });

    const onAudioPlayEvent = (e) => {
      const { audio } = demo.playingAudio;
      if (audio && audio !== e.target) {
        audio.pause();
        // audio.fastSeek(0);
      }
      demo.playingAudio = { audio: e.target };
    };

    const filterStringMethod = ({ value, option, cellValue, row, column }) => {
      return XEUtils.toValueString(cellValue).toLowerCase().indexOf(option.data) > -1;
    };

    const nameSortBy = ({ row, column }) => {
      const name = XEUtils.toValueString(row.name).trim();
      if (!name) return -1;
      //todo
      const matchMp3 = name.match("^([0-9]{1,8}).mp3$");
      if (matchMp3) {
        return parseInt(matchMp3[1]);
      } else {
        const matchNumber = name.match("^[0-9]{1,8}$");
        if (matchNumber) {
          return parseInt(matchNumber[1]);
        }
        return -1;
      }
    };

    const gridOptions = reactive({
      data: props.audioList,
      border: true,
      resizable: true,
      showHeaderOverflow: true,
      showOverflow: true,
      id: "audio_table" + Math.random(),
      highlightHoverRow: true,
      maxHeight: 400,
      rowId: "id",
      customConfig: {
        storage: true,
      },
      printConfig: {},
      sortConfig: {
        trigger: "cell",
      },
      filterConfig: {},

      toolbarConfig: {
        tools: [{ code: "myExport", name: "导出" }],
        print: true,
        zoom: true,
        custom: true,
      },

      columns: [
        {
          type: "checkbox",
          width: 40,
          className: ({ row }) =>
            row.status === "deleted" ? "bg-red-500" : "bg-green-500",
        },
        { type: "seq", width: 60 },
        {
          field: "file_name",
          title: "文件名",
          width: 160,
          sortable: true,
          sortBy: nameSortBy,
          titleHelp: { message: "注意要加上文件后缀" },
          filters: [{ data: "" }],
          filterMethod: filterStringMethod,
          filterConfig: {},
          filterRender: { name: "$input" },
        },
        //for export only
        { field: "file_path", title: "音频文件路径", visible: false },
        { field: "file_size", title: "音频文件大小(字节)", visible: false },
        {
          title: "播放",
          width: 300,
          slots: {
            default: "source_audio",
          },
        },
        {
          field: "book_name",
          title: "书名",
          titleHelp: { message: "书的名字，便于过滤和查找" },
          filters: [{ data: "" }],
          filterMethod: filterStringMethod,
          filterConfig: {},
          filterRender: { name: "$input" },
        },
        {
          field: "author.name",
          title: "作者",
          titleHelp: { message: "这行数据的作者" },
          filters: [{ data: "" }],
          filterMethod: filterStringMethod,
          filterConfig: {},
          filterRender: { name: "$input" },
        },
      ],
      //   proxyConfig: {
      // ajax: {
      //   // 当点击工具栏查询按钮或者手动提交指令 query或reload 时会被触发
      //   query: async ({ page, sorts, filters, form }) => {
      //     demo.audioList = props.audioList;
      //     resetAll();
      //     return demo.audioList;
      //   },
      // },
      //   },
      checkboxConfig: {
        highlight: true,
        range: true,
      },
    });

    const resetAll = () => {
      console.log("reset all");
    };

    const gridEvents = {
      toolbarToolClick({ code }) {
        const $grid = xGrid.value;
        switch (code) {
          case "myExport":
            $grid.exportData({
              type: "csv",
              mode: "all", //	current, selected, all
              original: true,
              columns: [
                { field: "name" },
                { field: "file_name" },
                { field: "file_path" },
                { field: "file_size" },
                { field: "book_name" },
              ],
            });
        }
      },
    };

    return {
      xGrid,
      gridOptions,
      demo,
      resetAll,
      filterStringMethod,
      gridEvents,
      onAudioPlayEvent,
    };
  },
});
</script>
