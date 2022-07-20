<template>
  <div>
    <div style="height: 0;overflow: hidden;">
      <canvas id="app-attachment-canvas" style="border: 1px solid #ccc;visibility: hidden;"></canvas>
    </div>
    <el-tabs v-model="tabActiveName" v-if="showTab">
      <el-tab-pane label="图片" name="1"></el-tab-pane>
      <el-tab-pane label="视频" name="2"></el-tab-pane>
    </el-tabs>
    <div class="flex attachment-box ">
      <div class="attachment-left">
        <el-scrollbar height="450px">
          <el-input
            style="padding:6px;"
            v-model="cateQuery"
            placeholder="请输入分类搜索"
            @input="onQueryChanged"
            @change="onQueryChanged"
          ></el-input>
          <el-tree-v2
            :data="cateList"
            :filter-method="filterMethod"
            @current-change="handleClickCate"
            :expand-on-click-node="false"
            ref="cateTree"
            style="width:230px;margin-top:15px;" highlight-current>
            <template #default="{ node }">
              <div class="tree-item"
                   :class="selectCate === node.data.id ? 'active':''">
                <div style="overflow: hidden;text-overflow: ellipsis">
                  <i class="el-icon-tickets"></i>
                  <span>{{node.label}}</span>
                </div>

                <div class="tree-btn" v-if="node.data && node.data.id >0" @click.stop="">
                  <el-dropdown @command="handleCommand">
                        <span class="el-dropdown-link tree-btn-icon">
                            <quick-icon rotate="90" icon="el-icon-MoreFilled"></quick-icon>
                        </span>
                    <template #dropdown>
                      <el-dropdown-menu>
                        <el-dropdown-item command="add" @click.stop="addCate(node.data,'add',node)"
                                          v-if="node.level < 3">添加
                        </el-dropdown-item>
                        <el-dropdown-item command="edit" @click.stop="addCate(node.data,'edit')">编辑</el-dropdown-item>
                        <el-dropdown-item command="del" @click.stop="delCate(node.data.id)">删除</el-dropdown-item>
                      </el-dropdown-menu>
                    </template>
                  </el-dropdown>
                </div>
                <div v-else-if="node.level < 3">
                  <el-button type="text" @click.stop="addCate">添加</el-button>
                </div>
              </div>
            </template>
          </el-tree-v2>
        </el-scrollbar>
      </div>
      <div class="flex-y flex-grow" v-loading="loading">
        <div class="flex" style="padding:15px;">

          <el-checkbox v-model="allChecked" @change="selectAllChange">全选</el-checkbox>
          <el-button size="mini" type="danger" :disabled="!selectItemIds.length" style="margin-left:15px;"
                     @click="delAttachments">删除
          </el-button>
          <el-popover
            placement="bottom"
            title=""
            :disabled="!selectItemIds.length"
            :width="200"
            trigger="click"
            content="this is content, this is content, this is content"
          >
            <el-scrollbar height="300px">
              <el-tree-v2
                :data="cateList"
                :expand-on-click-node="false"
                ref="cateTree"
                style="width:200px;margin-top:15px;"
                @current-change="selectMoveCate"
                highlight-current
              >
                <template #default="{ node }">
                  <div class="tree-item" >
                    <div style="overflow: hidden;text-overflow: ellipsis">
                      <i class="el-icon-tickets"></i>
                      <span>{{node.label}}</span>
                    </div>
                  </div>
                </template>
              </el-tree-v2>

            </el-scrollbar>
            <div class="flex-end flex">
              <el-button size="mini" type="primary" @click="handleMoveCate">确定</el-button>
            </div>
            <template #reference>
              <el-button size="mini" :disabled="!selectItemIds.length">移动至</el-button>
            </template>
          </el-popover>
          <div class="search" style="margin-left: 12px">
            <el-input placeholder="请输入名称搜索" v-model="keyword"
                      @keyup.enter="search"
                      class="input-with-select">
              <template #append>
                <el-button icon="el-icon-Search" @click="search"></el-button>
              </template>
            </el-input>
          </div>
        </div>
        <div class="image-list">
          <el-scrollbar height="500px">
            <el-upload
              :show-file-list="false"
              :headers="headers"
              :action="action"
              :data="updateRequestData"
              multiple
              :before-upload="beforeUpload"
              :on-progress="onProgress"
              :on-success="onSuccess"
              class="image-item-box item-upload"
            >
              <div v-if="uploadData.progress.percent" class="progress">
                <el-progress type="circle" :width="Math.min(100, 100) * 0.8"
                             :percentage="uploadData.progress.percent"/>
              </div>
              <div v-else class="image-slot" style="width: 100px;height: 100px;line-height: 100px;">
                <quick-icon icon="el-icon-UploadFilled"></quick-icon>
              </div>
            </el-upload>
            <div
              v-for="(item, index) in attachments"
              :key="index"
              class="image-item-box"
              :class="item.checked ?' checked':''"
              @click="handleImgClick(item)">
              <img v-if="item.type == 1" class="img" :src="item.thumb_image"
                   style="width: 100px;height: 100px;">
              <div v-if="item.type == 2" class="img"
                   style="width: 100px;height: 100px;position: relative">
                <div v-if="item.cover_pic_src"
                     class="app-attachment-video-cover"
                     :style="'background-image: url('+item.cover_pic_src+');'"></div>
                <video style="width: 0;height: 0;visibility: hidden;"
                       :id="'app_attachment_'+ _uid + '_' + index">
                  <source :src="item.url">
                </video>
                <div class="app-attachment-video-info">
                  <i class="el-icon-video-play"></i>
                  <span>{{item.duration?item.duration:'--:--'}}</span>
                </div>
              </div>
              <div v-if="item.type == 3" class="app-attachment-img"
                   style="width: 100px;height: 100px;line-height: 100px;text-align: center">
                <i class="file-type-icon el-icon-document"></i>
              </div>
              <div class="flex flex-between image-name-box" style="margin-top:5px;">
                <div class="image-name">{{item.name}}</div>
                <div class="name-btn" @click="showEdit(item)">
                  <quick-icon icon="el-icon-Edit" size="13" color="#409EFF"></quick-icon>
                </div>

              </div>
              <div class="image-full-name">{{item.name}}</div>

            </div>
          </el-scrollbar>

        </div>
        <div style="padding: 5px;text-align: right;margin-top:auto">
          <el-pagination
            v-if="pagination"
            background
            @size-change="handleLoad"
            @current-change="handleLoad"
            :current-page="page"
            :page-size="pagination.pageSize"
            layout="total,prev, pager, next, jumper"
            :total="pagination.totalCount">
          </el-pagination>
        </div>


      </div>
    </div>


    <el-dialog
      v-model="cateDialog.visible"
      :title="cateDialog.title"
      :width="cateDialog.width"
    >
      <el-form ref="cateformRef" :model="cateForm" label-width="80px" :rules="cateRule">
        <el-form-item label="上级分类" v-if="cateForm.parent_id > 0">
          <el-input disabled v-model="cateForm.parent_name"></el-input>
        </el-form-item>
        <el-form-item label="名称" prop="name">
          <el-input v-model="cateForm.name" @keyup.enter="confirmEditCate"></el-input>
        </el-form-item>

      </el-form>
      <template #footer>
          <span class="dialog-footer">
            <el-button @click="cateDialog.visible = false">取消</el-button>
            <el-button type="primary" @click="confirmEditCate"
            >确定</el-button
            >
          </span>
      </template>
    </el-dialog>


    <el-dialog
      v-model="editVisible"
      title="编辑"
      width="400px"
    >
      <el-form ref="formRef" :model="attachmentForm" label-width="80px" :rules="attachmentRule">
        <el-form-item label="名称" prop="name">
          <el-input v-model="attachmentForm.name" @keyup.enter="updateAttachment"></el-input>
        </el-form-item>

      </el-form>
      <template #footer>
          <span class="dialog-footer">
            <el-button @click="editVisible = false">取消</el-button>
            <el-button type="primary" @click="updateAttachment"
            >确定</el-button
            >
          </span>
      </template>
    </el-dialog>

  </div>
