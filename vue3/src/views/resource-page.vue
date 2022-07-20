<template>
  <div id="qk_resource">
    <json-render
      :render-data="formFields"
      :module-name="moduleName"
      :resource="resourceName"
      :key="key"
      @refresh="refresh"
      v-if="formFields.component"/>
  </div>

</template>

<script setup>
  import {computed, ref, onMounted, defineProps, watch} from 'vue';

  import {useStore} from 'vuex';
  import {useRoute, useRouter} from 'vue-router';

  const store = useStore();
  const routeInfo = useRoute();
  const router = useRouter();

  const props = defineProps({
    // 模块
    moduleName: {
      type: String,
      default: ''
    },
    // 资源
    resourceName: {
      type: String,
      default: 'index'
    },
    // 方法
    actionName: {
      type: String,
      default: 'index'
    },
    func: {
      type: String,
      default: ''
    }
  })


  const formFields = ref({});
  const show = ref(false);
  const components = ref({});
  const key = ref(1);

  const resolveModeluName = computed(() => {
    return props.moduleName
  })


  const resourceRequestQueryString = computed(() => {
    return Object.assign({}, routeInfo.query)
  })

  const requestUrl = computed(() => {
    let url = `${resolveModeluName.value}/resource/${props.resourceName}/${props.actionName}`
    if (props.func) {
      url += '/' + props.func
    }
    return url
  })

  const refreshPage = computed(() => {
    return routeInfo.query.repage || ''
  })


  const refresh = function () {
    getInitData
  }
  /**
   * 获取数据
   */
  const getInitData = function () {
    Quick.api.loading({
      target:'.loading_class',
      background: 'rgba(0, 0, 0, 0.1)'
    })
    Quick.request().get(requestUrl.value,
      {
        params: resourceRequestQueryString.value
      }
    ).then((data) => {


      formFields.value = data.data
      // key.value++
      Quick.api.closeLoading()
    }).catch((error) => {
      console.log('error', error)
    }).finally(() => {
      Quick.api.closeLoading()
    })
  }

  watch(
    () => refreshPage.value + requestUrl.value,
    () => {
    getInitData()
  })
  onMounted(() => {
    Quick.$on('refresh',() => {
      getInitData()
    })
    getInitData()

  })


</script>

<style scoped lang="scss">
  .box {
    /*background-color: #FFFFFF;*/
    margin: 15px;
    padding: 15px;
  }

  .skeleton-box {
    margin-top: 30px;

    .skeleton-box-item {
      padding: 30px;
      border-radius: 8px;
      background-color: #FFFFFF;
    }
  }
</style>
