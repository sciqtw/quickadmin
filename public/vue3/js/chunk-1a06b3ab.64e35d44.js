(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-1a06b3ab"],{"270a":function(e,c,n){},4190:function(e,c,n){"use strict";n("270a")},"5d2c":function(e,c,n){"use strict";n.r(c);n("99af"),n("d3b7");var t=n("8bbf"),o=n("5502"),u=n("6c02"),a={id:"qk_resource"},r={name:"resource-page",props:{moduleName:{type:String,default:""},resourceName:{type:String,default:"index"},actionName:{type:String,default:"index"},func:{type:String,default:""}},setup:function(e){var c=e,n=(Object(o["c"])(),Object(u["c"])()),r=(Object(u["d"])(),Object(t["ref"])({})),i=(Object(t["ref"])(!1),Object(t["ref"])({}),Object(t["ref"])(1)),d=Object(t["computed"])((function(){return c.moduleName})),l=Object(t["computed"])((function(){return Object.assign({},n.query)})),f=Object(t["computed"])((function(){var e="".concat(d.value,"/resource/").concat(c.resourceName,"/").concat(c.actionName);return c.func&&(e+="/"+c.func),e})),b=Object(t["computed"])((function(){return n.query.repage||""})),s=function(){},p=function(){Quick.api.loading({target:".loading_class",background:"rgba(0, 0, 0, 0.1)"}),Quick.request().get(f.value,{params:l.value}).then((function(e){r.value=e.data,Quick.api.closeLoading()})).catch((function(e){console.log("error",e)})).finally((function(){Quick.api.closeLoading()}))};return Object(t["watch"])((function(){return b.value+f.value}),(function(){p()})),Object(t["onMounted"])((function(){Quick.$on("refresh",(function(){p()})),p()})),function(c,n){var o=Object(t["resolveComponent"])("json-render");return Object(t["openBlock"])(),Object(t["createElementBlock"])("div",a,[r.value.component?(Object(t["openBlock"])(),Object(t["createBlock"])(o,{"render-data":r.value,"module-name":e.moduleName,resource:e.resourceName,key:i.value,onRefresh:s},null,8,["render-data","module-name","resource"])):Object(t["createCommentVNode"])("",!0)])}}},i=(n("4190"),n("6b0d")),d=n.n(i);const l=d()(r,[["__scopeId","data-v-ba30ba64"]]);c["default"]=l}}]);
//# sourceMappingURL=chunk-1a06b3ab.64e35d44.js.map