</template>

<script>
  import {ref, onBeforeMount, watch, getCurrentInstance, reactive, computed} from 'vue'

  export default {
    name: "qk-attachment",
    props: {
      action: {
        type: String,
        required: true
      },
      headers: {
        type: Object,
        default: () => {}
      },
      data: {
        type: Object,
        default: () => {}
      },
      name: {
        type: String,
        default: 'file'
      },
      multiple: {
        type: Boolean,
        default: true,
      },
      size: {
        type: Number,
        default: 2
      },
      ext: {
        type: Array,
        default: () => ['jpg', 'png', 'gif', 'bmp']
      },
      max: {
        type: Number,
        default: 0
      },
      recycle: {
        type: Boolean,
        default: false,
      },
      showTab: {
        type: Boolean,
        default: false,
      },
      moduleName:{
        type: String,
        default: 'admin',
      },


    },
    setup(props, {emit}) {


      const attachments = ref([])
      const page = ref(1)
      const loading = ref(false)
      const pagination = ref(null)
      const loadingMore = ref(false)
      const allChecked = ref(false)
      const tabActiveName = ref('1')
      const selectCate = reactive({
        id: 0,
        label: '全部',
      })
      const {proxy} = getCurrentInstance()


      /*****************group start**************/
      const cateList = ref([
        {
          id: 0,
          label: '全部'
        }
      ]);
      const cateDialog = reactive({
        visible: false,
        title: '编辑',
        width: '400px'
      })
      const cateForm = reactive({
        id: 0,
        parent_id: 0,
        parent_name: '',
        name: ''
      })
      const cateRule = reactive({
        name: [
          {
            required: true,
            message: '名称不能为空',
            trigger: 'blur',
          },
          {
            min: 1,
            max: 10,
            message: '名称长度只能是1到10个字符',
            trigger: 'blur',
          },
        ],
      })
      const cateformRef = ref(null)
      const cateTree = ref(null)
      const handleClickCate = function (cate) {
        selectCate.id = cate.id
        selectCate.label = cate.label

      }
      const confirmEditCate = function () {
        cateformRef.value.validate((e) => {
          if (e) {
            Quick.request({
              url: `${props.moduleName}/resource/attachment/editCate`,
              method: 'POST',
              data: {
                id: cateForm.id,
                parent_id: cateForm.parent_id,
                name: cateForm.name,
              }
            }).then((res) => {
              if (res.code === 0) {
                cateDialog.visible = false
                getCateList()
              }
            })
          }
        })
      }
      const getCateList = function () {
        Quick.request({
          url: `${props.moduleName}/resource/attachment/cateList`,
          method: 'get',
          params: {
            is_recycle: props.recycle ? 1 : 0,
          }
        }).then((res) => {
          if (res.code === 0) {
            cateList.value = [
              {
                id: -1,
                label: '全部'
              },
              ...res.data
            ]
          }
        })
      }

      const addCate = function (data, type, node) {
        cateForm.name = ''
        cateForm.parent_name = ''
        cateForm.parent_id = 0
        cateForm.id = 0
        if (type === 'edit') {
          cateDialog.title = '编辑'
          cateForm.name = data.label
          cateForm.id = data.id
        } else {
          cateDialog.title = '添加'
          if (data.id) {
            cateForm.parent_name = data.label
            cateForm.parent_id = data.id
          }

        }
        cateDialog.visible = true
      }
      const delCate = function (id) {

        proxy.$confirm('你确定要删除吗！', '提示', {
          cancelButtonText: '取消',
          confirmButtonText: '确定',
          callback: function (action, instance) {
            if (action === 'confirm') {
              Quick.request({
                url:  `${props.moduleName}/resource/attachment/delCate`,
                method: 'POST',
                data: {
                  id: id
                }
              }).then((res) => {
                if (res.code === 0) {
                  getCateList()
                }
              })
            }
          }
        })

      }

      const selectMoveCateItem = ref(0)
      const selectMoveCate = function (data) {
        selectMoveCateItem.value = data.id
      }
      const handleMoveCate = function () {
        if (!selectMoveCateItem.value) {
          proxy.$message.error(`请选择迁移分类`)
          return
        }
        if (!selectItemIds.value.length) {
          proxy.$message.error(`迁移素材不能为空！`)
          return
        }
        proxy.$confirm('你确定要迁移吗！', '提示', {
          cancelButtonText: '取消',
          confirmButtonText: '确定',
          callback: function (action, instance) {
            if (action === 'confirm') {
              Quick.request({
                url:  `${props.moduleName}/resource/attachment/moveCate`,
                method: 'POST',
                data: {
                  cate_id: selectMoveCateItem.value > 0 ? selectMoveCateItem.value : 0,
                  ids: selectItemIds.value
                }
              }).then((res) => {
                if (res.code === 0) {
                  proxy.$message.success(`迁移成功！`)
                  loadList()
                }
              })
            }
          }
        })
      }


      const cateQuery = ref('')
      const onQueryChanged = () => {
        cateTree.value?.filter(cateQuery.value)
      }
      const filterMethod = (query, node) => {
        return node.label?.indexOf(query) !== -1
      }


      /***************group end*********************/



      const selectAllChange = function (val) {
        attachments.value = attachments.value.map((item) => {
          item.checked = val
          return item;
        })
      }

      const selectItemIds = computed(() => {
        let delIds = [];
        attachments.value.forEach((item) => {
          if (item.checked) {
            delIds.push(item.id);
          }
        })
        return delIds;
      })

      const handleLoad = function (currentPage) {

        page.value = currentPage;
        loadList();
      }


      watch(selectCate, () => {
        page.value = 1

        loadList()
      })

      watch(tabActiveName, () => {
        page.value = 1
        loadList()
      })


      const search = function () {
        page.value = 1
        loadList()
      }
      const keyword = ref('')
      const loadList = function () {

        if (loading.value) {
          return
        }
        loading.value = true

        Quick.request({
          url:  `${props.moduleName}/resource/attachment/list`,
          params: {
            type: tabActiveName.value,
            is_recycle: props.recycle ? 1 : 0,
            keyword: keyword.value,
            page: page.value,
            cate_id: selectCate.id,
          }
        }).then((res) => {
          loading.value = false
          if (!res.code) {
            attachments.value = res.data.list
            pagination.value = res.data.pagination
          }
        }).catch(() => {
          loading.value = false
        })


      }


      const delAttachments = function () {

        if (!selectItemIds.value.length) {
          proxy.$message.error(`请选择要删除的内容`)
          return
        }

        proxy.$confirm('你确定要删除吗！', '提示', {
          cancelButtonText: '取消',
          confirmButtonText: '确定',
          callback: function (action, instance) {
            if (action === 'confirm') {
              Quick.request({
                url:  `${props.moduleName}/resource/attachment/delete`,
                method: 'POST',
                data: {
                  ids: selectItemIds.value
                }
              }).then((res) => {
                proxy.$message.success(`删除成功`)
                loadList()
              })
            }
          }
        })


      }


      const attachmentForm = reactive({
        id: 0,
        name: ''
      })
      const attachmentRule = reactive({
        name: [
          {
            required: true,
            message: '名称不能为空',
            trigger: 'blur',
          },
          {
            min: 1,
            max: 30,
            message: '名称长度只能是1到30个字符',
            trigger: 'blur',
          },
        ],
      })

      const editVisible = ref(false)
      const formRef = ref(null)
      const updateAttachment = function () {
        formRef.value.validate((e) => {
          if (e) {
            Quick.request({
              url:  `${props.moduleName}/resource/attachment/update`,
              method: 'POST',
              data: {
                id: attachmentForm.id,
                name: attachmentForm.name,
              }
            }).then((res) => {
              if (res.code === 0) {
                proxy.$message.success(res.msg)
                editVisible.value = false
                loadList()
              }
            })
          }
        })
      }
      const showEdit = function (item) {
        editVisible.value = true
        attachmentForm.id = item.id
        attachmentForm.name = item.name
      }


      onBeforeMount(() => {
        getCateList();
        loadList()

        pagination.value = {
          pageSize: 10,
          totalCount: 1,
        }
      })

      let checkedAttachments = [];
      const handleImgClick = function (item) {

        if (item.checked) {
          item.checked = false;
          for (let i in checkedAttachments) {
            if (item.id === checkedAttachments[i].id) {
              checkedAttachments.splice(i, 1)
            }
          }
          emit('select', checkedAttachments)
          return;
        }

        if (props.multiple) {
          let checkedCount = 0;
          for (let i in attachments.value) {
            if (attachments.value[i].checked) {
              checkedCount++
            }
          }
          if (props.max && !item.checked && checkedCount >= props.max) {
            proxy.$message.warning(`最多只能选择${props.max}个文件`)
            return
          }
          item.checked = true;
          checkedAttachments.push(item);
        } else {
          for (let i in attachments.value) {
            attachments.value[i].checked = false;
          }
          item.checked = true;
          checkedAttachments = [item];
        }
        emit('select', checkedAttachments)
      }


      const uploadData = ref({
        dialogImageIndex: 0,
        imageViewerVisible: false,
        progress: {
          preview: '',
          percent: 0
        }
      })

      const updateRequestData = computed(() => {
        return {
          ...props.data,
          cate_id: selectCate.id
        }
      })

      function beforeUpload(file) {
        const fileName = file.name.split('.')
        const fileExt = fileName[fileName.length - 1]
        const isTypeOk = props.ext.indexOf(fileExt) >= 0
        const isSizeOk = file.size / 1024 / 1024 < props.size
        if (!isTypeOk) {
          proxy.$message.error(`上传图片只支持 ${props.ext.join(' / ')} 格式！`)
        }
        if (!isSizeOk) {
          proxy.$message.error(`上传图片大小不能超过 ${props.size}MB！`)
        }
        if (isTypeOk && isSizeOk) {
          uploadData.value.progress.preview = URL.createObjectURL(file)
        }
        return isTypeOk && isSizeOk
      }

      function onProgress(file) {
        uploadData.value.progress.percent = ~~file.percent
      }

      function onSuccess(res) {
        uploadData.value.progress.preview = ''
        uploadData.value.progress.percent = 0
        console.log('----onSuccess----------', res)
        attachments.value.unshift(res.data)
        emit('on-success', res)
      }


      const uploading = ref(false);
      const uploadCompleteFilesNum = ref(0);
      const uploadFilesNum = ref(0);

      let canvas = null
      const updateVideo = function () {
        if (!canvas) {
          canvas = document.getElementById('app-attachment-canvas');
        }
        for (let i in attachments.value) {
          if (attachments.value[i].type == 2) {
            if (attachments.value[i].duration) {
              continue;
            }
            let times = 0;
            let video = null;
            const maxRetry = 10;
            const id = 'app_attachment_' + this._uid + '_' + i;
            const timer = setInterval(() => {
              times++;
              if (times >= maxRetry) {
                clearInterval(timer);
              }
              if (!video) {
                video = document.getElementById(id);
              }
              if (!video) {
                return;
              }
              try {
                const zoom = 0.15;
                canvas.width = video.videoWidth * zoom;
                canvas.height = video.videoHeight * zoom;
                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                attachments.value[i].cover_pic_src = canvas.toDataURL('image/jpg');
              } catch (e) {
                console.warn('获取视频封面异常: ', e);
              }

              if (video.duration && !isNaN(video.duration)) {
                let m = Math.trunc(video.duration / 60);
                let s = Math.trunc(video.duration) % 60;
                m = m < 10 ? `0${m}` : `${m}`;
                s = s < 10 ? `0${s}` : `${s}`;
                attachments.value[i].duration = `${m}:${s}`;
                clearInterval(timer);
              }
            }, 500);
          }
        }
      }


      return {
        tabActiveName,
        handleImgClick,
        handleLoad,
        keyword,
        search,
        selectAllChange,
        selectItemIds,
        delAttachments,
        onSuccess,
        onProgress,
        beforeUpload,
        addCate,
        delCate,
        cateRule,
        cateForm,
        cateDialog,
        cateformRef,
        cateTree,
        onQueryChanged,
        cateQuery,
        filterMethod,
        selectMoveCateItem,
        selectMoveCate,
        handleMoveCate,
        handleClickCate,
        confirmEditCate,
        loading,
        uploadData,
        selectCate,
        updateRequestData,
        uploadFilesNum,
        uploadCompleteFilesNum,
        page,
        pagination,
        cateList,
        attachments,
        allChecked,
        attachmentForm,
        attachmentRule,
        editVisible,
        formRef,
        updateAttachment,
        showEdit,
      }
    }
  }
