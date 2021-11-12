<template>
<div class="overlay">
    <p class="content">{{content}}</p>
</div>
</template>

<style scoped>
.overlay {
    position: fixed;
    height: 100%;
    width: 100%;
    background: rgba(0,0,0,0.3);
    top:0;
    left:0;
    z-index: 1000000;
}

.content{
    display: inline-flexbox;
    margin: 200px auto;
    font-size: 25px;
}
</style>

<script>
    import { defineComponent } from 'vue'

    export default defineComponent({
        props: ['content'],
    })
</script>
