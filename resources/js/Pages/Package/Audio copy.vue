<template>
<<<<<<< HEAD
  <vxe-modal
    v-model="showModal"
    @show="onModalShow"
    :show-header="false"
    width="200"
    height="60"
    :content="modalContent"
  >
  </vxe-modal>

  <!-- <vxe-toolbar export :refresh="{ query: findList }"> -->
  <vxe-toolbar>
    <template #buttons>
      <vxe-input
        v-model="dataReactive.filter"
        type="search"
        placeholder="试试全表搜索"
        @keyup="search"
      ></vxe-input>
      <vxe-button>
        <template #default>新增空白行</template>
        <template #dropdowns>
          <vxe-button type="text" @click="insertEvent(null)">从第一行插入</vxe-button>
          <vxe-button type="text" @click="insertEvent(-1)">从最后插入</vxe-button>
          <vxe-button type="text" @click="insertEvent($refs.xTable.getData(100))"
            >插入到选中行</vxe-button
          >
        </template>
      </vxe-button>
      <vxe-button>
        <template #default>删除操作</template>
        <template #dropdowns>
          <vxe-button type="text" @click="$refs.xTable.removeCheckboxRow()"
            >删除选中</vxe-button
          >
          <vxe-button type="text" @click="$refs.xTable.remove($refs.xTable.getData(0))"
            >删除第一行</vxe-button
          >
          <vxe-button
            type="text"
            @click="
              $refs.xTable.remove($refs.xTable.getData($refs.xTable.getData().length - 1))
            "
            >删除最后一行</vxe-button
          >
          <vxe-button type="text" @click="$refs.xTable.remove($refs.xTable.getData(100))"
            >删除第 100 行</vxe-button
          >
        </template>
      </vxe-button>
      <vxe-button @click="getInsertEvent">获取新增</vxe-button>
      <vxe-button @click="getRemoveEvent">获取删除</vxe-button>
      <vxe-button @click="getUpdateEvent">获取修改</vxe-button>
    </template>
  </vxe-toolbar>

  <!-- <vxe-toolbar perfect>
      <vxe-button
        v-if="canEdit"
        icon="fa fa-plus"
        status="perfect"
        @click="insertEmptyRow"
        >添加空白行</vxe-button
      >

      <vxe-button
        v-if="canEdit"
        icon="fa fa-plus"
        status="perfect"
        @click="$refs.insertAudio.click()"
        >添加MP3</vxe-button
      >
      <input
        v-if="canEdit"
        type="file"
        ref="insertAudio"
        accept=".mp3"
        hidden
        multiple
        @change="insertAudio"
      />
      <vxe-button
        v-if="canEdit"
        icon="fa fa-trash-o"
        status="perfect"
        @click="deleteChecked"
        >批量删除</vxe-button
      >
            <vxe-button v-if="canEdit" icon="fa fa-save" status="perfect" @click="saveEvent"
        >保存</vxe-button
      >
    </template>
  </vxe-toolbar> -->

  <vxe-table
    border
    resizable
    show-overflow
    highlight-hover-row
    highlight-hover-column
    :checkbox-config="{ checkField: 'checked', highlight: true, range: true }"
    :row-key="true"
    empty-text="还没有添加音频哦！"
    :loading="data.loading"
    :data="tableDataFiltered"
    height="600"
    ref="xTable"
    :edit-config="
      canEdit
        ? {
            trigger: 'click',
            mode: 'cell',
            showStatus: false,
          }
        : null
    "
  >
    <vxe-column v-if="canEdit" type="checkbox" width="60"></vxe-column>
    <vxe-column type="seq" width="80"></vxe-column>
    <vxe-column
      width="150"
      field="name"
      title="文件名"
      :edit-render="{
        name: 'input',
        attrs: { type: 'text' },
        events: {
          change: onInputChange,
        },
      }"
    >
    </vxe-column>
    <vxe-column :title="'播放' + (canEdit ? '和重录' : '')" :width="canEdit ? 520 : 300">
      <template #default="{ row }">
        <audio-recorder :row="row" :can-edit="canEdit"></audio-recorder>
      </template>
    </vxe-column>
    <vxe-column
      width="200"
      field="book_name"
      title="所属书名"
      :edit-render="{
        name: 'input',
        attrs: { type: 'text' },
        events: {
          change: onInputChange,
        },
      }"
    >
    </vxe-column>
    <vxe-column
      field="audio_text"
      title="音频文字"
      :edit-render="{
        name: 'input',
        attrs: { type: 'text' },
        events: {
          change: onInputChange,
        },
      }"
    >
    </vxe-column>
    <vxe-column v-if="canEdit" title="操作" width="100" show-overflow>
      <template #default="{ row }">
        <vxe-button icon="fa fa-trash" status="perfect" @click="deleteRow(row)">
          删除
        </vxe-button>
      </template>
    </vxe-column>
  </vxe-table>
