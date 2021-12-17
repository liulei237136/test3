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
    </template>
  </vxe-grid>
</template>
<script >
import { defineComponent, onMounted, reactive, ref, Ref } from "vue";
import { VXETable, VxeGridInstance, VxeGridProps } from "vxe-table";
import XEUtils from "xe-utils";
import axios from "axios";

export default defineComponent({
  setup() {
    const xGrid = ref({});

    const demo = reactive({
      filterAllString: "",
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
        refresh: true,
        import: true,
        export: true,
        print: true,
        zoom: true,
        custom: true,
      },

      columns: [
        { type: "checkbox", title: "ID", width: 120 },
        {
          field: "name",
          title: "Name",
          sortable: true,
          titleHelp: { message: "名称必须填写！" },
          editRender: { name: "input", attrs: { placeholder: "请输入名称" } },
        },
        {
          field: "role",
          title: "Role",
          sortable: true,
          filters: [
            { label: "前端开发", value: "前端" },
            { label: "后端开发", value: "后端" },
            { label: "测试", value: "测试" },
            { label: "程序员鼓励师", value: "程序员鼓励师" },
          ],
          filterMultiple: false,
          editRender: { name: "input", attrs: { placeholder: "请输入角色" } },
        },
        {
          field: "email",
          title: "Email",
          width: 160,
          editRender: { name: "$input", props: { placeholder: "请输入邮件" } },
        },
        {
          field: "nickname",
          title: "Nickname",
          editRender: { name: "input", attrs: { placeholder: "请输入昵称" } },
        },
        {
          field: "sex",
          title: "Sex",
          filters: [
            { label: "男", value: "1" },
            { label: "女", value: "0" },
          ],
          editRender: {
            name: "$select",
            options: [],
            props: { placeholder: "请选择性别" },
          },
        },
        {
          field: "age",
          title: "Age",
          visible: false,
          sortable: true,
          editRender: {
            name: "$input",
            props: { type: "number", min: 1, max: 120 },
          },
        },
        {
          field: "amount",
          title: "Amount",
          formatter({ cellValue }) {
            return cellValue
              ? `￥${XEUtils.commafy(XEUtils.toNumber(cellValue), {
                  digits: 2,
                })}`
              : "";
          },
          editRender: {
            name: "$input",
            props: { type: "float", digits: 2, placeholder: "请输入数值" },
          },
        },
        {
          field: "updateDate",
          title: "Update Date",
          width: 160,
          visible: false,
          sortable: true,
          formatter({ cellValue }) {
            return XEUtils.toDateString(cellValue, "yyyy-MM-dd HH:ss:mm");
          },
        },
        {
          field: "createDate",
          title: "Create Date",
          width: 160,
          visible: false,
          sortable: true,
          formatter({ cellValue }) {
            return XEUtils.toDateString(cellValue, "yyyy-MM-dd");
          },
        },
      ],

      checkboxConfig: {
        labelField: "id",
        reserve: true,
        highlight: true,
        range: true,
      },
      editRules: {
        name: [
          { required: true, message: "app.body.valid.rName" },
          { min: 3, max: 50, message: "名称长度在 3 到 50 个字符" },
        ],
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

    onMounted(() => {
      const sexList = [
        { label: "女", value: "0" },
        { label: "男", value: "1" },
      ];
      const { formConfig, columns } = gridOptions;
      if (columns) {
        const sexColumn = columns[5];
        if (sexColumn && sexColumn.editRender) {
          sexColumn.editRender.options = sexList;
        }
      }
      if (formConfig && formConfig.items) {
        const sexItem = formConfig.items[4];
        if (sexItem && sexItem.itemRender) {
          sexItem.itemRender.options = sexList;
        }
      }
    });

    return {
      xGrid,
      gridOptions,
      demo,
      onFilterAll,
      insertEmptyAt,
    };
  },
});
</script>
