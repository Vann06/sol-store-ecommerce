<template>
  <div class="intro-video">
    <div class="video-frame gradient-outline">
      <div class="video-aspect">
        <!-- Replace source with your own hosted video when available -->
        <video
          v-if="source"
          class="video"
          :src="source"
          v-bind="poster ? { poster } : {}"
          controls
          playsinline
        ></video>
        <div v-else class="video-placeholder">
          <div class="placeholder-inner">
            <i class="fas fa-play"></i>
            <p>Tu video de presentación aquí</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'

const props = defineProps({
  source: { type: String, default: '' },
  poster: { type: String, default: '' }
})
</script>

<style scoped>
.intro-video { display: grid; place-items: center; }
.video-frame { width: 100%; max-width: 1000px; border-radius: 16px; overflow: hidden; }
.video-aspect { position: relative; width: 100%; padding-top: 56.25%; /* 16:9 */ }
.video, .video-placeholder { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
.video-placeholder { display: grid; place-items: center; background: linear-gradient(135deg, rgba(240,192,64,0.25), rgba(122,0,25,0.08)); }
.placeholder-inner { text-align: center; color: var(--brand-strong); }
.placeholder-inner i { font-size: 48px; color: var(--accent-2); }
.placeholder-inner p { margin-top: 8px; font-weight: 700; }
</style>
