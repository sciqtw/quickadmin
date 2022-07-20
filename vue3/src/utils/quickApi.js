import { ElLoading } from 'element-plus'

export default {
  loadObj: null,
  modalList: [],
  modalService:null,
  timer:null,
  loading: function(options) {
    this.closeLoading()
    if(!options){
      options = { body: true,background: 'rgba(0, 0, 0, 0.5)', }
    }
    this.timer = setTimeout(() => {
      this.loadObj  = ElLoading.service(options)
      setTimeout(() => {
        this.loadObj.close()
      },20000)
    },500)

  },
  closeLoading() {
    if(this.timer){
      clearTimeout(this.timer)
    }
    if (this.loadObj) {
      this.loadObj.close()
    }
  },
  openInNewTab(url) {
    window.open(url, '_blank')
  },
  push(action) {
    this.app.$router.push(action)
  },
  redirect(url) {
    window.location = url
  },
  download(url, name) {
    const link = document.createElement('a')
    link.href = url
    if (name) {
      link.download = name
    }
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
  },
  confirm(confirm) {
    this.app.$confirm(confirm.msg
      , confirm.title,
      confirm.attributes
    ).then(() => {
      if (confirm.confirm) {
        confirm.confirm()
      }
    }).catch(() => {
      if (confirm.cancel) {
        confirm.cancel()
      }
    })
  },
  message(action) {
    window.Quick.message(Object.assign({}, action))
  },
  open(content,options){
    if(this.modalService){
      return this.modalService.open(content,options)
    }
  },
  close(event,data,id){

    if(this.modalService){
      const index = this.modalList.findIndex((x) => x.id === id);
      let modal = null
      if(index < 0){
        modal = this.modalList.pop()
      }else{
        modal = this.modalList[index]
      }
      if(modal){
        modal.close(event,data);
        this.modalService.close(modal.modalId)
      }

    }
  },
  _close(id){

    if(this.modalService){
      const index = this.modalList.findIndex((x) => x.id === id);
      let modal = null
      if(index >= 0){
        console.log('splice-----------',index)
        this.modalList.splice(index,1)
      }
    }
  },
  pushDialog(dialog){
    this.modalList.push(dialog)
  },
  createRender: function(h, component) {
    let child = component.children
    if (Array.isArray(child) && child.length) {
      child = child.map((item, index) => {
        return this.createRender(h, item)
      })
    }

    const props = Object.assign({}, {
      attrs: {
        ...component.attributes
      },
      props: {
        ...component.props
      }
    },
    component.extraAttrs)
    return h(component.component, props, child)
  }
}
