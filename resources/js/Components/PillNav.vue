<script setup>
import { ref, onMounted, onUnmounted, nextTick, watch } from 'vue'
import { Link } from '@inertiajs/vue3'
import { gsap } from 'gsap'
import './PillNav.css'

const props = defineProps({
  logo: { type: String, default: '' },
  logoAlt: { type: String, default: 'Logo' },
  items: { type: Array, default: () => [] },
  activeHref: { type: String, default: undefined },
  className: { type: String, default: '' },
  ease: { type: String, default: 'power3.easeOut' },
  baseColor: { type: String, default: '#fff' },
  pillColor: { type: String, default: '#120F17' },
  hoveredPillTextColor: { type: String, default: '#120F17' },
  pillTextColor: { type: String, default: undefined },
  onMobileMenuClick: { type: Function, default: undefined },
  initialLoadAnimation: { type: Boolean, default: false }
})

const resolvedPillTextColor = props.pillTextColor ?? props.baseColor

const isMobileMenuOpen = ref(false)

const circleRefs = ref([])
const tlRefs = ref([])
const activeTweenRefs = ref([])
const logoImgRef = ref(null)
const hamburgerRef = ref(null)
const mobileMenuRef = ref(null)
const navItemsRef = ref(null)
const logoRef = ref(null)

function setCircleRef(el, index) {
  if (el) circleRefs.value[index] = el
}

function layout() {
  circleRefs.value.forEach((circle, i) => {
    if (!circle?.parentElement) return

    const pill = circle.parentElement
    const rect = pill.getBoundingClientRect()
    const w = rect.width
    const h = rect.height
    const R = ((w * w) / 4 + h * h) / (2 * h)
    const D = Math.ceil(2 * R) + 2
    const delta = Math.ceil(R - Math.sqrt(Math.max(0, R * R - (w * w) / 4))) + 1
    const originY = D - delta

    circle.style.width = `${D}px`
    circle.style.height = `${D}px`
    circle.style.bottom = `-${delta}px`

    gsap.set(circle, {
      xPercent: -50,
      scale: 0,
      transformOrigin: `50% ${originY}px`
    })

    const label = pill.querySelector('.pill-label')
    const white = pill.querySelector('.pill-label-hover')

    if (label) gsap.set(label, { y: 0 })
    if (white) gsap.set(white, { y: h + 12, opacity: 0 })

    if (tlRefs.value[i]) tlRefs.value[i].kill()

    const tl = gsap.timeline({ paused: true })
    tl.to(circle, { scale: 1.2, xPercent: -50, duration: 2, ease: props.ease, overwrite: 'auto' }, 0)

    if (label) {
      tl.to(label, { y: -(h + 8), duration: 2, ease: props.ease, overwrite: 'auto' }, 0)
    }

    if (white) {
      gsap.set(white, { y: Math.ceil(h + 100), opacity: 0 })
      tl.to(white, { y: 0, opacity: 1, duration: 2, ease: props.ease, overwrite: 'auto' }, 0)
    }

    tlRefs.value[i] = tl
  })
}

function handleClick(i) {
  const circle = circleRefs.value[i]
  if (circle) gsap.set(circle, { scale: 0, clearProps: 'transform' })
  if (tlRefs.value[i]) tlRefs.value[i].progress(0)
}

function handleEnter(i) {
  const tl = tlRefs.value[i]
  if (!tl) return
  if (activeTweenRefs.value[i]) activeTweenRefs.value[i].kill()
  activeTweenRefs.value[i] = tl.tweenTo(tl.duration(), {
    duration: 0.3,
    ease: props.ease,
    overwrite: 'auto'
  })
}

function handleLeave(i) {
  const tl = tlRefs.value[i]
  if (!tl) return
  activeTweenRefs.value[i]?.kill()
  const circle = circleRefs.value[i]
  if (circle) gsap.set(circle, { scale: 0 })
  activeTweenRefs.value[i] = tl.tweenTo(0, {
    duration: 0.15,
    ease: props.ease,
    overwrite: 'auto'
  })
}

function handleLogoEnter() {
  const img = logoImgRef.value
  if (!img) return
  gsap.set(img, { rotate: 0 })
  gsap.to(img, {
    rotate: 360,
    duration: 0.2,
    ease: props.ease,
    overwrite: 'auto'
  })
}

function toggleMobileMenu() {
  const newState = !isMobileMenuOpen.value
  isMobileMenuOpen.value = newState

  const hamburger = hamburgerRef.value
  const menu = mobileMenuRef.value

  if (hamburger) {
    const lines = hamburger.querySelectorAll('.hamburger-line')
    if (newState) {
      gsap.to(lines[0], { rotation: 45, y: 3, duration: 0.3, ease: props.ease })
      gsap.to(lines[1], { rotation: -45, y: -3, duration: 0.3, ease: props.ease })
    } else {
      gsap.to(lines[0], { rotation: 0, y: 0, duration: 0.3, ease: props.ease })
      gsap.to(lines[1], { rotation: 0, y: 0, duration: 0.3, ease: props.ease })
    }
  }

  if (menu) {
    if (newState) {
      gsap.set(menu, { visibility: 'visible' })
      gsap.fromTo(
        menu,
        { opacity: 0, y: 10, scaleY: 1 },
        {
          opacity: 1,
          y: 0,
          scaleY: 1,
          duration: 0.3,
          ease: props.ease,
          transformOrigin: 'top center'
        }
      )
    } else {
      gsap.to(menu, {
        opacity: 0,
        y: 10,
        scaleY: 1,
        duration: 0.2,
        ease: props.ease,
        transformOrigin: 'top center',
        onComplete: () => {
          gsap.set(menu, { visibility: 'hidden' })
        }
      })
    }
  }

  if (props.onMobileMenuClick) props.onMobileMenuClick()
}