</template>

<style lang="css" scoped>
.keyword-lighten {
  color: #000;
  background-color: #ffff00;
}
</style>

<script>
import { defineComponent, reactive, nextTick, ref } from "vue";
import XEUtils from "xe-utils";

import Icon from "@/Components/Icon";
import AudioRecorder from "./AudioRecorder";

import { v4 as uuidv4 } from "uuid";

export default defineComponent({
  setup() {
    const dataReactive = reactive({
      loading: false,
      filter: "",
      initTableData: [],
      tableData: [],
    });

    const xTable = ref({});

    const search = () => {
      const filter = XEUtils.toValueString(data.filter).trim().toLowerCase();
      if (filter) {
        const filterRE = new RegExp(filter, "gi");
        const searchProps = ["name", "book_name", "audio_text"];
        const rest = data.initTableData.filter((item) =>
          searchProps.some(
            (key) => XEUtils.toValueString(item[key]).toLowerCase().indexOf(filter) > -1
          )
        );
        xTable.loadData(
          rest.map((row) => {
            const item = Object.assign({}, row);
            searchProps.forEach((key) => {
              item[key] = XEUtils.toValueString(item[key]).replace(
                filterRE,
                (match) => `<span class="keyword-lighten">${match}</span>`
              );
            });
            return item;
          })
        );
      } else {
        xTable.loadData(data.initTableData);
      }
    };

    const loadInitTableData = () => {
      data.loading = true;
      return axios
        .get(route("package.audio", { package: this.package.id }))
        .then((res) => {
          dataReactive.initTableData = res.data;
          search();
        })
        .catch((err) => console.log(err))
        .finally(() => {
          data.loading = false;
        });
    };

    const insertEvent = (row) => {
      const $table = xTable.value;
      const record = {
        checked: false,
      };
      $table.insertAt(record, row).then(({ row }) => {
        $table.setActiveRow(row);
      });
    };

    const getInsertEvent = () => {
      const $table = xTable.value;
      const insertRecords = $table.getInsertRecords();
      VXETable.modal.alert(insertRecords.length);
    };

    const getRemoveEvent = () => {
      const $table = xTable.value;
      const removeRecords = $table.getRemoveRecords();
      VXETable.modal.alert(removeRecords.length);
    };

    const getUpdateEvent = () => {
      const $table = xTable.value;
      const updateRecords = $table.getUpdateRecords();
      VXETable.modal.alert(updateRecords.length);
    };

    nextTick(loadInitTableData);

    return {
      dataReactive,
      search,
      loadInitTableData,
      insertEvent,
      getInsertEvent,
      getRemoveEvent,
      getUpdateEvent,
    };
  },
  props: {
    package: Object,
  },
  components: {
    Icon,
    AudioRecorder,
  },
  data() {
    return {
      canEdit:
        this.$page.props.user && this.$page.props.user.id === this.package.author.id,
      initAudio: [],
      audioList: [],
      loading: false,
      showModal: false,
      modalContent: "",
      selectRow: null,
      deletedRow: [], //监控被删除的原生行
      filterItem: "",
    };
  },
  methods: {
    deleteChecked() {
      const selectRecords = this.$refs.xTable.getCheckboxRecords();
      selectRecords.forEach((row) => this.deleteRow(row));
    },
    //监控是否非新增row内容发生改变
    onInputChange({ row }) {
      //新增行不用监控，用uuid来分辨
      if (row.id) {
        row.updated = true;
      }
    },
    loadLocal() {
      this.tablePage.totalResult = this.audioList.length;
      this.tableData = this.audioList.slice(
        (this.tablePage.currentPage - 1) * this.tablePage.pageSize,
        this.tablePage.currentPage * this.tablePage.pageSize
      );
    },

    handlePageChange({ currentPage, pageSize }) {
      this.tablePage.currentPage = currentPage;
      this.tablePage.pageSize = pageSize;
      this.loadLocal();
    },

    formatTime({ cellValue, row, column }) {
      if (cellValue) {
        return dayjs(cellValue).fromNow();
      }
      return "";
    },
    //确保只有一行被选中，并返回它在audioList的索引
    getCheckedOnelineIndex() {
      const selectRecords = this.$refs.xTable.getCheckboxRecords();
      if (!selectRecords || selectRecords.length < 1) {
        alert("请勾选一行");
        return;
      }
      if (selectRecords.length > 1) {
        alert("请只勾选一行");
        return;
      }

      const selectedRow = selectRecords[0];
      let index;
      //如果是源行
      if (selectedRow.id) {
        index = this.audioList.findIndex((item) => item.id === selectedRow.id);
        //如果是新加的
      } else if (selectedRow.uuid) {
        index = this.audioList.findIndex((item) => item.uuid === selectedRow.uuid);
      }

      return index;
    },

    insertEmptyRow() {
      this.audioList.unshift({ uuid: uuidv4() });
      this.loadLocal();
    },

    insertAudioFileAtIndex(files, index = 0) {
      let count = 0;
      for (let file of files) {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = async (e) => {
          const record = {
            name: file.name,
            url: e.target.result,
            file: file,
            uuid: uuidv4(),
          };
          this.audioList.splice(index, 0, record);
          count++;
          if (count === files.length) {
            this.loadLocal();
          }
        };
      }
    },
    insertAudio(e) {
      const files = e.target.files;
      this.insertAudioFileAtIndex(files, 0);
    },

    insertAudioAtIndex(e) {
      const files = e.target.files;
      const index = this.getCheckedOnelineIndex();
      this.insertAudioFileAtIndex(files, index);
    },

    deleteRow(row) {
      if (row.id) this.deletedRow.push(_.cloneDeep(row));

      const index = this.audioList.findIndex((item) => item.id === row.id);
      this.audioList.splice(index, 1);
      this.loadLocal();
    },
    sortCreatedAt({ row }) {
      if (row.created_at) return new Date(row.created_at).valueOf();

      return -1;
    },
    sortUpdatedAt({ row }) {
      if (row.created_at) return new Date(row.updated_at).valueOf();

      return -1;
    },
    onModalShow() {
      //计算 diff setTimeout 是为了给显示'计算'一个间隔
      const inserted = [];
      const updated = [];
      let deleted = [];

      for (const row of this.audioList) {
        //是新添加的行
        if (!row.id) {
          inserted.push(row);
          //是源行或者说载入的行
        } else {
          //如果更新过
          if (row.updated) {
            updated.push(row);
          }
        }
      }

      deleted = this.deletedRow;

      if (inserted.length || deleted.length || updated.length) {
        this.modalContent = "正在上传保存...";
        this.save({ inserted, deleted, updated });
        return;
      }
      this.modalContent = "没有需要保存的内容.";
      setTimeout(() => {
        this.modalContent = "";
        this.showModal = false;
      }, 500);
    },

    saveEvent() {
      this.showModal = true;
      this.modalContent = "正在计算需要保存的内容...";
      return;
    },

    save({ inserted, deleted, updated }) {
      const results = [];
      const that = this;

      //insert
      inserted.forEach((row) => {
        const data = new FormData();

        (row.blob || row.file) && data.append("file", row.blob || row.file);
        row.name && data.append("name", row.name);
        row.book_name && data.append("book_name", row.book_name);
        row.audio_text && data.append("audio_text", row.audio_text);
        data.append("uuid", row["uuid"]);

        results.push(
          axios.post(route("package.audio.store", { package: this.package.id }), data, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          })
        );
      });

      //delete
      deleted.forEach((row) => {
        results.push(
          axios.delete(
            route("package.audio.destroy", {
              audio: row.id,
              package: this.package.id,
            })
          )
        );
      });

      updated.forEach((row) => {
        const data = new FormData();

        (row.blob || row.file) && data.append("file", row.blob || row.file);
        data.append("name", row.name);
        data.append("book_name", row.book_name);
        data.append("audio_text", row.audio_text);
        data.append("_method", "patch");
        data.append("uuid", row["uuid"]);

        results.push(
          axios.post(
            route("package.audio.update", {
              package: this.package.id,
              audio: row.id,
            }),
            data,
            {
              headers: {
                "Content-Type": "multipart/form-data",
              },
            }
          )
        );
      });

      Promise.all(results)
        .then((results) => {
          that.saveUpdateData(results);
          that.modalContent = "";
          that.showModal = false;
        })
        .catch((err) => {
          console.log(err);
        });
    },

    saveUpdateData(results) {
      for (let result of results) {
        const item = result.data;
        const success = item.success;
        const type = item.type;
        const newItem = item.data;
        const uuid = item.uuid;
        //todo  error handling
        if (!success) continue;
        let index, foundItem;

        switch (type) {
          case "insert":
            this.initAudio.push(newItem);
            foundItem = this.audioList.find((item) => item.uuid === uuid);

            foundItem.id = newItem.id;
            if (newItem.url) {
              foundItem.url = newItem.url;
              delete foundItem["file"];
              delete foundItem["blob"];
            }

            break;
          case "delete":
            index = this.initAudio.findIndex((item) => item.id === newItem.id);
            this.initAudio.splice(index, 1);
            let indexForDeleted = this.deletedRow.findIndex(
              (item) => item.id === newItem.id
            );
            this.deletedRow.splice(indexForDeleted, 1);

            break;
          case "update":
            index = this.initAudio.findIndex((item) => item.id === newItem.id);
            this.initAudio.splice(index, 1, newItem);

            foundItem = this.audioList.find((item) => item.id === newItem.id);
            if ((foundItem.blob || foundItem.file) && newItem.url) {
              foundItem.url = newItem.url;
              delete foundItem["file"];
              delete foundItem["blob"];
            }
            delete foundItem["updated"];
            break;
        }
      }
    },

    normalize(row) {
      const fields = ["name", "book_name", "audio_text"];
      for (const field of fields) {
        row[field] = _.trim(row[field]);
      }
      return row;
    },
  },
  computed: {
    tableDataFiltered() {
      const filterItemLowered = XEUtils.toValueString(this.filterItem)
        .toLowerCase()
        .trim();
      if (!filterItemLowered) return this.audioList;
      return this.audioList.filter((row) => {
        return (
          XEUtils.toValueString(row["name"]).toLowerCase().indexOf(filterItemLowered) >
            -1 ||
          XEUtils.toValueString(row["book_name"])
            .toLowerCase()
            .indexOf(filterItemLowered) > -1 ||
          XEUtils.toValueString(row["audio_text"])
            .toLowerCase()
            .indexOf(filterItemLowered) > -1
        );
      });
    },
  },
  created() {},
  mounted() {
    axios
      .get(route("package.audio", { package: this.package.id }))
      .then((res) => {
        this.initAudio = res.data;
        this.audioList = _.cloneDeep(this.initAudio);
        this.loadLocal();
      })
      .catch((err) => console.log(err));
=======
  <vxe-grid ref="xGrid" v-bind="gridOptions"></vxe-grid>
</template>
<script >
import { defineComponent, onMounted, reactive, ref, Ref } from "vue";
import { VXETable, VxeGridInstance, VxeGridProps } from "vxe-table";
import XEUtils from "xe-utils";
import axios from "axios";

export default defineComponent({
  setup() {
    const xGrid = ref({});

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
        checkMethod({ column }) {
          if (["nickname", "role"].includes(column.property)) {
            return false;
          }
          return true;
        },
      },
      printConfig: {
        columns: [
          { field: "name" },
          { field: "email" },
          { field: "nickname" },
          { field: "age" },
          { field: "amount" },
        ],
      },
      sortConfig: {
        trigger: "cell",
        remote: true,
      },
      filterConfig: {
        remote: true,
      },
      pagerConfig: {
        pageSize: 10,
        pageSizes: [5, 10, 15, 20, 50, 100, 200, 500, 1000],
      },
      formConfig: {
        titleWidth: 100,
        titleAlign: "right",
        items: [
          {
            field: "name",
            title: "app.body.label.name",
            span: 8,
            titlePrefix: {
              message: "app.body.valid.rName",
              icon: "fa fa-exclamation-circle",
            },
            itemRender: {
              name: "$input",
              props: { placeholder: "请输入名称" },
            },
          },
          {
            field: "email",
            title: "邮件",
            span: 8,
            itemRender: {
              name: "$input",
              props: { placeholder: "请输入邮件" },
            },
          },
          {
            field: "nickname",
            title: "昵称",
            span: 8,
            itemRender: {
              name: "$input",
              props: { placeholder: "请输入昵称" },
            },
          },
          {
            field: "role",
            title: "角色",
            span: 8,
            folding: true,
            itemRender: {
              name: "$input",
              props: { placeholder: "请输入角色" },
            },
          },
          {
            field: "sex",
            title: "性别",
            span: 8,
            folding: true,
            titleSuffix: {
              message: "注意，必填信息！",
              icon: "fa fa-info-circle",
            },
            itemRender: { name: "$select", options: [] },
          },
          {
            field: "age",
            title: "年龄",
            span: 8,
            folding: true,
            itemRender: {
              name: "$input",
              props: {
                type: "number",
                min: 1,
                max: 120,
                placeholder: "请输入年龄",
              },
            },
          },
          {
            span: 24,
            align: "center",
            collapseNode: true,
            itemRender: {
              name: "$buttons",
              children: [
                {
                  props: {
                    type: "submit",
                    content: "app.body.label.search",
                    status: "primary",
                  },
                },
                { props: { type: "reset", content: "app.body.label.reset" } },
              ],
            },
          },
        ],
      },
      toolbarConfig: {
        buttons: [
          { code: "insert_actived", name: "新增", icon: "fa fa-plus" },
          { code: "delete", name: "直接删除", icon: "fa fa-trash-o" },
          { code: "mark_cancel", name: "删除/取消", icon: "fa fa-trash-o" },
          {
            code: "save",
            name: "app.body.button.save",
            icon: "fa fa-save",
            status: "success",
          },
        ],
        refresh: true,
        import: true,
        export: true,
        print: true,
        zoom: true,
        custom: true,
      },
      proxyConfig: {
        seq: true, // 启用动态序号代理，每一页的序号会根据当前页数变化
        sort: true, // 启用排序代理，当点击排序时会自动触发 query 行为
        filter: true, // 启用筛选代理，当点击筛选时会自动触发 query 行为
        form: true, // 启用表单代理，当点击表单提交按钮时会自动触发 reload 行为
        // 对应响应结果 { result: [], page: { total: 100 } }
        props: {
          result: "result", // 配置响应结果列表字段
          total: "page.total", // 配置响应结果总页数字段
        },
        // 只接收Promise，不关心系实现方式
        ajax: {
          // 当点击工具栏查询按钮或者手动提交指令 query或reload 时会被触发
          query: ({ page, sorts, filters, form }) => {
            const queryParams = Object.assign({}, form);
            // 处理排序条件
            const firstSort = sorts[0];
            if (firstSort) {
              queryParams.sort = firstSort.property;
              queryParams.order = firstSort.order;
            }
            // 处理筛选条件
            filters.forEach(({ property, values }) => {
              queryParams[property] = values.join(",");
            });
            return fetch(
              `https://api.xuliangzhan.com:10443/demo/api/pub/page/list/${page.pageSize}/${page.currentPage}`,
              queryParams
            ).then((response) => {
              return response.json();
            });
          },
          // 当点击工具栏删除按钮或者手动提交指令 delete 时会被触发
          delete: ({ body }) => {
            return axios.post(
              "https://api.xuliangzhan.com:10443/demo/api/pub/save",
              body
            );
          },
          // 当点击工具栏保存按钮或者手动提交指令 save 时会被触发
          save: ({ body }) => {
            return axios.post(
              "https://api.xuliangzhan.com:10443/demo/api/pub/save",
              body
            );
          },
        },
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
      importConfig: {
        remote: true,
        types: ["xlsx"],
        modes: ["insert"],
        // 自定义服务端导入
        importMethod({ file }) {
          const $grid = xGrid.value;
          const formBody = new FormData();
          formBody.append("file", file);
          return axios
            .post(
              "https://api.xuliangzhan.com:10443/demo/api/pub/import",
              formBody
            )
            .then((data) => {
              VXETable.modal.message({
                content: `成功导入 ${data.result.insertRows} 条记录！`,
                status: "success",
              });
              // 导入完成，刷新表格
              $grid.commitProxy("query");
            })
            .catch(() => {
              VXETable.modal.message({
                content: "导入失败，请检查数据是否正确！",
                status: "error",
              });
            });
        },
      },
      exportConfig: {
        remote: true,
        types: ["xlsx"],
        modes: ["current", "selected", "all"],
        // 自定义服务端导出
        exportMethod({ options }) {
          const $grid = xGrid.value;
          const proxyInfo = $grid.getProxyInfo();
          // 传给服务端的参数
          const body = {
            filename: options.filename,
            sheetName: options.sheetName,
            isHeader: options.isHeader,
            original: options.original,
            mode: options.mode,
            pager: proxyInfo ? proxyInfo.pager : null,
            ids:
              options.mode === "selected"
                ? options.data.map((item) => item.id)
                : [],
            fields: options.columns.map((column) => {
              return {
                field: column.property,
                title: column.title,
              };
            }),
          };
          // 开始服务端导出
          return axios
            .post("https://api.xuliangzhan.com:10443/demo/api/pub/export", body)
            .then((data) => {
              if (data.id) {
                VXETable.modal.message({
                  content: "导出成功，开始下载",
                  status: "success",
                });
                // 读取路径，请求文件
                fetch(
                  `https://api.xuliangzhan.com:10443/demo/api/pub/export/download/${data.id}`
                ).then((response) => {
                  response.blob().then((blob) => {
                    // 开始下载
                    VXETable.saveFile({
                      filename: "导出数据",
                      type: "xlsx",
                      content: blob,
                    });
                  });
                });
              }
            })
            .catch(() => {
              VXETable.modal.message({
                content: "导出失败！",
                status: "error",
              });
            });
        },
      },
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
        email: [{ required: true, message: "邮件必须填写" }],
        role: [{ required: true, message: "角色必须填写" }],
      },
      editConfig: {
        trigger: "click",
        mode: "row",
        showStatus: true,
      },
    });

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
    };
>>>>>>> deb965a636cea90f602797ba2047f34742876f11
  },
});
</script>
