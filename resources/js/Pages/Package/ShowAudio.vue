<template>
  <content-layout>
    <vxe-grid ref="xGrid" v-bind="gridOptions" v-on="gridEvents">
      <template #toolbar_buttons>
        <vxe-pulldown ref="xDown" class="mr-4">
          <template #default>
            <vxe-input
              v-model="demo.filterCommitTitle"
              placeholder="历史保存"
              @focus="commitFocusEvent"
              @keyup="commitKeyupEvent"
            ></vxe-input>
          </template>
          <template #dropdown>
            <div class="my-dropdown">
              <div
                class="list-item"
                v-for="commit in demo.filteredCommitsList"
                :key="commit.id"
              >
                <Link
                  :to="
                    route('package.show', {
                      package: package.id,
                      commit: commit.id,
                      tab: 'audio',
                    })
                  "
                  :title="commit.description ? commit.description : commit.title"
                  >{{ commit.title }}</Link
                >
              </div>
            </div>
          </template>
        </vxe-pulldown>
      </template>

      <template #source_audio="{ row }">
        <audio
          v-if="row.url"
          :src="row.url"
          @play="onAudioPlayEvent($event, row)"
          controls
        ></audio>
      </template>
    </vxe-grid>
  </content-layout>
</template>

<style scoped>
.my-dropdown {
  padding: 4px;
  height: auto;
  max-height: 300px;
  min-width: 300px;
  max-width: 600px;
  overflow-y: auto;
  border-radius: 4px;
  border: 1px solid #dcdfe6;
  background-color: #fff;
}
.list-item {
  padding: 2px;
  line-height: 22px;
  font-size: 16px;
}
.list-item:hover {
  background-color: #f5f7fa;
}
</style>

<script>
import { Inertia } from "@inertiajs/inertia";
import { Link } from "@inertiajs/inertia-vue3";
import { defineComponent, onMounted, reactive, ref } from "vue";
import XEUtils from "xe-utils";
import axios from "axios";
import ContentLayout from "@/Layouts/ContentLayout.vue";

export default defineComponent({
  props: {
    package: Object,
    canEdit: Boolean,
    commit: Object,
    commits: Array,
  },
  components: {
    Link,
    ContentLayout,
  },
  setup(props, context) {
    const xGrid = ref({});

    const xDown = ref({});

    const demo = reactive({
      filterAllString: "",
      audioList: [],
      filterCommitTitle: "",
      filteredCommitsList: props.commits,
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

    const commitFocusEvent = () => {
      const $pulldown = xDown.value;
      $pulldown.showPanel();
    };

    const commitKeyupEvent = () => {
      demo.filteredCommitsList = demo.filterCommitTitle
        ? props.commits.filter(
            (commit) => commit.title.indexOf(demo.filterCommitTitle) > -1
          )
        : props.commits;
    };

    const commitSelectEvent = (commit) => {
      const $pulldown = xDown.value;
      //   demo1.value1 = item.label;
      $pulldown.hidePanel().then(() => {
        // demo1.list1 = data1;
        Inertia.get(
          route("package.show", {
            package: props.package.id,
            commit: commit.id,
            tab: "audio",
          })
        );
      });
    };
    const filterNameMethod = ({ value, option, cellValue, row, column }) => {
      return XEUtils.toValueString(cellValue).toLowerCase().indexOf(option.data) > -1;
    };

    const filterBookNameMethod = ({ value, option, cellValue, row, column }) => {
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
      border: true,
      resizable: true,
      showHeaderOverflow: true,
      showOverflow: true,
      highlightHoverRow: true,
      id: "full_edit_1",
      height: 600,
      rowId: "id",
      customConfig: {
        storage: true,
        checkMethod({ column }) {
          console.log(column);
          const field = column.property;
          if (field === "file_name" || field === "size") {
            return false;
          }
          return true;
        },
      },
      printConfig: {},
      sortConfig: {
        trigger: "cell",
      },
      filterConfig: {},

      toolbarConfig: {
        slots: {
          buttons: "toolbar_buttons",
        },
        tools: [{ code: "myExport", name: "导出" }],
        print: true,
        zoom: true,
        custom: true,
      },

      columns: [
        { type: "checkbox", width: 40 },
        { type: "seq", width: 60 },
        {
          field: "name",
          title: "文件名",
          width: 120,
          sortable: true,
          sortBy: nameSortBy,
          titleHelp: { message: "注意要加上文件后缀" },
          filters: [{ data: "" }],
          filterMethod: filterNameMethod,
          filterConfig: {},
          filterRender: { name: "$input" },
        },
        //for export only
        { field: "file_name", visible: false },
        { field: "size", visible: false },
        {
          title: "音频",
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
          filterMethod: filterBookNameMethod,
          filterConfig: {},
          filterRender: { name: "$input" },
        },
      ],
      proxyConfig: {
        ajax: {
          // 当点击工具栏查询按钮或者手动提交指令 query或reload 时会被触发
          query: async ({ page, sorts, filters, form }) => {
            const audioList = await getCommitAudio();
            resetAll();
            return audioList;
          },
        },
      },
      checkboxConfig: {
        highlight: true,
        range: true,
      },
    });

    const getCommitAudio = async () => {
      if (props.package && props.package.id && props.commit && props.commit.id) {
        const result = await axios(
          route("package.commit.audio", {
            package: props.package.id,
            commit: props.commit.id,
          })
        );
        console.log("result.data", result.data);
        demo.audioList = result.data.audio;
      } else {
        demo.audioList = [];
      }
      return demo.audioList;
    };

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
                { field: "size" },
                { field: "book_name" },
              ],
            });
        }
      },
    };

    return {
      xGrid,
      xDown,
      gridOptions,
      demo,
      getCommitAudio,
      resetAll,
      filterNameMethod,
      filterBookNameMethod,
      gridEvents,
      commitFocusEvent,
      commitKeyupEvent,
      commitSelectEvent,
      onAudioPlayEvent,
    };
  },
});
</script>
