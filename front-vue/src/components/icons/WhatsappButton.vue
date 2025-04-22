    <template>
    <div class="whatsapp-button">
       <button @click="redirectToWhatsApp" class="whatsapp-button">
        <a
          :href="whatsappLink"
          target="_blank"
          rel="noopener noreferrer"
          class="whatsapp-button"
        >
          <img
            src="@/assets/whatsapp.svg"
            alt="WhatsApp"
            class="whatsapp-icon"
          />
        </a>
        </button>
      </div>
</template>


<script setup>
const redirectToWhatsApp = () => {
  const phone = import.meta.env.VITE_WHATSAPP_PHONE
  const text  = import.meta.env.VITE_WHATSAPP_MESSAGE

  const url = `https://wa.me/${phone}?text=${encodeURIComponent(text)}`
  window.open(url, '_blank')
}
</script>


<style scoped>
.home-container {
  position: relative;
}
.whatsapp-button {
  width: 3.5rem;
  height: 3.5rem;
  background-color: #25D366;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  position: fixed;
  bottom: 1.5rem;
  right: 1.5rem;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
  z-index: 1000;
  cursor: pointer;
  overflow: hidden;
  padding: 0;
  border: none;
}

.whatsapp-icon {
  width: 3rem;
  height: 3rem;
  transition: transform 0.2s ease;
}

.whatsapp-button:hover .whatsapp-icon {
  transform: scale(1.5); 

}
</style>