<template>
  <vxe-modal
    v-model="showModal"
    @show="onModalShow"
    :show-header="false"
    width="200"
    height="60"
    :content="modalContent"
  >
  </vxe-modal>
  <vxe-toolbar perfect>
    <template #buttons>
      <div class="inline-flex items-center space-x-2 ml-2 mr-8">
        <label
          for="filter"
          class="text-sm font-medium text-gray-900 flex-shrink-0 dark:text-gray-300"
          >过滤:</label
        >
        <input
          v-model="filterItem"
          type="text"
          id="filter"
          class="w-64 h-8 bg-gray-50 border border-gray-300 text-gray-900 sm:text-xs rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
        />
      </div>
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
      <vxe-button v-if="canEdit" icon="fa fa-save" status="perfect" @click="saveEvent">保存</vxe-button>
    </template>
  </vxe-toolbar>
  <vxe-table
    :checkbox-config="{ checkField: 'checked', highlight: true, range: true }"
    :row-key="true"
    empty-text="还没有添加音频哦！"
    show-overflow
    :loading="loading"
    border
    highlight-hover-row
    highlight-hover-column
    :data="tableDataFiltered"
    resizable
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

<script>
import { defineComponent } from "vue";

import Icon from "@/Components/Icon";
import AudioRecorder from "./AudioRecorder";

import { v4 as uuidv4 } from "uuid";

import XEUtils from "xe-utils";

export default defineComponent({
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
  },
});
</script>
