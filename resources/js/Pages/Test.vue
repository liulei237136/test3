<template>
  <vxe-grid
    ref="xGrid"
    class="my-grid66"
    v-bind="gridOptions"
    @checkbox-change="checkboxChangeEvent"
    @checkbox-all="checkboxChangeEvent"
  >
    <!--使用 form 插槽-->
    <template #form>
      <vxe-form :data="demo1.formData" @submit="searchEvent">
        <vxe-form-item title="名称" field="name">
          <template #default="{ data }">
            <vxe-input v-model="data.name" placeholder="请输入名称" clearable></vxe-input>
          </template>
        </vxe-form-item>
        <vxe-form-item title="昵称" field="nickname">
          <template #default="{ data }">
            <vxe-input
              v-model="data.nickname"
              placeholder="请输入昵称"
              clearable
            ></vxe-input>
          </template>
        </vxe-form-item>
        <vxe-form-item title="性别" field="sex">
          <template #default="{ data }">
            <vxe-select v-model="data.sex" placeholder="请选择性别" clearable>
              <vxe-option value="1" label="女"></vxe-option>
              <vxe-option value="2" label="男"></vxe-option>
            </vxe-select>
          </template>
        </vxe-form-item>
        <vxe-form-item>
          <template #default>
            <vxe-button type="submit" status="primary">查询</vxe-button>
          </template>
        </vxe-form-item>
      </vxe-form>
    </template>

    <!--自定义插槽 toolbar buttons 插槽-->
    <template #toolbar_buttons>
      <vxe-form>
        <vxe-form-item>
          <template #default>
            <vxe-input placeholder="搜索"></vxe-input>
          </template>
        </vxe-form-item>
        <vxe-form-item>
          <template #default>
            <vxe-button status="primary">查询</vxe-button>
          </template>
        </vxe-form-item>
      </vxe-form>
    </template>

    <!--自定义插槽 toolbar tools 插槽-->
    <template #toolbar_tools>
      <vxe-input placeholder="搜索"></vxe-input>
    </template>

    <!--使用 top 插槽-->
    <template #top>
      <div class="alert-message">
        <i class="fa fa-exclamation-circle alert-message-icon"></i>
        <span class="alert-message-content">
          <div>自定义模板</div>
        </span>
      </div>
    </template>

    <!--自定义插槽-->
    <template #name_header>
      <div class="first-col">
        <div class="first-col-top">名称</div>
        <div class="first-col-bottom">类型</div>
      </div>
    </template>

    <template #num_default="{ row, rowIndex }">
      <template v-if="rowIndex === 2">
        <vxe-switch v-model="row.flag"></vxe-switch>
      </template>
      <template v-else-if="rowIndex === 3">
        <vxe-switch v-model="row.flag" open-label="开" close-label="关"></vxe-switch>
      </template>
      <template v-else>
        <span>{{ row.num1 }}</span>
      </template>
    </template>
    <template #num_footer="{ items, _columnIndex }">
      <span style="color: red">合计：{{ items[_columnIndex] }}</span>
    </template>

    <template #num1_default="{ row }">
      <span>￥{{ row.num1 }}元</span>
    </template>

    <template #num1_header="{ column }">
      <span>
        <i>@</i>
        <span style="color: red" @click="headerClickEvent">{{ column.title }}</span>
      </span>
    </template>

    <template #num1_footer="{ items, _columnIndex }">
      <span>
        <vxe-button status="primary" size="mini">按钮</vxe-button>
        <span>累计：{{ items[_columnIndex] }}</span>
      </span>
    </template>

    <template #num1_filter="{ column, $panel }">
      <div>
        <div v-for="(option, index) in column.filters" :key="index">
          <input
            type="type"
            v-model="option.data"
            @input="changeFilterEvent(evnt, option, $panel)"
          />
        </div>
      </div>
    </template>

    <template #num1_edit="{ row }">
      <input type="number" class="my-input" v-model="row.num1" />
    </template>

    <template #img1_default="{ row }">
      <img v-if="row.img1" :src="row.img1" style="width: 100px" />
      <span v-else>无</span>
    </template>

    <!--使用 bottom 插槽-->
    <template #bottom>
      <div class="alert-message">
        <i class="fa fa-exclamation-circle alert-message-icon"></i>
        <span class="alert-message-content">
          <div>自定义模板</div>
        </span>
      </div>
    </template>
  </vxe-grid>

  <vxe-modal v-model="demo1.showDetails" title="查看详情" width="800" height="400" resize>
    <template #default>
      <div v-if="demo1.selectRow" v-html="demo1.selectRow.html3"></div>
    </template>
  </vxe-modal>
</template>

<script>
import { defineComponent, reactive, ref } from "vue";
import { VXETable, VxeGridInstance, VxeGridProps, VxeTableEvents } from "vxe-table";

