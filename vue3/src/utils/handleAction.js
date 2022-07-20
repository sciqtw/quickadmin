import {routePush} from './routePush';

class Actions {
  constructor(options) {

    this.options = options
  }

  /**
   * 响应回调动作
   *
   * @param type  事件类型
   * @param data
   */
  emit(type, data) {
    if (this.options.callback) {
      this.options.callback(type, data)
    } else if (typeof this.options[type] === 'function') {
      this.options[type](data);
    }
  }

  event(action) {

    if (action.isQuick) {
      Quick.$emit(action.event, action.data)
    } else {
      this.emit(action.event, action.data)
    }
  }

  openInNewTab(action) {
    window.open(action.url, '_blank')
  }

  push(action) {

    if (this.options.params) {
      action.query = Object.assign({}, action.query || {}, this.options.params)
    }
    routePush(action)
  }

  redirect(action) {
    window.location = action.url
  }

  download(action) {
    const link = document.createElement('a')
    link.href = action.link
    link.download = action.name
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
  }

  message(action) {
    window.Quick.message(Object.assign({}, action))
  }

  confirm(confirm) {
    Quick.app.config.globalProperties.$confirm(confirm.msg
      , confirm.title,
      confirm.attributes
    ).then(() => {
      if (confirm.confirm) {
        this.executeAction(confirm.confirm)
      }
    }).catch(() => {
      if (confirm.cancel) {
        this.executeAction(confirm.cancel)
      }
    })
  }

  _modal(content){
    let self = this;
    const config = Object.assign({}, content.config.props, {
      component:content.config.component,
      beforeClose: function (action, data, done) {
        if (action === 'action') {
          self.executeAction(data);
          done();
        } else {
          if (self.options.beforeClose) {
            self.options.beforeClose(action, data, done)
          } else {
            self.emit(action, data)
            done();
          }
        }
      }
    })
    if (typeof content.content === 'string') {
      content.content = {
        url: content.content,
        method: 'POST',
        data: self.options.data,
        params: self.options.params,
      }
    }else if(content.content.url){
      content.content.data = Object.assign({},self.options.data,content.content.data)
      content.content.params = Object.assign({},self.options.params,content.content.params)
    }

    Quick.api.open(content.content, config);
  }

  drawer(content){
    this._modal(content)
  }

  dialog(content) {
    this._modal(content)
  }

  request(action) {
    if (this.options.params) {
      action.params = Object.assign({}, action.params, this.options.params)
    }
    if (this.options.data) {
      action.data = Object.assign({}, action.data, this.options.data)
    }
    action.headers = Object.assign({}, action.headers, {'request-source': 'action'})

    const Quick = window.Quick
    Quick.api.loading()

    Quick.request(action).then(response => {

      Quick.api.closeLoading()
      if (response.action) {
        this.executeAction(response.action)
      } else {
        this.emit(response)
      }
    }).catch((error) => {
      Quick.api.closeLoading()
    }).finally(() =>{
      Quick.api.closeLoading()
    })
  }

  executeAction(actionList) {
    if (Array.isArray(actionList)) {
      actionList.map(item => {
        this.execute(item)
      })
    } else {
      this.execute(actionList)
    }
  }

  execute = (action) => {
    // console.log('-execute------------', action)
    if (this[action.action]) {
      if (action.delay) {
        // 延迟执行
        setTimeout(() => {
          this[action.action](action.params)
        }, action.delay)
      } else {
        this[action.action](action.params)
      }
    }
  }

}


export function useAction(config) {

  const actionObj = new Actions(config)

  return {
    action: (actionList) => {
      actionObj.executeAction(actionList)
    },
    setData:(data) => {
      actionObj.options.data = Object.assign({},actionObj.options.data,data)
    },
    setParams:(data) => {
      actionObj.options.params = Object.assign({},actionObj.options.params,data)
    },
  };
}


