<template>
  <content-layout>
    <vxe-modal
      v-model="demo.showSaveModal"
      id="saveModal"
      width="800"
      height="360"
      min-width="460"
      min-height="320"
      show-zoom
      resize
      storage
      transfer
    >
      <template #title>
        <span>给本次保存起个名字</span>
      </template>
      <template #default>
        <vxe-form
          title-colon
          ref="saveForm"
          title-align="right"
          title-width="100"
          :data="demo.saveFormData"
          :rules="demo.saveFormRules"
          :loading="demo.saveFormloading"
          @submit="saveModalFormSubmitEvent"
        >
          <vxe-form-item title="名字" field="title" span="18" :item-render="{}">
            <template #default="{ data }">
              <vxe-input
                @change="saveForm.clearValidate('title')"
                v-model="data.title"
                placeholder=""
                clearable
              ></vxe-input>
            </template>
          </vxe-form-item>
          <vxe-form-item title="描述" field="description" span="24" :item-render="{}">
            <template #default="{ data }">
              <vxe-textarea
                v-model="data.description"
                @change="saveForm.clearValidate('description')"
                placeholder=""
                :autosize="{ minRows: 6, maxRows: 10 }"
                clearable
              ></vxe-textarea>
            </template>
          </vxe-form-item>
          <vxe-form-item align="center" span="24">
            <template #default>
              <vxe-button
                :disabled="demo.saveFormLoading"
                type="submit"
                status="primary"
                content="提交"
              ></vxe-button>
            </template>
          </vxe-form-item>
        </vxe-form>
      </template>
    </vxe-modal>

    <vxe-grid ref="xGrid" v-bind="gridOptions" v-on="gridEvents">
      <template #toolbar_buttons>
        <vxe-pulldown ref="xDown" class="mr-4">
          <template #default>
            <vxe-input
              v-model="demo.filterCommitTitle"
              :placeholder="commit ? commit.title : '还没有保存过'"
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
                    route('package.audio', {
                      package: package.id,
                      commit: commit.id,
                    })
                  "
                  :title="commit.title"
                  class="inline-block w-full"
                  >{{ commit.title }}</Link
                >
              </div>
            </div>
          </template>
        </vxe-pulldown>
        <!-- 保存 -->
        <vxe-button v-if="canEdit" content="保存" @click="onSave"></vxe-button>
        <!-- 插入 -->
        <vxe-button v-if="canEdit" content="插入空白行">
          <template #dropdowns>
            <vxe-button
              type="text"
              @click="insertEmptyAt(0)"
              content="在第一行插入"
            ></vxe-button>
            <vxe-button
              type="text"
              @click="insertEmptyAt(-1)"
              content="在最后一行插入"
            ></vxe-button>
            <vxe-button
              type="text"
              @click="insertEmptyBeforeSelected()"
              content="选中行前插入"
            ></vxe-button>
          </template>
        </vxe-button>
        <vxe-button v-if="canEdit" content="插入音频">
          <template #dropdowns>
            <vxe-button
              type="text"
              @click="insertAudioAt(0)"
              content="在第一行插入"
            ></vxe-button>
            <vxe-button
              type="text"
              @click="insertAudioAt(-1)"
              content="在最后一行插入"
            ></vxe-button>
            <vxe-button
              type="text"
              @click="insertAudioBeforeSelected()"
              content="选中行前插入"
            ></vxe-button>
          </template>
        </vxe-button>
        <!-- 删除 -->
        <vxe-button
          v-if="canEdit"
          content="删除"
          @click="xGrid.removeCheckboxRow"
        ></vxe-button>
      </template>

      <template #source_audio="{ row }">
        <audio
          v-if="row.url"
          :src="row.url"
          @play="onAudioPlayEvent($event, row)"
          controls
          preload="auto"
          autobuffer
        ></audio>
      </template>
      <template #local_audio="{ row }">
        <audio
          v-if="row.localAudioUrl"
          :src="row.localAudioUrl"
          @play="onAudioPlayEvent($event, row)"
          controls
        ></audio>
      </template>
      <template #record_audio="{ row }">
        <div class="flex items-center">
          <audio-recorder :row="row" :demo="demo" class="mr-1"></audio-recorder>
          <audio
            v-if="row.recordUrl"
            :src="row.recordUrl"
            @play="onAudioPlayEvent($event, row)"
            controls
          ></audio>
        </div>
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
import { defineComponent, nextTick, onMounted, reactive, ref } from "vue";
import { VXETable, VxeGridInstance, VxeGridProps } from "vxe-table";
import XEUtils from "xe-utils";
import axios from "axios";
import ContentLayout from "@/Layouts/ContentLayout.vue";

import AudioRecorder from "./AudioRecorder.vue";

