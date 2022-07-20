import { createVNode, render,isVNode ,createApp} from 'vue';
import components from '../components';
import Quick from '../Quick';

export function DynamicModal(component, props,close) {
  console.log('---DynamicModalDynamicModal-',component,props)



  // 创建父级包裹对象
  const container = document.createElement('div');
  // 创建vue Node实例, 并传递props(强制传递onClose方法)
  const obj = new Quick({})
  const vm = obj.inits(component,{
    ...props,
    onCloseBefore: (action, params, vm, done) => {

      close(action, params, vm, done);
    },
    onClose: () => {
      document.body.removeChild(container);
      // 返回子组件传递的数据

    },
  },false)

  // 把vue Node实例渲染到包裹对象中
  // render(vm, container);
  vm.mount(container)

  // 将包裹对象插入到body中
  document.body.appendChild(container);
  return vm
}
