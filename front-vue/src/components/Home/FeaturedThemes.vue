<template>
  <div class="featured-themes" role="region" aria-label="Temáticas">
    <div class="carousel">
      <button class="nav prev" @click="scroll(-1)" aria-label="Ver anteriores">❮</button>
      <div
        class="track"
        ref="themeGrid"
        tabindex="0"
        @pointerdown="onPointerDown"
        @pointermove="onPointerMove"
        @pointerup="onPointerUp"
        @pointercancel="onPointerUp"
        @pointerleave="onPointerUp"
        @keydown.left.prevent="scroll(-1)"
        @keydown.right.prevent="scroll(1)"
      >
  <div v-for="(theme, index) in themes" :key="index" class="item" @click="onItemClick(theme)">
          <Card type="outlined" class="tile-card">
            <div class="media"><img :src="theme.imagen" :alt="theme.name" /></div>
            <div class="label">{{ theme.name }}</div>
          </Card>
        </div>
      </div>
      <button class="nav next" @click="scroll(1)" aria-label="Ver siguientes">❯</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import http from '@/http'
import { useRouter } from 'vue-router'
import Card from '@/components/ui/Card.vue'

const themes = ref([])
const themeGrid = ref(null)
const router = useRouter()

const goToCatalog = (theme) => {
  const q = theme?.name || ''
  router.push({ name: 'catalogo', query: q ? { q } : {} })
}
const scroll = (dir) => themeGrid.value?.scrollBy({ left: dir * 320, behavior: 'smooth' })

// Click handling that respects drag state
const onItemClick = (theme) => { if (!drag.moved) goToCatalog(theme) }

// Pointer drag/swipe support
const drag = { active: false, startX: 0, startLeft: 0, moved: false }
const onPointerDown = (e) => {
  if (!themeGrid.value) return
  drag.active = true
  drag.moved = false
  drag.startX = e.clientX
  drag.startLeft = themeGrid.value.scrollLeft
  themeGrid.value.classList.add('dragging')
}
const onPointerMove = (e) => {
  if (!drag.active || !themeGrid.value) return
  const dx = e.clientX - drag.startX
  if (Math.abs(dx) > 4) drag.moved = true
  themeGrid.value.scrollLeft = drag.startLeft - dx
}
const onPointerUp = () => {
  drag.active = false
  setTimeout(() => { drag.moved = false }, 0)
  if (themeGrid.value) themeGrid.value.classList.remove('dragging')
}

onMounted(async () => {
  const res = await http.get('/tematicas')
  themes.value = res.data.slice(0, 6)
})
</script>

<style scoped>
/* Horizontal carousel */
.carousel { position: relative; display: grid; grid-template-columns: auto 1fr auto; align-items: center; gap: 8px; }
.track { display: grid; grid-auto-flow: column; grid-auto-columns: minmax(220px, 1fr); gap: 16px; overflow-x: auto; scroll-snap-type: x mandatory; scroll-behavior: smooth; padding: 8px; touch-action: pan-x; cursor: grab; }
.track.dragging { cursor: grabbing; }
.item { scroll-snap-align: start; }
.nav { appearance: none; background: linear-gradient(135deg, var(--brand-strong), var(--brand)); color: #fff; border: 0; width: 36px; height: 36px; border-radius: 999px; cursor: pointer; box-shadow: 0 8px 16px rgba(122,0,25,0.25); }
.nav:disabled { opacity: .4; cursor: not-allowed; }

.tile-card { height: 100%; display: grid; grid-template-rows: 1fr auto; }
.media { display: grid; place-items: center; overflow: hidden; border-radius: 12px; background: #fff; }
.media img { width: 100%; height: 107px; object-fit: cover; object-position: center; }
.label { text-align: center; font-weight: 800; padding: 10px; color: var(--ink-1); }

@media (max-width: 640px) {
  .nav { width: 44px; height: 44px; }
  .track { grid-auto-columns: 70%; }
}
</style>
