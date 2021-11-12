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
      <vxe-button icon="fa fa-mail-reply" status="perfect" @click="revertEvent"
        >还原</vxe-button
      >
    </template>
  </vxe-toolbar>
  <vxe-table
    keep-source
    :row-key="true"
    :edit-config="{ trigger: 'click', mode: 'cell', showStatus: true }"
    empty-text="还没有添加音频哦！"
    show-overflow
    :loading="loading"
    border
    highlight-hover-row
    highlight-hover-column
    :data="tableData"
    resizable
    :sort-config="{
      trigger: 'cell',
      defaultSort: { field: 'name', order: 'asc' },
      orders: ['desc', 'asc'],
    }"
    ref="xTable"
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
    <vxe-column title="播放和重录" width="520">
      <template #default="{ row }">
        <audio-recorder :row="row"></audio-recorder>
      </template>
    </vxe-column>
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
    <vxe-column
      field="created_at"
      title="创建时间"
      :formatter="formatTime"
      :sort-by="sortCreatedAt"
      sortable
    ></vxe-column>
    <vxe-column
      field="updated_at"
      title="更新时间"
      :formatter="formatTime"
      :sort-by="sortUpdatedAt"
      sortable
    ></vxe-column>
    <vxe-column title="操作" width="100" show-overflow>
      <template #default="{ row }">
        <button type="button" @click="deleteRow(row)">
          <icon name="edit"></icon>
        </button>
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
    :page-sizes="[
      10,
      20,
      100,
      { label: '大量数据', value: 1000 },
      { label: '全量数据', value: -1 },
    ]"
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
</template>

<script>
import { defineComponent } from "vue";

import VXETable from "vxe-table";
import Icon from "@/Components/Icon";
import AudioRecorder from "./AudioRecorder";

import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import "dayjs/locale/zh-cn";
dayjs.extend(relativeTime).locale("zh-cn");

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
      audioList: window._.cloneDeep(this.package.audio),
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
    };
  },
  methods: {
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
      const record = {};
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
        const old = this.package.audio;

        for (const row of this.audioList) {
          if (!row.id) {
            //避免空行
            const normalized = this.normalize(row);
            if (
              normalized.name ||
              normalized.audio_text ||
              normalized.book_name ||
              normalized.url
            ) {
              inserted.push(row);
              continue;
            }
          }
        }

        for (const oldRow of old) {
          let newRow = this.audioList.find((row) => row.id === oldRow.id);
          //deleted
          if (!newRow) {
            deleted.push(oldRow);
            continue;
          }
          // not change,so skip
          newRow = this.normalize(newRow);
          if (
            oldRow.name == newRow.name &&
            oldRow.url == newRow.url &&
            oldRow.book_name == newRow.book_name &&
            oldRow.audio_text == newRow.audio_text
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

      //insert
      inserted.forEach((row) => {
        const data = new FormData();
        //录音或者上传mp3
        if(row.blob || row.file){
            data.append("file", row.blob || row.file);
        }
        //name
        if (row.name) {
          data.append("name", row.name);
        }
        //book_name
        if (row.book_name) {
          data.append("book_name", row.book_name);
        }
        //audio_text
        if (row.audio_text) {
          data.append("audio_text", row.audio_text);
        }

        results.push(
          axios.post(
            route("package.audio.store", { package: this.package.id }),
            data,
            {
              headers: {
                "Content-Type": "multipart/form-data",
                _method: "PATCH",
              },
            }
          )
        );
      });

      //delete
      deleted.forEach((row) => {
        results.push(
          axios.post(
            route("package.audio.destroy", {
              audio: row.id,
              package: this.package.id,
            }),
            {
              headers: {
                _method: "DELETE",
              },
            }
          )
        );
      });

      updated.forEach((row) => {
        const data = new FormData();
        //file
        if(row.blob || row.file){
            data.append("file", row.blob || row.file);
        }
        //name
        if (row.name) {
          data.append("name", row.name);
        }
        //book_name
        if (row.book_name) {
          data.append("book_name", row.book_name);
        }
        //audio_text
        if (row.audio_text) {
          data.append("audio_text", row.audio_text);
        }

        results.push(
          axios.post(
            route("package.audio.store", { package: this.package.id }),
            data,
            {
              headers: {
                "Content-Type": "multipart/form-data",
                _method: "PATCH",
              },
            }
          )
        );
      });

      Promise.all(results)
        .then((result) => {
          console.log(result);
        })
        .catch((err) => {
          console.log(err);
        });
    },

    normalize(row) {
      const fields = ["name", "book_name", "audio_text"];
      for (const field of fields) {
        if (typeof row[field] == "string") {
          row[field] = row[field].trim();
          if (row[field].length === 0) {
            row[field] = null;
          }
        }
      }
      return row;
    },
  },

  mounted() {
    this.loadLocal();
  },
});
</script>
