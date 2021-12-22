<template></template>
<script>
import { defineComponent, onMounted, reactive, ref, Ref } from "vue";
import { VXETable, VxeGridInstance, VxeGridProps } from "vxe-table";
import XEUtils from "xe-utils";
import axios from "axios";
// import Url from "url-parse";
import url from "url";

export default defineComponent({
  props: {
    package: Object,
    canEdit: Boolean,
    commit: Object,
    commits: Array,
  },
  setup(props, context) {
    const xGrid = ref({});

    const demo = reactive({
      filterAllString: "",
      audioList: [],
    });

    const onFilterAll = () => {
      console.log("onFilterAll");
    };

    const filterNameMethod = ({ value, option, cellValue, row, column }) => {
      //   console.log(s);
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
          sortBy: nameSortBy,
          titleHelp: { message: "这里是Mp3文件的文件名" },
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
          delete: ({ body }) => {
            return xGrid.value.removeCheckboxRow();
          },
          save: async (item) => {
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

    // const filterNameMethod = ({ value, option, cellValue, row, column }) => {

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
      onFilterAll,
      insertEmptyAt,
      getCommitAudio,
      resetAll,
      filterNameMethod,
    };
  },
});
</script>
