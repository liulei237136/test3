import { App } from 'vue'
import 'xe-utils'
import VXETable from 'vxe-table'
import 'vxe-table/lib/style.css'

// 全局默认参数
VXETable.setup({
    version: 0,
    zIndex: 100,
    table: {
        autoResize: true
    },
 icon: {
          // table
          TABLE_SORT_ASC: 'fa fa-caret-up',
          TABLE_SORT_DESC: 'fa fa-caret-down',
          TABLE_FILTER_NONE: 'vxe-icon--funnel',
          TABLE_FILTER_MATCH: 'vxe-icon--funnel',
          TABLE_EDIT: 'vxe-icon--edit-outline',
          TABLE_TREE_LOADED: 'vxe-icon--refresh roll',
          TABLE_TREE_OPEN: 'vxe-icon--caret-right rotate90',
          TABLE_TREE_CLOSE: 'vxe-icon--caret-right',
          TABLE_EXPAND_LOADED: 'vxe-icon--refresh roll',
          TABLE_EXPAND_OPEN: 'vxe-icon--arrow-right rotate90',
          TABLE_EXPAND_CLOSE: 'vxe-icon--arrow-right',

          // button
          BUTTON_DROPDOWN: 'vxe-icon--arrow-bottom',
          BUTTON_LOADING: 'vxe-icon--refresh roll',

          // select
          SELECT_OPEN: 'vxe-icon--caret-bottom rotate180',
          SELECT_CLOSE: 'vxe-icon--caret-bottom',

          // pager
          PAGER_JUMP_PREV: 'fa fa-chevron-double-left',
          PAGER_JUMP_NEXT: 'fa fa-chevron-double-right',
          PAGER_PREV_PAGE: 'fa fa-chevron-left',
          PAGER_NEXT_PAGE: 'fa fa-chevron-right',
          PAGER_JUMP_MORE: 'vxe-icon--more',

          // input
          INPUT_CLEAR: 'vxe-icon--close',
          INPUT_PWD: 'vxe-icon--eye-slash',
          INPUT_SHOW_PWD: 'vxe-icon--eye',
          INPUT_PREV_NUM: 'fa fa-caret-up',
          INPUT_NEXT_NUM: 'fa fa-caret-down',
          INPUT_DATE: 'vxe-icon--calendar',
          INPUT_SEARCH: 'vxe-icon--search',

          // modal
          MODAL_ZOOM_IN: 'vxe-icon--square',
          MODAL_ZOOM_OUT: 'vxe-icon--zoomout',
          MODAL_CLOSE: 'vxe-icon--close',
          MODAL_INFO: 'vxe-icon--info',
          MODAL_SUCCESS: 'vxe-icon--success',
          MODAL_WARNING: 'vxe-icon--warning',
          MODAL_ERROR: 'vxe-icon--error',
          MODAL_QUESTION: 'vxe-icon--question',
          MODAL_LOADING: 'vxe-icon--refresh roll',

          // toolbar
          TOOLBAR_TOOLS_REFRESH: 'vxe-icon--refresh',
          TOOLBAR_TOOLS_REFRESH_LOADING: 'vxe-icon--refresh roll',
          TOOLBAR_TOOLS_IMPORT: 'vxe-icon--upload',
          TOOLBAR_TOOLS_EXPORT: 'vxe-icon--download',
          TOOLBAR_TOOLS_ZOOM_IN: 'vxe-icon--zoomin',
          TOOLBAR_TOOLS_ZOOM_OUT: 'vxe-icon--zoomout',
          TOOLBAR_TOOLS_CUSTOM: 'vxe-icon--menu',

          // form
          FORM_PREFIX: 'vxe-icon--info',
          FORM_SUFFIX: 'vxe-icon--info',
          FORM_FOLDING: 'vxe-icon--arrow-top rotate180',
          FORM_UNFOLDING: 'vxe-icon--arrow-top'
        }
})

export function useTable(app) {
    app.use(VXETable);
    app.config.globalProperties.$XModal = VXETable.modal;
}

