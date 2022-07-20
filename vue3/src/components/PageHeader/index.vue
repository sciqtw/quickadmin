<template>
    <div class="header">
        <div style="width:100%;">
            <div class="title">
              <slot name="title">{{title}}</slot>
            </div>
            <div class="content">
                <slot name="content">{{ content }}</slot>
            </div>
        </div>
        <div>
            <slot />
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: {
        type: String,
    },
    content: {
        type: String,
        default: ''
    }
})
</script>

<style lang="scss" scoped>
.header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    padding: 16px 20px 0 20px;
    background: #fff;
    border-bottom: 1px solid #e8eaec;
    .title {
        font-size: 22px;
    }
    .content {
        color: #909399;
        font-size: 14px;
        margin-top: 10px;
        /*padding-bottom: 15px;*/
        &:empty {
            display: none;
        }
    }
}
</style>