export default defineComponent({
  props: {
    package: Object,
    canEdit: Boolean,
    commit: Object,
    commits: Array,
  },
  components: {
    Link,
    AudioRecorder,
    ContentLayout,
  },
  setup(props, context) {
    const xGrid = ref({});

    const saveForm = ref({});

    const xDown = ref({});

    const demo = reactive({
      filterAllString: "",
      audioList: [],
      filterCommitTitle: "",
      filteredCommitsList: props.commits,
      showSaveModal: false,
      saveFormLoading: false,
      saveFormData: {
        title: "",
        description: "",
      },
      saveFormRules: {
        title: [
          { required: true, message: "请输入本次保存的名称" },
          { min: 3, max: 256, message: "长度在 3 到 256 个字符" },
        ],
        description: [{ max: 1000, message: "长度小于1000个字符" }],
      },
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

    const onSave = async () => {
      const { insertRecords, updateRecords, removeRecords } = xGrid.value.getRecordset();
      // 如果没有改变
      if (!insertRecords.length && !updateRecords.length && !removeRecords.length) {
        return await VXETable.modal.message({ content: "内容没有改动" });
      }
      demo.showSaveModal = true;
    };

    const saveModalFormSubmitEvent = async () => {
      // 先验证是否有错误
      const errMap = await saveForm.value.validate();
      if (errMap) return;

      demo.saveFormLoading = true;
      const { insertRecords, updateRecords, removeRecords } = xGrid.value.getRecordset();
      const removeAudioIds = [];
      const insertAudioIds = [];
      const unchangedAudioIds = [];
      let audio_ids = [];
      //1清理需要'删除'和新增的
      removeRecords.forEach((record) => removeAudioIds.push(record.id));
      updateRecords.forEach((record) => {
        removeAudioIds.push(record.id);
        insertRecords.push(record);
      });

      //2.开始新增audio
      for (let record of insertRecords) {
        const data = new FormData();
        // console.log("new record", record);
        //优先级 recordFile > localFile
        const fileToUpload = record.audioFile || record.localFile;
        if (fileToUpload) {
          data.append("file", fileToUpload);
        }
        record.file_name && data.append("file_name", record.file_name);
        record.book_name && data.append("book_name", record.book_name);
        record.original_text && data.append("original_text", record.original_text);
        record.file_path && data.append("file_path", record.file_path);
        record.file_size && data.append("file_size", record.file_size);
        const result = await axios.post(route("audio.store"), data, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });
        insertAudioIds.push(result.data.data.id);
      }

      //3 开始计算ids 等于 原有audio去掉removeAudioIds 再加上 insertAudiosIds
      demo.audioList.forEach((audio) => {
        if (!removeAudioIds.includes(audio.id)) unchangedAudioIds.push(audio.id);
      });
      //   console.log("unchangedAudioIds", unchangedAudioIds);
      //   console.log("insertAudioIds", insertAudioIds);
      audio_ids = unchangedAudioIds.concat(insertAudioIds);
      console.log(audio_ids);

      await Inertia.post(
        route("package.commit.store", { package: props.package.id }),
        {
          title: demo.saveFormData.title,
          description: demo.saveFormData.description,
          audio_ids: JSON.stringify(audio_ids),
        },
        {
          replace: true,
          preserveState: false,
        }
      );
    };

    const filterNameMethod = ({ value, option, cellValue, row, column }) => {
      return XEUtils.toValueString(cellValue).toLowerCase().indexOf(option.data) > -1;
    };

    const filterBookNameMethod = ({ value, option, cellValue, row, column }) => {
      return XEUtils.toValueString(cellValue).toLowerCase().indexOf(option.data) > -1;
    };

    const nameSortBy = ({ row, column }) => {
      const file_name = XEUtils.toValueString(row.file_name).trim();
      if (!file_name) return -1;
      //todo
      const matchMp3 = file_name.match("^([0-9]{1,8}).mp3$");
      if (matchMp3) {
        return parseInt(matchMp3[1]);
      } else {
        const matchNumber = file_name.match("^[0-9]{1,8}$");
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
      keepSource: true,
      id: "full_edit_1",
      height: 600,
      rowKey: true,
      customConfig: {
        storage: true,
      },
      importConfig: {
        mode: "covering",
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
        tools: [
          { code: "myImport", name: "导入" },
          { code: "myExport", name: "导出" },
        ],
        save: true,
        refresh: true,
        print: true,
        zoom: true,
        custom: true,
      },

      columns: [
        { type: "checkbox", width: 40 },
        { type: "seq", width: 60 },
        {
          field: "file_name",
          title: "音频文件名",
          width: 210,
          sortable: true,
          sortBy: nameSortBy,
          titleHelp: { message: "注意要加上文件后缀" },
          editRender: { name: "input", attrs: { placeholder: "请输入文件名" } },
          filters: [{ data: "" }],
          filterMethod: filterNameMethod,
          filterConfig: {},
          filterRender: { name: "$input" },
        },
        //for export only
        { field: "file_path", title: "音频文件路径", visible: false },
        { field: "file_size", title: "音频文件大小", visible: false },
        {
          title: "音频",
          width: 210,
          slots: {
            default: "source_audio",
          },
        },
        {
          title: "本次插入音频",
          width: 210,
          slots: {
            default: "local_audio",
          },
        },
        {
          title: "本次录音",
          width: 370,
          slots: {
            default: "record_audio",
          },
        },
        {
          field: "book_name",
          title: "书名",
          titleHelp: { message: "书的名字，便于过滤和查找" },
          editRender: { name: "input", attrs: { placeholder: "请输入书名" } },
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
            demo.audioList = await getCommitAudio();
            resetAll();
            return demo.audioList;
          },
        },
      },
      checkboxConfig: {
        highlight: true,
        range: true,
      },
      editRules: {
        file_name: [{ max: 225, message: "名称长度最长 225 个字符" }],
      },
      editConfig: {
        trigger: "click",
        mode: "row",
        showStatus: true,
      },
    });

    const insertEmptyAt = async (index) => {
      const grid = xGrid.value;
      const { row } = await grid.insertAt({}, index);
      await grid.setActiveCell(row, "file_name");
    };

    const insertEmptyBeforeSelected = async () => {
      const grid = xGrid.value;
      const selectedRows = grid.getCheckboxRecords(true);
      if (selectedRows.length === 0) {
        return VXETable.modal.message({
          content: "请选中一行",
          status: "warning",
        });
      } else if (selectedRows.length > 1) {
        return VXETable.modal.message({
          content: "勾选了多行，请只选一行",
          status: "warning",
        });
      } else {
        await insertEmptyAt(selectedRows[0]);
      }
    };

    const insertAudioAt = async (index) => {
      const grid = xGrid.value;
      const { files } = await grid.readFile({ multiple: true });
      let fileCount = files.length;
      if (fileCount === 0) return;
      let rows = [];
      for (let file of files) {
        rows.push({
          name: file.name,
          localFile: file,
        });
      }
      if (index === 0) {
        rows.reverse();
      }
      for (let j = 0; j < fileCount; j++) {
        const item = rows[j];
        const { row: currentRow } = await grid.insertAt(item, index);
        if (j === fileCount - 1) {
          //todo 根据条件选出setActiveCell row
          await grid.setActiveCell(currentRow, "name");
        }
      }
    };

    const insertAudioBeforeSelected = async () => {
      const grid = xGrid.value;
      const selectedRows = grid.getCheckboxRecords(true);
      if (selectedRows.length === 0) {
        return VXETable.modal.message({
          content: "请选中一行",
          status: "warning",
        });
      } else if (selectedRows.length > 1) {
        return VXETable.modal.message({
          content: "勾选了多行，请只选一行",
          status: "warning",
        });
      } else {
        await insertAudioAt(selectedRows[0]);
      }
    };

    const getCommitAudio = async () => {
      if (!props.commit) {
        return [];
      } else {
        const result = await axios(
          route("commit.audio", {
            commit: props?.commit?.id,
          })
        );
        return result.data.audio_list;
      }
    };

    const resetAll = () => {
      //   console.log("reset all");
    };

    const gridEvents = {
      toolbarToolClick({ code }) {
        const $grid = xGrid.value;
        switch (code) {
          case "myImport":
            $grid.importData({
              mode: "insert",
              afterImportMethod: () => {
                // console.log($grid.getRecordset());
              },
            });
            // $grid.openImport();
            break;
          case "myExport":
            const { insertRecords, removeRecords, updateRecords } = $grid.getRecordset();
            if (insertRecords.length || removeRecords.length || updateRecords.length) {
              return VXETable.modal.alert({
                content: "当前有未保存的改动，请先保存或撤销后再导出",
              });
            }
            $grid.exportData({
              type: "csv",
              mode: "all", //	urrent, selected, all
              original: true,
              columns: [
                { field: "file_name" },
                { field: "file_path" },
                { field: "file_size" },
                { field: "book_name" },
              ],
            });
        }
      },
    };

    onMounted(async () => {});

    return {
      xGrid,
      xDown,
      gridOptions,
      demo,
      insertEmptyAt,
      insertAudioAt,
      insertEmptyBeforeSelected,
      insertAudioBeforeSelected,
      getCommitAudio,
      resetAll,
      filterNameMethod,
      filterBookNameMethod,
      saveForm,
      onSave,
      saveModalFormSubmitEvent,
      gridEvents,
      commitFocusEvent,
      commitKeyupEvent,
      onAudioPlayEvent,
    };
  },
});
</script>
