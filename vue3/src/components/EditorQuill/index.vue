<template>
  <div class="editor-quill">
    <div ref="editor" class="editor" :style="style"></div>
  </div>
</template>

<script >
  import { computed, defineComponent, onMounted, ref, watch } from "vue";
  import Quill from "quill";
  import "quill/dist/quill.snow.css";

  export default defineComponent({
    name: "editor-quill",
    props: {
      options: Object,
      modelValue: null,
      height: {
        type:[String, Number],
        default:'400px'
      },
      width: [String, Number]
    },
    emits: ["update:modelValue", "load"],
    setup(props, { emit }) {

      let quill = null;
      const editor = ref(null);
      const upload = ref(null);

      // 文本内容
      const content = ref("");

      // 光标位置
      const cursorIndex = ref(0);

      // 上传处理
      function uploadFileHandler() {
        const selection = quill.getSelection();

        if (selection) {
          cursorIndex.value = selection.index;
        }
        emit("upload-open");
      }

      // 文件确认
      function setUploadFiles(files) {
        if (files.length > 0) {
          // 批量插入图片
          files.forEach((file, i) => {
            const type = file.type || 'image';

            quill.insertEmbed(cursorIndex.value + i, type, file.url, Quill.sources.USER);
          });

          // 移动光标到图片后一位
          quill.setSelection(cursorIndex.value + files.length);
        }
      }

      // 设置内容
      function setContent(val) {
        quill.root.innerHTML = val || "";
      }

      // 编辑框样式
      const style = computed(() => {
        const height = typeof props.height  === 'number' ? props.height + "px" : props.height;
        const width = typeof props.width === 'number' ? props.width + "px" : props.width;

        return {
          height,
          width
        };
      });

      // 监听绑定值
      watch(
        () => props.modelValue,
        (val) => {
          if (val) {
            if (val !== content.value) {
              setContent(val);
            }
          } else {
            setContent("");
          }
        }
      );

      onMounted(function () {
        console.log('---------editer',editor)
        // 实例化
        quill = new Quill(editor.value, {
          theme: "snow",
          placeholder: "输入内容",
          modules: {
            toolbar: [
              ["bold", "italic", "underline", "strike"],
              ["blockquote", "code-block"],
              [{ header: 1 }, { header: 2 }],
              [{ list: "ordered" }, { list: "bullet" }],
              [{ script: "sub" }, { script: "super" }],
              [{ indent: "-1" }, { indent: "+1" }],
              [{ direction: "rtl" }],
              [{ size: ["small", false, "large", "huge"] }],
              [{ header: [1, 2, 3, 4, 5, 6, false] }],
              [{ color: [] }, { background: [] }],
              [{ font: [] }],
              [{ align: [] }],
              ["clean"],
              ["link", "image"]
            ]
          },
          ...props.options
        });

        // 添加图片工具
        quill.getModule("toolbar").addHandler("image", uploadFileHandler);

        // 监听输入
        quill.on("text-change", () => {
          content.value = quill.root.innerHTML;
          emit("update:modelValue", content.value);
        });

        // 设置内容
        setContent(props.modelValue);

        // 加载回调
        emit("load", quill);
      });

      return {
        content,
        editor,
        upload,
        cursorIndex,
        style,
        setContent,
        setUploadFiles
      };
    }
  });
</script>

<style lang="scss" >
  .editor-quill {
    background-color: #fff;

    .ql-snow {
      line-height: 22px !important;
    }

    .el-upload,
    #quill-upload-btn {
      display: none;
    }

    .ql-snow {
      border: 1px solid #dcdfe6;
    }

    .ql-snow .ql-tooltip[data-mode="link"]::before {
      content: "请输入链接地址:";
    }

    .ql-snow .ql-tooltip.ql-editing a.ql-action::after {
      border-right: 0px;
      content: "保存";
      padding-right: 0px;
    }

    .ql-snow .ql-tooltip[data-mode="video"]::before {
      content: "请输入视频地址:";
    }

    .ql-snow .ql-picker.ql-size .ql-picker-label::before,
    .ql-snow .ql-picker.ql-size .ql-picker-item::before {
      content: "14px";
    }

    .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="small"]::before,
    .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="small"]::before {
      content: "10px";
    }

    .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="large"]::before,
    .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="large"]::before {
      content: "18px";
    }

    .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="huge"]::before,
    .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="huge"]::before {
      content: "32px";
    }

    .ql-snow .ql-picker.ql-header .ql-picker-label::before,
    .ql-snow .ql-picker.ql-header .ql-picker-item::before {
      content: "文本";
    }

    .ql-snow .ql-picker.ql-header .ql-picker-label[data-value="1"]::before,
    .ql-snow .ql-picker.ql-header .ql-picker-item[data-value="1"]::before {
      content: "标题1";
    }

    .ql-snow .ql-picker.ql-header .ql-picker-label[data-value="2"]::before,
    .ql-snow .ql-picker.ql-header .ql-picker-item[data-value="2"]::before {
      content: "标题2";
    }

    .ql-snow .ql-picker.ql-header .ql-picker-label[data-value="3"]::before,
    .ql-snow .ql-picker.ql-header .ql-picker-item[data-value="3"]::before {
      content: "标题3";
    }

    .ql-snow .ql-picker.ql-header .ql-picker-label[data-value="4"]::before,
    .ql-snow .ql-picker.ql-header .ql-picker-item[data-value="4"]::before {
      content: "标题4";
    }

    .ql-snow .ql-picker.ql-header .ql-picker-label[data-value="5"]::before,
    .ql-snow .ql-picker.ql-header .ql-picker-item[data-value="5"]::before {
      content: "标题5";
    }

    .ql-snow .ql-picker.ql-header .ql-picker-label[data-value="6"]::before,
    .ql-snow .ql-picker.ql-header .ql-picker-item[data-value="6"]::before {
      content: "标题6";
    }

    .ql-snow .ql-picker.ql-font .ql-picker-label::before,
    .ql-snow .ql-picker.ql-font .ql-picker-item::before {
      content: "标准字体";
    }

    .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="serif"]::before,
    .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="serif"]::before {
      content: "衬线字体";
    }

    .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="monospace"]::before,
    .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="monospace"]::before {
      content: "等宽字体";
    }
  }
</style>
