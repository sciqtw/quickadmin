<template>
  <div>
    <el-card class="icon-card" shadow="hover">
      <a href="" style="text-align: center">
        <svg-icon size="30" icon="el-icon-s-operation" class="icon" color="#69c0ff" />
        <p>配置</p>
      </a>
    </el-card>

    <el-card shadow="hover">
      <div class="card-box">
        <div class="card_box_cir two">
          <div class="two1 card_box_cir1">
            <quick-icon size="30" icon="el-icon-s-operation" class="icon" color="#FFFFFF" />
          </div>
        </div>
        <div class="box-text">
          <span class="box-text-num">3</span>
          <span class="box-text-str" @click="test">参与人数量</span>
        </div>
      </div>

    </el-card>
  </div>

</template>

<script>
  export default {
    name: 'IconCard',
    props: ['href', 'icon', 'size', 'color', 'name'],
    methods: {
      test() {
        console.log('----test card-->',)
        this.dispatch('Test', 'test3')
      }
    }
  }
</script>

<style lang="scss" scoped>
  .icon-card{
    text-align:center;
  }
  .icon-card .icon{
    transition: all .3s ease-in-out;
  }
  .icon-card:hover .icon {
    transform: scale(1.15);
  }

  .card-box {
    width: 100%;
    height: 100%;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    border-radius: 4px;

    .card_box_cir{
      border-radius: 50%;
      display: -webkit-box;
      display: -ms-flexbox;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 60px;
      height: 60px;
      overflow: hidden;
      margin-right: 20px;

    }
    .card_box_cir1{
      border-radius: 50%;
      display: -webkit-box;
      display: -ms-flexbox;
      display: flex;
      justify-content: center;
      align-items: center;
      width: 48px;
      height: 48px;
    }

    .two {
      background: #fff3e0;
    }
    .two1 {
      background: #ed4014;
    }
    .box-text{
      .box-text-num{
        display:block;
        color: #252631;
        font-size: 24px;
      }
      .box-text-str{
        display:block;
        color: #98a9bc;
        font-size: 12px;
      }
    }
  }
</style>
