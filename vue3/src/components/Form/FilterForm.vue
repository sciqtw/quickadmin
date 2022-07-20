<template>
  <form-render
    v-if="showFilter"
    ref="gform"
    :key="key"
    v-bind="attributes"
    class="quick-form"
    @submit="submit"
    @reset="reset"
    @change="handleChange"
  >
  </form-render>
</template>

<script>
  import {
    onMounted,
    onUnmounted,
    nextTick,
    ref,
    watch,
    useAttrs,
    computed,
  } from 'vue';

  import {escapeUnicode} from '@/utils/escapeUnicode'
  import {useRoute, useRouter} from "vue-router";
  import defaults from "lodash/defaults";


  export default {
    name: 'FilterForm',
    props: {
      data: {
        type: Object,
        default: () => {
        }
      }
    },
    setup(props, {emit}) {

      const routeInfo = useRoute();
      const gform = ref(null);
      const filter = ref({})
      const form = ref({})
      const key = ref(1)
      const showFilter = ref(true)
      const attrs = useAttrs();

      const moduleName = computed(() => {
        return routeInfo.params.moduleName || ''
      })
      const resourceName = computed(() => {
        return routeInfo.params.resourceName || ''
      })

      const resourceRequestQueryString = computed(() => {
        return Object.assign({}, routeInfo.query)
      })

      const resolveModeluName = computed(() => {
        return props.moduleName
      })

      const currentFilters = computed(() => {
        return routeInfo.query.filters || ''
      })

      /**
       * 获取过滤器
       */
      const getFilterData = () => {
        if (!resourceName.value) {
          return
        }
        Quick.request({
          method: 'get',
          url: `${moduleName.value}/resource/${resourceName.value}/filter`,
          params: resourceRequestQueryString.value
        }).then(response => {
          filter.value = response.data
          form.value = response.data.form

          nextTick(() => {
            key.value++
            showFilter.value = true
          })

        }).catch(error => {
          console.log('getFilterData error--->', error)
        })
      }

      const handleChange = (data) => {
        // 先关闭自动搜索功能
        // submit()
        // console.log('------form-filter', data)
      }


      onMounted(() => {
        Quick.$on('enter',() => {
          submit()
        })

      })
      onUnmounted(() => {
        Quick.$off('enter')
      })

      watch(resourceRequestQueryString, () => {
        // getFilterData()
      })

      const reset = () => {
        gform.value.resetForm();
        handleFilterSubmit({})
      }
      const submit = (name) => {
        // const form = this.$refs[name]
        gform.value.validateForm((e) => {
          if (e.length === 0) {
            // emit('submit', gform.value.formData(), this)
            handleFilterSubmit(gform.value.formData())
          } else {
            console.log(e)
            Quick.message({
              message: e[0].message,
              type: 'warning'
            })
          }
        })
      }
      /**
       * 处理filter
       * */
      const handleFilterSubmit = (data) => {

        const filters = btoa(escapeUnicode(JSON.stringify(data)))
        if (filters === currentFilters.value) {
          // this.getInitData()
        } else {
          updateQueryString({
            filters: filters,
            page: 1
          })
        }
      }


      const router = useRouter();
      const updateQueryString = (value) => {
        emit('submit', value)
        // router.push({ query: defaults(value, routeInfo.query) })
      }


      /**
       * 扩展属性
       */
      const attributes = computed(() => {

        const data = props.data?.form?.props || {}
        return {
          ...attrs,
          ...data
        }

      });

      return {
        submit,
        reset,
        handleChange,
        gform,
        filter,
        form,
        key,
        attributes,
        showFilter,
      }

    }


  }

</script>

<style lang="scss" scoped>
  .quick-form {
    .footer-button {
      margin-bottom: 0;
      margin-right: 10px;
    }

    margin-bottom: 15px;
  }

</style>
