(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-712824d7"],{"24a7":function(e,t,n){"use strict";n("91a8")},"8cdb":function(e,t,n){"use strict";n.r(t);var c=n("8bbf"),o=function(e){return Object(c["pushScopeId"])("data-v-3a57b1b2"),e=e(),Object(c["popScopeId"])(),e},r={class:"notfound"},a={class:"content"},u=o((function(){return Object(c["createElementVNode"])("h1",null,"404",-1)})),i=o((function(){return Object(c["createElementVNode"])("div",{class:"desc"},"抱歉，你访问的页面不存在",-1)}));function d(e,t,n,o,d,s){var b=Object(c["resolveComponent"])("svg-icon"),l=Object(c["resolveComponent"])("el-button");return Object(c["openBlock"])(),Object(c["createElementBlock"])("div",r,[Object(c["createVNode"])(b,{name:"404"}),Object(c["createElementVNode"])("div",a,[u,i,Object(c["createVNode"])(l,{type:"primary",onClick:s.goBack},{default:Object(c["withCtx"])((function(){return[Object(c["createTextVNode"])(Object(c["toDisplayString"])(d.countdown)+"秒后，返回首页",1)]})),_:1},8,["onClick"])])])}var s={beforeRouteLeave:function(e,t,n){clearInterval(this.inter),n()},data:function(){return{inter:null,countdown:5}},mounted:function(){var e=this;this.inter=setInterval((function(){e.countdown--,0==e.countdown&&(clearInterval(e.inter),e.goBack())}),1e3)},methods:{goBack:function(){this.$router.push("/")}}},b=(n("24a7"),n("6b0d")),l=n.n(b);const f=l()(s,[["render",d],["__scopeId","data-v-3a57b1b2"]]);t["default"]=f},"91a8":function(e,t,n){}}]);
//# sourceMappingURL=chunk-712824d7.4994613c.js.map