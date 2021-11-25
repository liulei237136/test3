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
  <div class="flex flex-col space-y-2">
    <h1>包名: &nbsp;&nbsp;{{ package.name }}</h1>
    <div>作者: &nbsp;&nbsp;{{ package.author.name }}</div>
    <div>分类: &nbsp;&nbsp;{{ package.category }}</div>
    <p>
      {{ package.description }}
    </p>
    <p>
      <button
        class="purpleButton bg-purple-500 px-4 py-2 mt-2"
        @click="onClone"
      >
        克隆这个点读包
      </button>
    </p>
  </div>
</template>

<script>
import { defineComponent } from "vue";

export default defineComponent({
  props: {
    package: Object,
  },
  data() {
    return {
      showModal: false,
      modalContent: "",
      afterModalShow: null,
    };
  },
  methods: {
    onModalShow() {},
    onClone() {
        //如果未登录
        if(!this.$page.props.user){
            this.$inertia.get(route('login'));
            return;
        }
        this.modalContent = '正在克隆，请稍等...';
        this.showModal = true;
        this.clone();
    },
    clone(){
        axios.post(route('package.clone', {package: this.package.id}))
        .then((result)=>{
            console.log(result);
            this.modalContent = '克隆成功，准备进入编辑';
            setTimeout(()=>{
                this.$inertia.get(route('package.editInfo', {package: result.data.package_id}));
            }, 1000);
        })
        .catch((err)=>{
            console.log(err);
            this.modalContent = '克隆失败,请稍后重试';
        });

    },
  },
});
</script>
