<template>
  <span v-if="dot" ref="badge" :class="classes">
    <slot />
    <sup v-show="badge" :class="dotClasses" :style="styles" />
  </span>
  <span v-else-if="status || color" ref="badge" :class="classes" class="ant-badge-status">
    <span :class="statusClasses" :style="statusStyles" />
    <span class="ant-badge-status-text"><slot name="text">{{ text }}</slot></span>
  </span>
  <span v-else ref="badge" :class="classes">
    <slot />
    <sup v-if="$slots.count" :style="styles" :class="customCountClasses"><slot name="count" /></sup>
    <sup v-else-if="hasCount" v-show="badge" :style="styles" :class="countClasses"><slot name="text">{{ finalCount }}</slot></sup>
  </span>
</template>
<script>

  const initColorList = ['blue', 'green', 'red', 'yellow', 'pink', 'magenta', 'volcano', 'orange', 'gold', 'lime', 'cyan', 'geekblue', 'purple']
  const prefixCls = 'ant-badge'
  const oneOf = function(value, validList) {
    for (let i = 0; i < validList.length; i++) {
      if (value === validList[i]) {
        return true
      }
    }
    return false
  }
  export default {
    name: 'Badge',
    props: {
      count: Number,
      dot: {
        type: Boolean,
        default: false
      },
      overflowCount: {
        type: [Number, String],
        default: 99
      },
      className: String,
      showZero: {
        type: Boolean,
        default: false
      },
      text: {
        type: String,
        default: ''
      },
      status: {
        validator(value) {
          return oneOf(value, ['success', 'processing', 'default', 'error', 'warning'])
        }
      },
      type: {
        validator(value) {
          return oneOf(value, ['success', 'primary', 'normal', 'error', 'warning', 'info'])
        }
      },
      offset: {
        type: Array
      },
      color: {
        type: String
      }
    },
    computed: {
      classes() {
        return `${prefixCls}`
      },
      dotClasses() {
        return `${prefixCls}-dot`
      },
      countClasses() {
        return [
          `${prefixCls}-count`,
          {
            [`${this.className}`]: !!this.className,
            [`${prefixCls}-count-alone`]: this.alone,
            [`${prefixCls}-count-${this.type}`]: !!this.type
          }
        ]
      },
      customCountClasses() {
        return [
          `${prefixCls}-count`,
          `${prefixCls}-count-custom`,
          {
            [`${this.className}`]: !!this.className
          }
        ]
      },
      statusClasses() {
        return [
          `${prefixCls}-status-dot`,
          {
            [`${prefixCls}-status-${this.status}`]: !!this.status,
            [`${prefixCls}-status-${this.color}`]: !!this.color && oneOf(this.color, initColorList)
          }
        ]
      },
      statusStyles() {
        return oneOf(this.color, initColorList) ? {} : { backgroundColor: this.color }
      },
      styles() {
        const style = {}
        if (this.offset && this.offset.length === 2) {
          style['margin-top'] = `${this.offset[0]}px`
          style['margin-right'] = `${this.offset[1]}px`
        }
        return style
      },
      finalCount() {
        if (this.text !== '') return this.text
        return parseInt(this.count) >= parseInt(this.overflowCount) ? `${this.overflowCount}+` : this.count
      },
      badge() {
        let status = false

        if (this.count) {
          status = !(parseInt(this.count) === 0)
        }

        if (this.dot) {
          status = true
          if (this.count !== null) {
            if (parseInt(this.count) === 0) {
              status = false
            }
          }
        }

        if (this.text !== '') status = true

        return status || this.showZero
      },
      hasCount() {
        if (this.count || this.text !== '') return true
        if (this.showZero && parseInt(this.count) === 0) return true
        else return false
      },
      alone() {
        return this.$slots.default === undefined
      }
    }
  }
