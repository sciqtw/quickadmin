import { h, resolveComponent ,isVNode} from 'vue';

const setProps = function (domData,attrs) {
  domData = Object.assign({},domData.props,attrs)
  // delete domData.children
  return domData;
};



const renderDiv = function (domData,attrs) {
  // console.log('----renderDiv', domData);
  if (!domData || !domData.children) {
    return [];
  }
  const child = domData.children;
  // let slotName = 'default';
  let list = [];
  const defaultSlot = [];
  const slots = {};

  if (Array.isArray(child) && child.length > 0) {
    child.forEach((item) => {
      const node = renderDiv(item,attrs);
      if (item.slot && item.slot !== 'default' && domData.component.indexOf('-') !== -1) {
        if (slots[item.slot]) {
          slots[item.slot].push(node);
        } else {
          slots[item.slot] = [node];
        }
      } else {
        defaultSlot.push(node);
      }
    });
    // child = child.map((item) => renderDiv(item));
  } else {
    list = child;
  }

  if (JSON.stringify(slots) !== '{}') {

    const temp = {};
    for(let key in slots){
      temp[key] = () => slots[key]
    }
    if (defaultSlot.length) {
      temp.default = () => {
        return defaultSlot
      };
    }
    list = temp

  } else if (defaultSlot.length) {
    list = () => defaultSlot ;
  }else if(JSON.stringify(list) === '{}' || list.length === 0){
    list = ''
  }else if(typeof list === 'string'){
    list = () => child
  }else if(list.component){
    list = () => list
  }


  const props = setProps(domData,attrs);
  let myCom = domData.component;
  if (!isVNode(domData.component) && domData.component.indexOf('-') !== -1) {
    myCom = resolveComponent(domData.component);
  }


  return h(myCom, props, list);
};

/**
 * 渲染组件
 */
export default {
  name: 'JsonRender',
  componentName: 'JsonRender',
  props: {
    renderData: {
      type: Object,
      default: () => {},
    },
  },
  data() {
    return {
      stats: {
        nickname: 'fdfd',
      },
    };
  },
  render() {
    let vnode = h('div',{},'error')
    const props = {
      ...this.$attrs
    }
    try {
       vnode = renderDiv(this.renderData,props)
    }catch (e) {
      console.log('-----------e',e)
    }
    return vnode;
  },

  methods: {

  },
};