export default defineComponent({
  setup() {
    const xGrid = ref({});

    const addressClickEvent = (row) => {
      VXETable.modal.alert(`address点击事件：${row.address}`);
    };

    const demo1 = reactive({
      searchVal1: "",
      searchVal2: "",
      showDetails: false,
      selectRow: null,
      isAllChecked: false,
      isIndeterminate: false,
      selectRecords: [],
      formData: {
        name: "",
        nickname: "",
        sex: "",
      },
      tablePage: {
        totalResult: 8,
        currentPage: 1,
        pageSize: 10,
      },
    });

    const meanNum = (list, field) => {
      let count = 0;
      list.forEach((item) => {
        count += Number(item[field]);
      });
      return count / list.length;
    };

    const gridOptions = reactive({
      border: true,
      resizable: true,
      showFooter: true,
      height: 600,
      editConfig: {
        trigger: "click",
        mode: "cell",
        icon: "fa fa-pencil-square-o",
      },
      data: [
        {
          id: 10001,
          name: "Test1",
          nickname: "T1",
          role: "Develop",
          num1: "222",
          sex: "Man",
          age: 28,
          address: "Shenzhen",
          img1: "/vxe-table/static/other/img1.gif",
        },
        {
          id: 10002,
          name: "Test2",
          nickname: "T2",
          role: "Test",
          num1: "536",
          sex: "Women",
          age: 22,
          address: "Guangzhou",
          img1: "/vxe-table/static/other/img2.gif",
        },
        {
          id: 10003,
          name: "Test3",
          nickname: "T3",
          role: "PM",
          num1: "1000",
          sex: "Man",
          age: 32,
          address: "Shanghai",
          img1: "/vxe-table/static/other/img1.gif",
        },
        {
          id: 10004,
          name: "Test4",
          nickname: "T4",
          role: "Designer",
          num1: "424323",
          sex: "Women",
          age: 23,
          address: "Shenzhen",
          img1: "",
        },
        {
          id: 10005,
          name: "Test5",
          nickname: "T5",
          role: "Develop",
          num1: "253",
          sex: "Women",
          age: 30,
          address: "Shanghai",
          img1: "/vxe-table/static/other/img1.gif",
        },
        {
          id: 10006,
          name: "Test6",
          nickname: "T6",
          role: "Designer",
          num1: "555",
          sex: "Women",
          age: 21,
          address: "Shenzhen",
          img1: "/vxe-table/static/other/img2.gif",
        },
        {
          id: 10007,
          name: "Test7",
          nickname: "T7",
          role: "Test",
          num1: "11",
          sex: "Man",
          age: 29,
          address: "Shenzhen",
          img1: "",
        },
        {
          id: 10008,
          name: "Test8",
          nickname: "T8",
          role: "Develop",
          num1: "998",
          sex: "Man",
          age: 35,
          address: "Shenzhen",
          img1: "/vxe-table/static/other/img1.gif",
        },
      ],
      toolbarConfig: {
        custom: true,
        zoom:true,
        slots: {
          buttons: "toolbar_buttons",
          tools: "toolbar_tools",
        },
      },
      columns: [
        { type: "checkbox", width: 60 },
        {
          field: "name",
          title: "Name",
          width: 200,
          resizable: false,
          slots: { header: "name_header" },
        },
        { field: "age", title: "Age", width: 100 },
        {
          field: "num1",
          title: "Num1",
          showHeaderOverflow: true,
          filters: [{ data: "" }],
          editRender: { autofocus: ".my-input" },
          slots: {
            // 使用插槽模板渲染
            default: "num1_default",
            header: "num1_header",
            footer: "num1_footer",
            filter: "num1_filter",
            edit: "num1_edit",
          },
        },
        {
          field: "address",
          title: "Address",
          showOverflow: true,
          slots: {
            // 使用 JSX 渲染
            // default: ({ row }) => {
            //   return [
            //     <span style="color: blue" onClick={ () => addressClickEvent(row) }>自定义模板内容</span>
            //   ]
            // }
          },
        },
        { field: "img1", title: "图片路径", slots: { default: "img1_default" } },
      ],
      footerMethod({ columns, data }) {
        return [
          columns.map((column, index) => {
            if (index === 0) {
              return "平均";
            } else if (["num1", "age"].includes(column.property)) {
              return meanNum(data, column.property);
            }
            return null;
          }),
        ];
      },
    });

    const searchEvent = () => {
      VXETable.modal.alert("查询");
    };

    const headerClickEvent = () => {
      VXETable.modal.alert("头部点击事件");
    };

    const changeFilterEvent = (evnt, option, $panel) => {
      $panel.changeOption(evnt, !!option.data, option);
    };

    const showDetailEvent = (row) => {
      demo1.selectRow = row;
      demo1.showDetails = true;
    };

    const checkboxChangeEvent = ({ records }) => {
      const $grid = xGrid.value;
      demo1.isAllChecked = $grid.isAllCheckboxChecked();
      demo1.isIndeterminate = $grid.isAllCheckboxIndeterminate();
      demo1.selectRecords = records;
    };

    const changeAllEvent = () => {
      const $grid = xGrid.value;
      $grid.setAllCheckboxRow(demo1.isAllChecked);
      demo1.selectRecords = $grid.getCheckboxRecords();
    };

    return {
      xGrid,
      demo1,
      gridOptions,
      searchEvent,
      headerClickEvent,
      changeFilterEvent,
      showDetailEvent,
      checkboxChangeEvent,
      changeAllEvent,
    };
  },
});
</script>