</script>

<style scoped lang="scss">


  .tool-box {
    padding: 10px;

    .search {
      width: 300px;
    }
  }

  .attachment-box {
    border: 1px solid rgb(227, 227, 227);
    margin-bottom: 10px;
    min-height: 300px;

    .attachment-left {
      border-right: 1px solid rgb(227, 227, 227);
      width: 230px;
    }

    .image-list {
      /*min-width: 500px;*/
      display: flex;
      flex-wrap: wrap;

      .item-upload {
        box-shadow: none;
        border: 1px dashed #b2b6bd;
        height: 100px !important;
        width: 100px !important;
        line-height: 80px;
        margin: 17.5px !important;
        padding: 0 !important;

        .progress {
          width: 100px;
          height: 100px;
          display: flex;
          justify-content: center;
          align-items: center;
        }
      }

      .item-upload:hover {
        box-shadow: none;
        border: 1px dashed #409EFF;
      }

      .image-item-box:hover {
        .image-name-box {
          .name-btn {
            display: block;
          }
        }
      }

      .image-item-box {
        display: inline-block;
        cursor: pointer;
        position: relative;
        float: left;
        width: 120px;
        height: 140px;
        margin: 7.5px;
        text-align: center;
        padding: 10px 10px 0;

        .image-name-box {
          .name-btn {
            display: none;
            line-height: 17px;
          }
        }


        .image-name {
          color: #666666;
          font-size: 13px;
          height: 17px;
          word-break: break-all;
          text-overflow: ellipsis;
          display: -webkit-box;
          -webkit-box-orient: vertical;
          -webkit-line-clamp: 1;
          overflow: hidden;
        }

        .image-full-name {
          display: none;
          position: absolute;
          white-space: nowrap;
          z-index: 9;
          background: #eee;
          height: 20px;
          line-height: 20px;
          color: #555;
          border: 1px solid #ebebeb;
          padding: 0 5px
        }

        .img {

        }
      }

      .image-item-box:hover {
        .image-full-name {
          display: block;
        }
      }

      .image-item-box.checked {
        box-shadow: 0 0 0 1px #bedcf8;
        background: #e2f0ff;
        border-radius: 5px;
      }
    }
  }

  .is-current {
    .tree-item .tree-btn {
      display: block;
    }
  }

  .tree-item:hover {
    .tree-btn {
      display: block;
    }
  }

  .tree-item {
    color: #8f8f8f;
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-right: 10px;


    .tree-btn {
      /*margin-left: 60px;*/
      display: none;

      .tree-btn-icon {
        padding-left: 15px;
      }

    }


  }


  .flex,
  .flex-row,
  .flex-x {
    display: flex;
  }

  .flex-y,
  .flex-column {
    display: flex;
    flex-direction: column;
  }

  .flex-x-center {
    display: flex;
    justify-content: center;
  }

  .flex-xy-center {
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .flex-y-center {
    display: flex;
    align-items: center;
  }

  .flex-x-left {
    display: flex;
  }

  .flex-x-reverse,
  .flex-row-reverse {
    flex-direction: row-reverse;
  }

  .flex-y-reverse,
  .flex-column-reverse {
    flex-direction: column-reverse;
  }

  // 换行
  .flex-wrap {
    flex-wrap: wrap;
  }

  // 反向换行
  .flex-wrap-reverse {
    flex-wrap: wrap-reverse;
  }

  // 主轴起点对齐
  .flex-start {
    justify-content: flex-start
  }

  // 主轴中间对齐
  .flex-center {
    justify-content: center
  }

  // 主轴终点对齐
  .flex-end {
    justify-content: flex-end
  }

  // 主轴等比间距
  .flex-between {
    justify-content: space-between
  }

  // 主轴均分间距
  .flex-around {
    justify-content: space-around
  }

  // 交叉轴起点对齐
  .flex-items-start {
    align-items: flex-start
  }

  // 交叉轴中间对齐
  .flex-items-center {
    align-items: center
  }

  // 交叉轴终点对齐
  .flex-items-end {
    align-items: flex-end
  }

  // 交叉轴第一行文字基线对齐
  .flex-items-baseline {
    align-items: baseline
  }

  // 交叉轴方向拉伸对齐
  .flex-items-stretch {
    align-items: stretch
  }


  // 以下属于项目(子元素)的类

  // 子元素交叉轴起点对齐
  .flex-self-start {
    align-self: flex-start
  }

  // 子元素交叉轴居中对齐
  .flex-self-center {
    align-self: center
  }

  // 子元素交叉轴终点对齐
  .flex-self-end {
    align-self: flex-end
  }

  // 子元素交叉轴第一行文字基线对齐
  .flex-self-baseline {
    align-self: baseline
  }

  // 子元素交叉轴方向拉伸对齐
  .flex-self-stretch {
    align-self: stretch
  }

  // 多轴交叉时的对齐方式

  // 起点对齐
  .flex-content-start {
    align-content: flex-start
  }

  // 居中对齐
  .flex-content-center {
    align-content: center
  }

  // 终点对齐
  .flex-content-end {
    align-content: flex-end
  }

  // 两端对齐
  .flex-content-between {
    align-content: space-between
  }

  // 均分间距
  .flex-content-around {
    align-content: space-around
  }

  // 全部居中对齐
  .flex-middle {
    justify-content: center;
    align-items: center;
    align-self: center;
    align-content: center
  }

  // 是否可以放大
  .flex-grow {
    flex-grow: 1
  }

  // 是否可以缩小
  .flex-shrink {
    flex-shrink: 1
  }


  /** end*******************/

</style>
