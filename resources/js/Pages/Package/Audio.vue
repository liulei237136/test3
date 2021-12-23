<template>
  <vxe-modal
    v-model="demo.showSaveModal"
    id="saveModal"
    width="800"
    height="360"
    min-width="460"
    min-height="320"
    show-zoom
    resize
    remember
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
            <vxe-input v-model="data.title" placeholder="" clearable></vxe-input>
          </template>
        </vxe-form-item>
        <vxe-form-item title="描述" field="description" span="24" :item-render="{}">
          <template #default="{ data }">
            <vxe-textarea
              v-model="data.description"
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
      <vxe-button content="插入空白行">
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
            @click="insertEmptyAt()"
            content="在选中行前插入"
          ></vxe-button>
        </template>
      </vxe-button>
      <vxe-button content="删除" @click="xGrid.removeCheckboxRow"></vxe-button>
      <!-- <vxe-button content="保存" @click="demo.showSaveModal = true"></vxe-button> -->
      <vxe-button content="保存" @click="onSave"></vxe-button>
    </template>
  </vxe-grid>
</template>
<script>
import { Inertia } from "@inertiajs/inertia";
import { defineComponent, onMounted, reactive, ref } from "vue";
import { VXETable, VxeGridInstance, VxeGridProps } from "vxe-table";
import XEUtils from "xe-utils";
import axios from "axios";

