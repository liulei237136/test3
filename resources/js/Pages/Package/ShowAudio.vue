<template>
  <vxe-table
    :row-key="true"
    empty-text="还没有添加音频"
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
      defaultSort: { field: 'name', order: 'asc' },
      orders: ['desc', 'asc', null],
    }"
    ref="xTable"
  >
    <vxe-column field="name" title="文件名" width="200" sortable></vxe-column>
    <vxe-column title="播放" width="320">
      <template #default="{ row }">
        <audio v-if="row.url" :src="row.url" controls></audio>
      </template>
    </vxe-column>
    <vxe-column field="book_name" title="所属书名" sortable></vxe-column>
    <vxe-column field="audio_text" title="音频内容文字" sortable></vxe-column>
  </vxe-table>
  <vxe-pager
    background
    size="small"
    :loading="loading"
    :current-page="tablePage.currentPage"
    :page-size="tablePage.pageSize"
    :total="tablePage.totalResult"
    :page-sizes="[10, 20, 100, 1000, { label: '全部数据', value: -1 }]"
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

export default defineComponent({
  props: {
    package: Object,
    audio: Array,
  },
  data() {
    return {
      tableData: [],
      tablePage: {
        currentPage: 1,
        pageSize: 10,
        totalResult: 0,
        currentRow: null,
      },
      loading: false,
    };
  },

  methods: {
    loadLocal() {
      this.tablePage.totalResult = this.audio.length;
      this.tableData = this.audio.slice(
        (this.tablePage.currentPage - 1) * this.tablePage.pageSize,
        this.tablePage.currentPage * this.tablePage.pageSize
      );
    },

    handlePageChange({ currentPage, pageSize }) {
      this.tablePage.currentPage = currentPage;
      this.tablePage.pageSize = pageSize;
      this.loadLocal();
    },
  },
  mounted() {
    this.loadLocal();
  },
});
</script>