</script>
<style>
  .ant-badge {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    color: rgba(0, 0, 0, 0.65);
    font-size: 14px;
    font-variant: tabular-nums;
    line-height: 1.5;
    list-style: none;
    font-feature-settings: 'tnum';
    position: relative;
    display: inline-block;
    color: unset;
    line-height: 1;
  }
  .ant-badge-count {
    z-index: auto;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    color: #fff;
    font-weight: normal;
    font-size: 12px;
    line-height: 20px;
    white-space: nowrap;
    text-align: center;
    background: #f5222d;
    border-radius: 10px;
    box-shadow: 0 0 0 1px #fff;
  }
  .ant-badge-count a,
  .ant-badge-count a:hover {
    color: #fff;
  }
  .ant-badge-multiple-words {
    padding: 0 8px;
  }
  .ant-badge-dot {
    z-index: auto;
    width: 6px;
    height: 6px;
    background: #f5222d;
    border-radius: 100%;
    box-shadow: 0 0 0 1px #fff;
  }
  .ant-badge-count,
  .ant-badge-dot,
  .ant-badge .ant-scroll-number-custom-component {
    position: absolute;
    top: 0;
    right: 0;
    transform: translate(50%, -50%);
    transform-origin: 100% 0%;
  }
  .ant-badge-status {
    line-height: inherit;
    vertical-align: baseline;
  }
  .ant-badge-status-dot {
    position: relative;
    top: -1px;
    display: inline-block;
    width: 6px;
    height: 6px;
    vertical-align: middle;
    border-radius: 50%;
  }
  .ant-badge-status-success {
    background-color: #52c41a;
  }
  .ant-badge-status-processing {
    position: relative;
    background-color: #1890ff;
  }
  .ant-badge-status-processing::after {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: 1px solid #1890ff;
    border-radius: 50%;
    -webkit-animation: antStatusProcessing 1.2s infinite ease-in-out;
    animation: antStatusProcessing 1.2s infinite ease-in-out;
    content: '';
  }
  .ant-badge-status-default {
    background-color: #d9d9d9;
  }
  .ant-badge-status-error {
    background-color: #f5222d;
  }
  .ant-badge-status-warning {
    background-color: #faad14;
  }
  .ant-badge-status-pink {
    background: #eb2f96;
  }
  .ant-badge-status-magenta {
    background: #eb2f96;
  }
  .ant-badge-status-red {
    background: #f5222d;
  }
  .ant-badge-status-volcano {
    background: #fa541c;
  }
  .ant-badge-status-orange {
    background: #fa8c16;
  }
  .ant-badge-status-yellow {
    background: #fadb14;
  }
  .ant-badge-status-gold {
    background: #faad14;
  }
  .ant-badge-status-cyan {
    background: #13c2c2;
  }
  .ant-badge-status-lime {
    background: #a0d911;
  }
  .ant-badge-status-green {
    background: #52c41a;
  }
  .ant-badge-status-blue {
    background: #1890ff;
  }
  .ant-badge-status-geekblue {
    background: #2f54eb;
  }
  .ant-badge-status-purple {
    background: #722ed1;
  }
  .ant-badge-status-text {
    margin-left: 8px;
    color: rgba(0, 0, 0, 0.65);
    font-size: 14px;
  }
  .ant-badge-dot-status {
    line-height: 1;
  }
  .ant-badge-zoom-appear,
  .ant-badge-zoom-enter {
    -webkit-animation: antZoomBadgeIn 0.3s cubic-bezier(0.12, 0.4, 0.29, 1.46);
    animation: antZoomBadgeIn 0.3s cubic-bezier(0.12, 0.4, 0.29, 1.46);
    -webkit-animation-fill-mode: both;
    animation-fill-mode: both;
  }
  .ant-badge-zoom-leave {
    -webkit-animation: antZoomBadgeOut 0.3s cubic-bezier(0.71, -0.46, 0.88, 0.6);
    animation: antZoomBadgeOut 0.3s cubic-bezier(0.71, -0.46, 0.88, 0.6);
    -webkit-animation-fill-mode: both;
    animation-fill-mode: both;
  }
  .ant-badge-not-a-wrapper:not(.ant-badge-status) {
    vertical-align: middle;
  }
  .ant-badge-not-a-wrapper .ant-scroll-number {
    position: relative;
    top: auto;
    display: block;
  }
  .ant-badge-not-a-wrapper .ant-badge-count {
    transform: none;
  }
  @-webkit-keyframes antStatusProcessing {
    0% {
      transform: scale(0.8);
      opacity: 0.5;
    }
    100% {
      transform: scale(2.4);
      opacity: 0;
    }
  }
  @keyframes antStatusProcessing {
    0% {
      transform: scale(0.8);
      opacity: 0.5;
    }
    100% {
      transform: scale(2.4);
      opacity: 0;
    }
  }
  .ant-scroll-number {
    overflow: hidden;
  }
  .ant-scroll-number-only {
    display: inline-block;
    height: 20px;
    transition: all 0.3s cubic-bezier(0.645, 0.045, 0.355, 1);
  }
  .ant-scroll-number-only > p.ant-scroll-number-only-unit {
    height: 20px;
    margin: 0;
  }
  .ant-scroll-number-symbol {
    vertical-align: top;
  }
  @-webkit-keyframes antZoomBadgeIn {
    0% {
      transform: scale(0) translate(50%, -50%);
      opacity: 0;
    }
    100% {
      transform: scale(1) translate(50%, -50%);
    }
  }
  @keyframes antZoomBadgeIn {
    0% {
      transform: scale(0) translate(50%, -50%);
      opacity: 0;
    }
    100% {
      transform: scale(1) translate(50%, -50%);
    }
  }
  @-webkit-keyframes antZoomBadgeOut {
    0% {
      transform: scale(1) translate(50%, -50%);
    }
    100% {
      transform: scale(0) translate(50%, -50%);
      opacity: 0;
    }
  }
  @keyframes antZoomBadgeOut {
    0% {
      transform: scale(1) translate(50%, -50%);
    }
    100% {
      transform: scale(0) translate(50%, -50%);
      opacity: 0;
    }
  }

</style>