function isExternalLink(href) {
  return href.startsWith('http://') ||
    href.startsWith('https://') ||
    href.startsWith('//') ||
    href.startsWith('mailto:') ||
    href.startsWith('tel:') ||
    href.startsWith('#')
}

function isRouterLink(href) {
  return href && !isExternalLink(href)
}

function resetPills() {
  circleRefs.value.forEach(circle => {
    if (circle) gsap.set(circle, { scale: 0 })
  })
  tlRefs.value.forEach(tl => tl?.kill())
  activeTweenRefs.value.forEach(t => t?.kill())
  tlRefs.value = []
  activeTweenRefs.value = []
  nextTick(() => layout())
}

onMounted(() => {
  nextTick(() => {
    layout()

    if (mobileMenuRef.value) {
      gsap.set(mobileMenuRef.value, { visibility: 'hidden', opacity: 0, scaleY: 1 })
    }

    if (props.initialLoadAnimation) {
      if (logoRef.value) {
        gsap.set(logoRef.value, { scale: 0 })
        gsap.to(logoRef.value, { scale: 1, duration: 0.6, ease: props.ease })
      }
      if (navItemsRef.value) {
        gsap.set(navItemsRef.value, { width: 0, overflow: 'hidden' })
        gsap.to(navItemsRef.value, { width: 'auto', duration: 0.6, ease: props.ease })
      }
    }
  })

  window.addEventListener('resize', layout)
  if (document.fonts?.ready) {
    document.fonts.ready.then(layout).catch(() => {})
  }
})

watch(() => props.activeHref, () => {
  resetPills()
})

onUnmounted(() => {
  window.removeEventListener('resize', layout)
  tlRefs.value.forEach(tl => tl?.kill())
  activeTweenRefs.value.forEach(t => t?.kill())
})
</script>

<template>
  <div class="pill-nav-container">
    <nav :class="['pill-nav', className]" aria-label="Primary" :style="{
      '--base': baseColor,
      '--pill-bg': pillColor,
      '--hover-text': hoveredPillTextColor,
      '--pill-text': resolvedPillTextColor
    }">
      <component
        v-if="logo"
        :is="isRouterLink(items?.[0]?.href) ? Link : 'a'"
        class="pill-logo"
        v-bind="isRouterLink(items?.[0]?.href) ? { href: items?.[0]?.href } : { href: items?.[0]?.href || '#' }"
        aria-label="Home"
        @mouseenter="handleLogoEnter"
        ref="logoRef"
      >
        <img :src="logo" :alt="logoAlt" ref="logoImgRef" />
      </component>

      <div class="pill-nav-items desktop-only" ref="navItemsRef">
        <ul class="pill-list" role="menubar">
          <li v-for="(item, i) in items" :key="item.href || `item-${i}`" role="none">
            <component
              :is="isRouterLink(item.href) ? Link : 'a'"
              role="menuitem"
              :href="item.href"
              :class="['pill', { 'is-active': activeHref === item.href }]"
              :aria-label="item.ariaLabel || item.label"
              @mouseenter="handleEnter(i)"
              @mouseleave="handleLeave(i)"
              @click="handleClick(i)"
            >
              <span class="hover-circle" aria-hidden="true" :ref="el => setCircleRef(el, i)" />
              <span class="label-stack">
                <span class="pill-label">{{ item.label }}</span>
                <span class="pill-label-hover" aria-hidden="true">{{ item.label }}</span>
              </span>
            </component>
          </li>
        </ul>
      </div>

      <button
        class="mobile-menu-button mobile-only"
        @click="toggleMobileMenu"
        aria-label="Toggle menu"
        ref="hamburgerRef"
      >
        <span class="hamburger-line" />
        <span class="hamburger-line" />
      </button>
    </nav>

    <div class="mobile-menu-popover mobile-only" ref="mobileMenuRef" :style="{
      '--base': baseColor,
      '--pill-bg': pillColor,
      '--hover-text': hoveredPillTextColor,
      '--pill-text': resolvedPillTextColor
    }">
      <ul class="mobile-menu-list">
        <li v-for="(item, i) in items" :key="item.href || `mobile-item-${i}`">
          <component
            :is="isRouterLink(item.href) ? Link : 'a'"
            :href="item.href"
            :class="['mobile-menu-link', { 'is-active': activeHref === item.href }]"
            @click="isMobileMenuOpen = false"
          >
            {{ item.label }}
          </component>
        </li>
      </ul>
    </div>
  </div>
</template>
