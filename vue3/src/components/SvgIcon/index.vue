<template>
  <img
    v-if="isExternal && isColorful"
    :src="styleExternalIcon"
    class="svg-external-img img-icon svg-icon"
    :style="styles"
  >
  <el-icon v-else-if="icon.indexOf('el-icon-') === 0 || icon.indexOf('ElIcon') === 0" :class="svgClass"
            :style="styles">
    <Component :is="icon"/>
  </el-icon>
  <i v-else-if="icon.indexOf('ri-') === 0" :class="svgClass" :style="styles"/>
  <div v-else-if="isExternal" :style="styleExternalIcon" class="svg-external-icon svg-icon"/>
  <svg v-else :class="svgClass" :style="styles" aria-hidden="true">
    <use :xlink:href="iconName"/>
  </svg>
</template>

<script setup>
  import {computed} from 'vue';

  const props = defineProps({
    icon: {
      type: String,
      default: '',
      required: true
    },
    className: {
      type: String
    },
    flip: {
      type: String,
      validator(value) {
        return ['', 'horizontal', 'vertical', 'both'].includes(value)
      },
      default: ''
    },
    size: [Number, String],
    color: String,
    iconType: {
      type: String,
      default: ''
    },
    rotate: {
      type: Number,
      validator(value) {
        return value >= 0 && value <= 360
      },
      default: 0
    }
  })

  const iconName = computed(() => {
    return `#${props.icon}`
  })

  const isColorful = computed(() => {
    return props.iconType === 'colorful'
  })

  const isExternal = computed(() => {
    return /^(https?:|mailto:|tel:)/.test(props.icon)
  })

  const svgClass = computed(() => {
    if (props.className) {
      return 'svg-icon ' + props.className
    } else {
      return 'svg-icon'
    }
  })


  const styles = computed(() => {
      const style = {}

      if (props.size) {
        style['font-size'] = `${props.size}px`
        // style['height'] = `${props.size}px`
      }

      if (props.color) {
        style.color = props.color
      }


      let trans = []
      if (props.flip != '') {
        switch (props.flip) {
          case 'horizontal':
            trans.push('rotateY(180deg)')
            break
          case 'vertical':
            trans.push('rotateX(180deg)')
            break
          case 'both':
            trans.push('rotateX(180deg)')
            trans.push('rotateY(180deg)')
            break
        }
      }
      if (props.rotate != 0) {
        trans.push(`rotate(${props.rotate}deg)`)
      }
      if (trans.length) {
        style.transform = trans.join(' ')
      }


      return style
    }
  )


  const transformStyle = computed(() => {
    let style = []
    if (props.flip != '') {
      switch (props.flip) {
        case 'horizontal':
          style.push('rotateY(180deg)')
          break
        case 'vertical':
          style.push('rotateX(180deg)')
          break
        case 'both':
          style.push('rotateX(180deg)')
          style.push('rotateY(180deg)')
          break
      }
    }
    if (props.rotate != 0) {
      style.push(`rotate(${props.rotate}deg)`)
    }
    return `transform: ${style.join(' ')};`
  })


  const styleExternalIcon = computed(() => {
    let styles = {
      mask: `url(${props.icon}) no-repeat 50% 50%`,
      '-webkit-mask': `url(${props.icon}) no-repeat 50% 50%`
    }
    if (isColorful.value) {
      styles = props.icon
    } else {
      if (props.size) {
        styles['width'] = `${props.size}px`
        styles['height'] = `${props.size}px`
      }

      if (props.color) {
        styles.color = props.color
      }
    }
    return styles
  })

</script>

<style scoped lang="scss">
  .svg-icon {
    width: 1em;
    height: 1em;
    vertical-align: -0.15em;
    fill: currentColor;
    overflow: hidden;

    &:hover {
      opacity: 0.8;
    }
  }

  .svg-external-icon {
    background-color: currentColor;
    mask-size: cover !important;
    display: inline-block;
  }

  .svg-external-img {
    display: inline-block;
  }

  .svg-icon {
    width: 1em;
    height: 1em;
    vertical-align: -0.15em;
    fill: currentColor;
    overflow: hidden;
  }
</style>
