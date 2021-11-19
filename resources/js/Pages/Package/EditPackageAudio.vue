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
      <vxe-button icon="fa fa-plus" status="perfect" @click="insertEvent()"
        >新增空白行</vxe-button
      >
      <vxe-button
        icon="fa fa-plus"
        status="perfect"
        @click="$refs.insertFromAudio.click()"
        >添加MP3来新增</vxe-button
      >
      <input
        type="file"
        ref="insertFromAudio"
        accept=".mp3"
        hidden
        multiple
        @change="insertFromAudioEvent"
      />
      <vxe-button icon="fa fa-trash-o" status="perfect">移除</vxe-button>
      <vxe-button icon="fa fa-save" status="perfect" @click="saveEvent"
        >保存</vxe-button
      >

      <vxe-button icon="fa fa-mail-reply" status="perfect">还原</vxe-button>
    </template>
  </vxe-toolbar>
  <vxe-pager
    background
    size="small"
    :loading="loading"
    :current-page="tablePage.currentPage"
    :page-size="tablePage.pageSize"
    :total="tablePage.totalResult"
    :page-sizes="[10, 20, 100, 1000, { label: '全量数据', value: -1 }]"
    :layouts="[
      'PrevPage',
      'JumpNumber',
      'NextPage',
      'FullJump',
      'Sizes',
      'Total',
    ]"
    @page-change="handlePageChange"
  >
  </vxe-pager>

  <!-- :edit-config="{ trigger: 'click', mode: 'cell', showStatus: false }" -->
  <vxe-table
    empty-text="还没有添加音频哦！"
    show-overflow
    :loading="loading"
    border
    highlight-hover-row
    highlight-hover-column
    :data="tableData"
    resizable
    height="600"
    :sort-config="{
      trigger: 'cell',
      defaultSort: { field: 'name', order: null },
      orders: ['desc', 'asc', null],
    }"
    ref="xTable"
    @cell-dblclick="cellDBLClickEvent"
  >
    <vxe-column type="checkbox" width="60"></vxe-column>
    <vxe-column
      field="name"
      title="文件名"
      sortable
      :edit-render="{
        name: 'input',
        attrs: { type: 'text' },
      }"
    ></vxe-column>
    <!-- <vxe-column title="播放和重录" width="520">
      <template #default="{ row }">
        <audio-recorder :row="row"></audio-recorder>
      </template>
    </vxe-column> -->
    <vxe-column
      field="book_name"
      title="所属书名"
      sortable
      :edit-render="{
        name: 'input',
        attrs: { type: 'text' },
        autoselect: true,
      }"
    ></vxe-column>
    <vxe-column
      field="audio_text"
      title="音频内容文字"
      sortable
      :edit-render="{
        name: 'input',
        attrs: { type: 'text' },
        autoselect: true,
      }"
    ></vxe-column>
    <vxe-column title="操作" width="100" show-overflow>
      <template #default="{ row }">
        <vxe-button
          type="text"
          icon="fa fa-edit"
          @click="editIconClickEvent(row)"
        ></vxe-button>
      </template>
    </vxe-column>
  </vxe-table>
  <vxe-pager
    background
    size="small"
    :loading="loading"
    :current-page="tablePage.currentPage"
    :page-size="tablePage.pageSize"
    :total="tablePage.totalResult"
    :page-sizes="[10, 20, 100, 1000, { label: '全量数据', value: -1 }]"
    :layouts="[
      'PrevPage',
      'JumpNumber',
      'NextPage',
      'FullJump',
      'Sizes',
      'Total',
    ]"
    @page-change="handlePageChange"
  >
  </vxe-pager>

  <vxe-modal
    :esc-closable="true"
    v-model="showEdit"
    :title="selectRow ? '编辑&保存' : '新增&保存'"
    width="800"
    min-width="600"
    min-height="300"
    :loading="submitLoading"
    resize
    destroy-on-close
  >
    <template #default>
      <vxe-form
        :data="formData"
        :rules="formRules"
        title-align="right"
        title-width="100"
        @submit.prevent="editFormSubmitEvent"
      >
        <vxe-form-item field="name" title="文件名" :span="24">
          <template #default="{ data }">
            <vxe-input v-model="data.name" placeholder="文件名"></vxe-input>
          </template>
        </vxe-form-item>
        <vxe-form-item field="book_name" title="书名" :span="24">
          <template #default="{ data }">
            <vxe-input v-model="data.book_name" placeholder="书名"></vxe-input>
          </template>
        </vxe-form-item>
        <vxe-form-item field="audio_text" title="音频文字描述" :span="24">
          <template #default="{ data }">
            <vxe-input
              v-model="data.audio_text"
              placeholder="音频文字描述"
            ></vxe-input>
          </template>
        </vxe-form-item>
      </vxe-form>
    </template>
  </vxe-modal>
</template>

<script>
import { defineComponent } from "vue";

import Icon from "@/Components/Icon";
import AudioRecorder from "./Audio/AudioRecorder";

import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import "dayjs/locale/zh-cn";
dayjs.extend(relativeTime).locale("zh-cn");

import { v4 as uuidv4 } from "uuid";

