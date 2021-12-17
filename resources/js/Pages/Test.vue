<template>
<<<<<<< HEAD
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
=======
  <vxe-toolbar>
    <template #buttons>
      <vxe-button icon="fa fa-plus" @click="insertEvent()">新增</vxe-button>
    </template>
  </vxe-toolbar>

  <vxe-table
    border
    resizable
    show-overflow
    ref="xTable"
    height="500"
    :loading="demo1.loading"
    :data="demo1.tableData"
    :edit-config="{ trigger: 'click', mode: 'cell' }"
  >
    <vxe-column type="seq" width="60"></vxe-column>
    <vxe-column
      field="name"
      title="Name"
      :edit-render="{ autofocus: '.my-input', autoselect: true }"
    >
      <template #edit="{ row }">
        <vxe-input v-model="row.name" type="text" class="my-input"></vxe-input>
      </template>
    </vxe-column>
    <vxe-column field="role" title="Role" :edit-render="{}">
      <template #edit="{ row }">
        <vxe-input
          v-model="row.role"
          type="text"
          placeholder="请输入昵称"
        ></vxe-input>
      </template>
    </vxe-column>
    <vxe-column field="sex" title="Sex" :edit-render="{}">
      <template #default="{ row }">
        <span>{{ formatSex(row.sex) }}</span>
      </template>
      <template #edit="{ row }">
        <vxe-select v-model="row.sex" transfer>
          <vxe-option
            v-for="item in demo1.sexList"
            :key="item.value"
            :value="item.value"
            :label="item.label"
          ></vxe-option>
        </vxe-select>
      </template>
    </vxe-column>
    <vxe-column field="sex2" title="多选下拉" :edit-render="{}">
      <template #default="{ row }">
        <span>{{ formatMultiSex(row.sex2) }}</span>
      </template>
      <template #edit="{ row }">
        <vxe-select v-model="row.sex2" multiple transfer>
          <vxe-option
            v-for="item in demo1.sexList"
            :key="item.value"
            :value="item.value"
            :label="item.label"
          ></vxe-option>
        </vxe-select>
      </template>
    </vxe-column>
    <vxe-column field="num6" title="Number" :edit-render="{}">
      <template #edit="{ row }">
        <vxe-input
          v-model="row.num6"
          type="number"
          placeholder="请输入数值"
        ></vxe-input>
      </template>
    </vxe-column>
    <vxe-column field="date12" title="Date" :edit-render="{}">
      <template #edit="{ row }">
        <vxe-input
          v-model="row.date12"
          type="date"
          placeholder="请选择日期"
          transfer
        ></vxe-input>
      </template>
    </vxe-column>
    <vxe-column field="date13" title="Week" :edit-render="{}">
      <template #edit="{ row }">
        <vxe-input
          v-model="row.date13"
          type="week"
          placeholder="请选择日期"
          transfer
        ></vxe-input>
      </template>
    </vxe-column>
    <vxe-column field="address" title="Address" :edit-render="{}">
      <template #edit="{ row }">
        <vxe-input v-model="row.address" type="text"></vxe-input>
      </template>
    </vxe-column>
    <!-- <vxe-column title="操作" width="160">
      <template #default="{ row }">
        <template v-if="$refs.xTable.isActiveByRow(row)">
          <vxe-button @click="saveRowEvent(row)">保存</vxe-button>
          <vxe-button @click="cancelRowEvent(row)">取消</vxe-button>
        </template>
        <template v-else>
          <vxe-button @click="editRowEvent(row)">编辑</vxe-button>
        </template>
      </template>
    </vxe-column> -->
  </vxe-table>

  <vxe-toolbar>
    <template #buttons>
      <vxe-button icon="fa fa-plus" @click="insertEvent()">新增</vxe-button>
    </template>
  </vxe-toolbar>
</template>

<script>
import { defineComponent, nextTick, reactive, ref } from "vue";
import { VXETable, VxeTableInstance } from "vxe-table";

