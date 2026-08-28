<script setup>
import { ref } from 'vue'
import './TiltedCard.css'

const props = defineProps({
  imageSrc: { type: String, default: '' },
  altText: { type: String, default: 'Tilted card image' },
  captionText: { type: String, default: '' },
  containerHeight: { type: String, default: '300px' },
  containerWidth: { type: String, default: '100%' },
  imageHeight: { type: String, default: '300px' },
  imageWidth: { type: String, default: '300px' },
  scaleOnHover: { type: Number, default: 1.1 },
  rotateAmplitude: { type: Number, default: 14 },
  showMobileWarning: { type: Boolean, default: true },
  showTooltip: { type: Boolean, default: true },
  displayOverlayContent: { type: Boolean, default: false }
})

const cardRef = ref(null)
const rotateX = ref(0)
const rotateY = ref(0)
const scale = ref(1)
const tooltipX = ref(0)
const tooltipY = ref(0)
const tooltipOpacity = ref(0)
const tooltipRotate = ref(0)
const lastY = ref(0)

function handleMouse(e) {
  const el = cardRef.value
  if (!el) return

  const rect = el.getBoundingClientRect()
  const offsetX = e.clientX - rect.left - rect.width / 2
  const offsetY = e.clientY - rect.top - rect.height / 2

  const rotationX = (offsetY / (rect.height / 2)) * -props.rotateAmplitude
  const rotationY = (offsetX / (rect.width / 2)) * props.rotateAmplitude

  rotateX.value = rotationX
  rotateY.value = rotationY

  tooltipX.value = e.clientX - rect.left
  tooltipY.value = e.clientY - rect.top

  const velocityY = offsetY - lastY.value
  tooltipRotate.value = -velocityY * 0.6
  lastY.value = offsetY
}

function handleMouseEnter() {
  scale.value = props.scaleOnHover
  tooltipOpacity.value = 1
}

function handleMouseLeave() {
  tooltipOpacity.value = 0
  scale.value = 1
  rotateX.value = 0
  rotateY.value = 0
  tooltipRotate.value = 0
}
</script>

<template>
  <figure
    ref="cardRef"
    class="tilted-card-figure"
    :style="{ height: containerHeight, width: containerWidth }"
    @mousemove="handleMouse"
    @mouseenter="handleMouseEnter"
    @mouseleave="handleMouseLeave"
  >
    <div v-if="showMobileWarning" class="tilted-card-mobile-alert">
      This effect is not optimized for mobile. Check on desktop.
    </div>

    <div
      class="tilted-card-inner"
      :style="{
        width: imageWidth,
        height: imageHeight,
        transform: `rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(${scale})`
      }"
    >
      <img
        v-if="imageSrc"
        :src="imageSrc"
        :alt="altText"
        class="tilted-card-img"
        :style="{ width: imageWidth, height: imageHeight }"
        loading="lazy"
        decoding="async"
      />
      <div v-else class="tilted-card-placeholder" :style="{ width: imageWidth, height: imageHeight }">
        <slot name="placeholder" />
      </div>

      <div v-if="displayOverlayContent" class="tilted-card-overlay">
        <slot name="overlay" />
      </div>
    </div>

    <figcaption
      v-if="showTooltip"
      class="tilted-card-caption"
      :style="{
        transform: `translate(${tooltipX}px, ${tooltipY}px) rotate(${tooltipRotate}deg)`,
        opacity: tooltipOpacity
      }"
    >
      {{ captionText }}
    </figcaption>
  </figure>
</template>