export default defineComponent({
  props: {
    package: Object,
    audio: Array,
  },
  components: {
    Icon,
    AudioRecorder,
  },
  data() {
    return {
      audioList: window._.cloneDeep(this.audio),
      tableData: [],
      tablePage: {
        currentPage: 1,
        pageSize: 10,
        totalResult: 0,
        currentRow: null,
      },
      loading: false,
      showModal: false,
      modalContent: "",
      formData: {
        name: "",
        book_name: "",
        audio_text: "",
      },
      selectRow: null,
      showEdit: false,
      submitLoading: false,
      formRules: {
        name: [{ min: 1, max: 30, message: "文件名介于1到15个字之间" }],
        book_name: [{ min: 1, max: 100, message: "书名不能超过50个字" }],
        audio_text: [
          { min: 1, max: 2000, message: "音频文字内容不能超过1000个字" },
        ],
      },
    };
  },
  methods: {
    editIconClickEvent(row) {
        this.formData = {
            name: row.name,
            book_name: row.book_name,
            audio_text: row.audio_text,
        };
    //   this.formData.name = row.name;
    //   this.formData.book_name = row.book_name;
    //   this.formData.audio_text = row.audio_text;
      this.selectRow = row;
      this.showEdit = true;
    },
    editFormSubmitEvent() {
      //     that = this;
      //   this.submitLoading = true;
      //   setTimeout(() => {
      //     that.submitLoading = false;
      //     that.showEdit = false;
      //     if (that.selectRow) {
      //       VXETable.modal.message({ content: "保存成功", status: "success" });
      //       Object.assign(that.selectRow, that.formData);
      //     } else {
      //       VXETable.modal.message({ content: "新增成功", status: "success" });
      //       $table.insert(demo1.formData);
      //     }
      //   }, 500);
    },
    cellDBLClickEvent({ row }) {
      this.editIconClickEvent(row);
    },
    onChange(e) {
      console.log(e.target.tagName);
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
    insertEvent(row) {
      const record = {
        uuid: uuidv4(),
      };
      this.audioList.unshift(record);
      this.loadLocal();
    },
    insertFromAudioEvent(e) {
      const files = e.target.files;
      let count = 0;
      for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = async (e) => {
          const url = e.target.result;
          const record = {
            name: file.name,
            url: url,
            file: file,
            uuid: uuidv4(),
          };
          this.audioList.unshift(record);
          count++;
          if (count === files.length) {
            this.loadLocal();
          }
        };
      }
    },

    deleteRow(row) {
      const index = this.audioList.findIndex((item) => item.id === row.id);
      this.audioList.splice(index, 1);
      this.loadLocal();
    },
    sortCreatedAt({ row }) {
      if (row.created_at) {
        return new Date(row.created_at).valueOf();
      }
      return -1;
    },
    sortUpdatedAt({ row }) {
      if (row.created_at) {
        return new Date(row.updated_at).valueOf();
      }
      return -1;
    },
    onModalShow() {
      //计算 diff setTimeout 是为了给显示'计算'一个间隔
      setTimeout(() => {
        const inserted = [];
        const updated = [];
        const deleted = [];
        const old = this.audio;

        for (const row of this.audioList) {
          if (!row.id) {
            //避免空行
            const normalized = this.normalizeNew(row);
            if (
              normalized.name ||
              normalized.audio_text ||
              normalized.book_name ||
              normalized.url
            ) {
              inserted.push(row);
            }
          }
        }

        for (let oldRow of old) {
          let newRow = this.audioList.find((row) => row.id === oldRow.id);
          //deleted
          if (!newRow) {
            deleted.push(oldRow);
            continue;
          }

          //统一标准
          oldRow = this.normalizeNew(oldRow);
          newRow = this.normalizeNew(newRow);

          if (
            oldRow.name === newRow.name &&
            oldRow.url === newRow.url &&
            oldRow.book_name === newRow.book_name &&
            oldRow.audio_text === newRow.audio_text
          ) {
            continue;
          } else {
            //else it changed
            updated.push(newRow);
          }
        }
        if (inserted.length || deleted.length || updated.length) {
          this.modalContent = "正在上传保存...";
          this.save({ inserted, deleted, updated });
          return;
        }
        this.modalContent = "没有需要保存的内容.";
        setTimeout(() => {
          this.modalContent = "";
          this.showModal = false;
        }, 2000);
      }, 1000);
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
          axios.post(
            route("package.audio.store", { package: this.package.id }),
            data,
            {
              headers: {
                "Content-Type": "multipart/form-data",
              },
            }
          )
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
            this.audio.push(newItem);
            foundItem = this.audioList.find((item) => item.uuid === uuid);

            foundItem.id = newItem.id;
            if (newItem.url) {
              foundItem.url = newItem.url;
              delete foundItem["file"];
              delete foundItem["blob"];
            }

            break;
          case "delete":
            index = this.audio.findIndex((item) => item.id === newItem.id);
            this.audio.splice(index, 1);

            break;
          case "update":
            index = this.audio.findIndex((item) => item.id === newItem.id);
            this.audio.splice(index, 1, newItem);

            foundItem = this.audioList.find((item) => item.uuid === uuid);
            if ((foundItem.blob || foundItem.file) && newItem.url) {
              foundItem.url = newItem.url;
              delete foundItem["file"];
              delete foundItem["blob"];
            }
            break;
        }
      }
    },

    normalizeNew(row) {
      const fields = ["name", "book_name", "audio_text"];
      for (const field of fields) {
        row[field] = _.trim(row[field]);
      }
      return row;
    },
  },

  mounted() {
    this.loadLocal();
  },
});
</script>