export default defineComponent({
  setup() {
    const xTable = ref({});

    const demo1 = reactive({
      loading: false,
      trigger: "click",
      tableData: [
>>>>>>> deb965a636cea90f602797ba2047f34742876f11
        {
          id: 10001,
          name: "Test1",
          nickname: "T1",
          role: "Develop",
<<<<<<< HEAD
          num1: "222",
          sex: "Man",
          age: 28,
          address: "Shenzhen",
          img1: "/vxe-table/static/other/img1.gif",
=======
          sex: "0",
          sex2: ["0"],
          num1: 40,
          age: 28,
          address: "Shenzhen",
          date12: "",
          date13: "",
>>>>>>> deb965a636cea90f602797ba2047f34742876f11
        },
        {
          id: 10002,
          name: "Test2",
          nickname: "T2",
<<<<<<< HEAD
          role: "Test",
          num1: "536",
          sex: "Women",
          age: 22,
          address: "Guangzhou",
          img1: "/vxe-table/static/other/img2.gif",
=======
          role: "Designer",
          sex: "1",
          sex2: ["0", "1"],
          num1: 20,
          age: 22,
          address: "Guangzhou",
          date12: "",
          date13: "2020-08-20",
>>>>>>> deb965a636cea90f602797ba2047f34742876f11
        },
        {
          id: 10003,
          name: "Test3",
          nickname: "T3",
<<<<<<< HEAD
          role: "PM",
          num1: "1000",
          sex: "Man",
          age: 32,
          address: "Shanghai",
          img1: "/vxe-table/static/other/img1.gif",
=======
          role: "Test",
          sex: "0",
          sex2: ["1"],
          num1: 200,
          age: 32,
          address: "Shanghai",
          date12: "2020-09-10",
          date13: "",
>>>>>>> deb965a636cea90f602797ba2047f34742876f11
        },
        {
          id: 10004,
          name: "Test4",
          nickname: "T4",
          role: "Designer",
<<<<<<< HEAD
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
=======
          sex: "1",
          sex2: ["1"],
          num1: 30,
          age: 23,
          address: "Shenzhen",
          date12: "",
          date13: "2020-12-04",
        },
      ],
      sexList: [
        { label: "", value: "" },
        { label: "男", value: "1" },
        { label: "女", value: "0" },
      ],
    });

    const formatSex = (value) => {
      if (value === "1") {
        return "男";
      }
      if (value === "0") {
        return "女";
      }
      return "";
    };

    const formatMultiSex = (values) => {
      if (values) {
        return values.map((val) => formatSex(val)).join(",");
      }
      return "";
    };

    const isActiveStatus = (row) => {
      const $table = xTable.value;
      return $table.isActiveByRow(row);
    };

    const editRowEvent = (row) => {
      const $table = xTable.value;
      $table.setActiveRow(row);
    };

    const saveRowEvent = () => {
      const $table = xTable.value;
      $table.clearActived().then(() => {
        demo1.loading = true;
        setTimeout(() => {
          demo1.loading = false;
          VXETable.modal.message({ content: "保存成功！", status: "success" });
        }, 300);
      });
    };

    const cancelRowEvent = (row) => {
      const $table = xTable.value;
      $table.clearActived().then(() => {
        // 还原行数据
        $table.revertData(row);
      });
    };

    const insertEvent = () => {
      const newRow = { name: "" };
      demo1.tableData = [newRow].concat(demo1.tableData);
      xTable.value.setActiveCell(newRow, "name").then(() => {
        setTimeout(() => {
          //   nextTick(() => {
          xTable.value.scrollToRow(newRow);
          //   });
        }, 0);
      });
      //   demo1.trigger = "dblclick";
    };

    return {
      xTable,
      demo1,
      formatSex,
      formatMultiSex,
      isActiveStatus,
      editRowEvent,
      saveRowEvent,
      cancelRowEvent,
      insertEvent,
>>>>>>> deb965a636cea90f602797ba2047f34742876f11
    };
  },
});
</script>
