(()=>{"use strict";var e={744:(e,n)=>{n.Z=(e,n)=>{const t=e.__vccOpts||e;for(const[e,o]of n)t[e]=o;return t}}},n={};function t(o){var r=n[o];if(void 0!==r)return r.exports;var c=n[o]={exports:{}};return e[o](c,c.exports,t),c.exports}(()=>{const e=Vue;const n={};var o=t(744);const r=(0,o.Z)(n,[["render",function(n,t,o,r,c,u){return(0,e.openBlock)(),(0,e.createElementBlock)("div",null," test-component ")}]]);var c=(0,e.createTextVNode)(" plugin-test ");const u={setup:function(){return{name:(0,e.ref)("hello")}}},i=(0,o.Z)(u,[["render",function(n,t,o,r,u,i){return(0,e.openBlock)(),(0,e.createElementBlock)("div",null,[c,(0,e.createElementVNode)("div",null,(0,e.toDisplayString)(r.name),1)])}]]);window.Quick.booting((function(n,t){t.addRoute({path:"/",component:{render:function(){return(0,e.h)((0,e.resolveComponent)("quick-layout"))}},children:[{name:"code",path:"/plugin-test",component:i}]}),n.component("qk-test",r)}))})()})();