export default defineComponent({
  props: {
    package: Object,
    canEdit: Boolean,
    commit: Object,
    commits: Array,
  },
  setup(props, context) {
    const xGrid = ref({});

    const saveForm = ref({});

    const demo = reactive({
      filterAllString: "",
      audioList: [],
      showSaveModal: false,
      saveFormLoading: false,
      saveFormData: {
        title: "",
        description: "",
      },
      saveFormRules: {
        title: [
          { required: true, message: "请输入本次保存的名称" },
          { min: 3, max: 56, message: "长度在 3 到 56 个字符" },
        ],
        description: [{ max: 1000, message: "长度小于1000个字符" }],
      },
    });

    const onSave = async () => {
      const { insertRecords, updateRecords, removeRecords } = xGrid.value.getRecordset();
      //如果没有改变
      if (!insertRecords.length && !updateRecords.length && !removeRecords.length) {
        return await VXETable.modal.message({ content: "内容没有改动" });
      }
      demo.showSaveModal = true;
    };

    const saveModalFormSubmitEvent = async () => {
      demo.saveFormLoading = true;
      const { insertRecords, updateRecords, removeRecords } = xGrid.value.getRecordset();
      const removeAudioIds = [];
      const insertAudioIds = [];
      const unchangedAudioIds = [];
      let ids = [];
      //1清理需要'删除'和新增的
      removeRecords.forEach((record) => removeAudioIds.push(record.id));
      updateRecords.forEach((record) => {
        removeAudioIds.push(record.id);
        insertRecords.push(record);
      });
      console.log("insertRecords", insertRecords);

      //2.开始新增audio
      for (let record of insertRecords) {
        const data = new FormData();
        console.log("new record", record);
        record.file && data.append("file", record.file);
        record.name && data.append("name", record.name);
        record.book_name && data.append("book_name", record.book_name);
        record.audio_text && data.append("audio_text", record.audio_text);
        const result = await axios.post(
          route("package.audio.store", { package: props.package.id }),
          data,
          {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          }
        );
        console.log("result", result);
        insertAudioIds.push(result.data.data.id);
      }

      //3 开始计算ids 等于 原有audio去掉removeAudioIds 再加上 insertAudiosIds
      demo.audioList.forEach((audio) => {
        if (!removeAudioIds.includes(audio.id)) unchangedAudioIds.push(audio.id);
      });
      console.log("unchangedAudioIds", unchangedAudioIds);
      console.log("insertAudioIds", insertAudioIds);
      ids = unchangedAudioIds.concat(insertAudioIds);
      console.log(ids);

      try {
        const result = await axios.post(
          route("package.commit.store", { package: props.package.id }),
          {
            title: demo.saveFormData.title,
            description: demo.saveFormData.description,
            ids,
          }
        );
        await Inertia.get(
          route("package.show", {
            package: props.package.id,
            commit: result.data.data.id,
            tab: "audio",
          })
        );
      } catch (e) {
        console.log(e);
      }
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
      const matchMp3 = name.match("^([0-9]{1,8})\.mp3$");
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

    const commitButtons = props.commits.map((commit) => ({
      code: `commit_${commit.id}`,
      name: commit.title,
      type: "text",
    }));

    const gridOptions = reactive({
      border: true,
      resizable: true,
      showHeaderOverflow: true,
      showOverflow: true,
      highlightHoverRow: true,
      keepSource: true,
      id: "full_edit_1",
      height: 600,
      rowId: "id",
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
          //   {
          //     name: "历史保存",
          //     disabled: false,
          //     dropdowns: commitButtons,
          //   },
        ],
        save: true,
        refresh: true,
        print: true,
        zoom: true,
        custom: true,
      },

      columns: [
        { type: "checkbox", width: 60 },
        { type: "seq", width: 60 },
        {
          field: "name",
          title: "文件名",
          sortable: true,
          sortBy: nameSortBy,
          titleHelp: { message: "注意要加上文件后缀" },
          editRender: { name: "input", attrs: { placeholder: "请输入文件名" } },
          filters: [{ data: "" }],
          filterMethod: filterNameMethod,
          filterConfig: {},
          filterRender: { name: "$input" },
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
            const audioList = await getCommitAudio();
            resetAll();
            return audioList;
          },
          //   delete: ({ body }) => {
          //     return xGrid.value.removeCheckboxRow();
          //   },
          //   save: async (item) => {
          //     console.log(item);
          //     return;
          //   },
        },
      },
      checkboxConfig: {
        highlight: true,
        range: true,
      },
      editRules: {
        name: [{ min: 3, max: 50, message: "名称长度在 3 到 50 个字符" }],
      },
      editConfig: {
        trigger: "click",
        mode: "row",
        showStatus: true,
      },
    });

    const insertEmptyAt = async (index) => {
      const grid = xGrid.value;
      if (index === 0) {
        const { row: newRow } = await grid.insertAt({});
        await grid.setActiveCell(newRow, "name");
      } else if (index === -1) {
        const { row: newRow } = await grid.insertAt({}, -1);
        await grid.setActiveCell(newRow, "name");
      } else {
        const selectedRow = grid.getCheckboxRecords(true)[0];
        if (selectedRow) {
          const { row: newRow } = await grid.insertAt({}, selectedRow);
          await grid.setActiveCell(newRow, "name");
        }
      }
    };

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
          case "myImport":
            $grid.importData({
              mode: "insert",
            });
            break;
          case "myExport":
            $grid.openExport({
              mode: "all",
              modes: ["all", "selected"],
              original: true,
            });
        }
      },
    };

    onMounted(async () => {
      // 1. get commits
      //2. try to get commitId
      //3. use the id to get audios
      //   const r = url.parse(this.$inertia.url, true);
      //   console.log(r.query);
      //   demo.commits = await getCommits();
      //   console.log(item);
      //   getCommitAudio();
      //   const sexList = [
      //     { label: "女", value: "0" },
      //     { label: "男", value: "1" },
      //   ];
      //   const { formConfig, columns } = gridOptions;
      //   if (columns) {
      //     const sexColumn = columns[5];
      //     if (sexColumn && sexColumn.editRender) {
      //       sexColumn.editRender.options = sexList;
      //     }
      //   }
      //   if (formConfig && formConfig.items) {
      //     const sexItem = formConfig.items[4];
      //     if (sexItem && sexItem.itemRender) {
      //       sexItem.itemRender.options = sexList;
      //     }
      //   }
    });

    return {
      xGrid,
      gridOptions,
      demo,
      insertEmptyAt,
      getCommitAudio,
      resetAll,
      filterNameMethod,
      filterBookNameMethod,
      saveForm,
      onSave,
      saveModalFormSubmitEvent,
      gridEvents,
    };
  },
});
</script>
