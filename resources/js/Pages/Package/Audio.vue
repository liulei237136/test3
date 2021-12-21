<template>
  <vxe-grid ref="xGrid" v-bind="gridOptions">
    <template #toolbar_buttons>
      <vxe-input
        v-model="demo.filterAllString"
        placeholder="全表过滤"
        @change="onFilterAll"
      ></vxe-input>
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
      <vxe-button content="保存" @click="xGrid.commitProxy('save')"></vxe-button>
    </template>
  </vxe-grid>
</template>
<script>
import { defineComponent, onMounted, reactive, ref, Ref } from "vue";
import { VXETable, VxeGridInstance, VxeGridProps } from "vxe-table";
import XEUtils from "xe-utils";
import axios from "axios";

export default defineComponent({
  props: {
    package: Object,
    canEdit: Boolean,
    commitId: String,
  },
  setup(props) {
    const xGrid = ref({});

    const demo = reactive({
      filterAllString: "",
      commitId: props.commitId,
      commits: [],
    });

    const onFilterAll = () => {
      console.log("onFilterAll");
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
      rowId: "id",
      customConfig: {
        storage: true,
      },
      importConfig: {},
      printConfig: {},
      sortConfig: {
        trigger: "cell",
      },
      filterConfig: {},

      toolbarConfig: {
        slots: {
          buttons: "toolbar_buttons",
        },
        save: true,
        refresh: true,
        import: true,
        export: true,
        print: true,
        zoom: true,
        custom: true,
      },

      columns: [
        { type: "checkbox", width: 60 },
        { type: "seq", width: 60 },
        {
          field: "name",
          title: "Name",
          sortable: true,
          titleHelp: { message: "这里是Mp3文件的文件名" },
          editRender: { name: "input", attrs: { placeholder: "请输入文件名" } },
        },
      ],
      proxyConfig: {
        ajax: {
          // 当点击工具栏查询按钮或者手动提交指令 query或reload 时会被触发
          query: async ({ page, sorts, filters, form }) => {
            const audioList = await getCommitAudio(props.commitId);
            console.log(audioList);
            await resetAll();
            return audioList;
          },
          delete: ({ body }) => {
            return xGrid.value.removeCheckboxRow();
          },
          save: async (item) => {
            console.log(item.body);
            return;
          },
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

    const getCommitAudio = async (commitId) => {
      if (commitId) {
        console.log("commitId", commitId);
        const result = await axios(
          route("package.commit.show", { package: props.package.id, commit: commitId })
        );
        console.log(result.data.audio);
        return result.data.audio;
      }
      return [];
    };

    const getCommits = async () => {
      const result = await axios(
        route("package.commit.index", { package: props.package.id })
      );
      return result.data.commits;
    };

    const resetAll = () => {
      console.log("reset all");
    };

    onMounted(async () => {
      demo.commits = await getCommits();

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
      onFilterAll,
      insertEmptyAt,
      getCommitAudio,
      resetAll,
    };
  },
});
</script>
