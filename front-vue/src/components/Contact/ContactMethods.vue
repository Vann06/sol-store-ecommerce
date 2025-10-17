<template>
  <div class="quick-contact-card">
    <div class="card-header">
      <h3>Contacto Rápido</h3>
      <p>Elige tu método preferido</p>
    </div>
    
    <div class="contact-methods">
      <!-- WhatsApp -->
      <button 
        @click="openWhatsApp" 
        class="contact-method whatsapp"
      >
        <div class="method-icon">
          <i class="fab fa-whatsapp"></i>
        </div>
        <div class="method-info">
          <h4>WhatsApp</h4>
          <p>Chat inmediato</p>
          <span class="method-status">En línea</span>
        </div>
      </button>

      <!-- Instagram -->
      <a 
        :href="instagramLink" 
        target="_blank" 
        class="contact-method instagram"
      >
        <div class="method-icon">
          <i class="fab fa-instagram"></i>
        </div>
        <div class="method-info">
          <h4>Instagram</h4>
          <p>@sol.store.gt</p>
          <span class="method-status">Activo</span>
        </div>
      </a>

      <!-- Facebook -->
      <a 
        :href="facebookLink" 
        target="_blank" 
        class="contact-method facebook"
      >
        <div class="method-icon">
          <i class="fab fa-facebook-f"></i>
        </div>
        <div class="method-info">
          <h4>Facebook</h4>
          <p>Próximamente</p>
          <span class="method-status">Conectado</span>
        </div>
      </a>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

// Links de contacto
const instagramLink = ref('https://www.instagram.com/sol.store.gt/')
const facebookLink = ref('#') // Link placeholder para Facebook

// Función para abrir WhatsApp usando la misma lógica del WhatsappButton
const openWhatsApp = () => {
  // Usar las mismas variables de entorno que el WhatsappButton
  const phone = import.meta.env.VITE_WHATSAPP_PHONE || '50231271096'
  const defaultMessage = import.meta.env.VITE_WHATSAPP_MESSAGE || 'Hola SolStore Guatemala, me interesa cotizar un proyecto de impresión 3D'
  
  const url = `https://wa.me/${phone}?text=${encodeURIComponent(defaultMessage)}`
  window.open(url, '_blank')
}
</script>

<style scoped>
/* Quick Contact Card */
.quick-contact-card {
  background: linear-gradient(var(--surface), var(--surface)) padding-box,
              linear-gradient(135deg, var(--accent) 0%, var(--brand) 100%) border-box;
  border: 2px solid transparent;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 8px 24px rgba(113, 18, 18, 0.12);
}

.card-header {
  background: linear-gradient(135deg, var(--brand) 0%, var(--brand-2) 100%);
  color: #fff;
  padding: 20px;
  text-align: center;
}

.card-header h3 {
  margin: 0 0 5px 0;
  font-size: 1.3rem;
}

.card-header p {
  margin: 0;
  opacity: 0.9;
  font-size: 0.9rem;
}

.contact-methods { padding: 16px; display: grid; gap: 12px; }

.contact-method {
  display: flex;
  align-items: center;
  padding: 14px 12px;
  border: 1.5px solid rgba(113, 18, 18, 0.25);
  border-radius: 12px;
  text-decoration: none;
  color: var(--ink-1);
  background: #fff;
  transition: all 0.2s ease;
  cursor: pointer;
  width: 100%;
}

.contact-method:last-child {
  margin-bottom: 0;
}

.contact-method:hover { transform: translateX(3px); box-shadow: 0 4px 14px rgba(113,18,18,0.12); }

.contact-method.whatsapp:hover {
  border-color: #25D366;
  background: rgba(37, 211, 102, 0.05);
}

.contact-method.instagram:hover {
  border-color: #E4405F;
  background: rgba(228, 64, 95, 0.05);
}

.contact-method.facebook:hover {
  border-color: #1877F2;
  background: rgba(24, 119, 242, 0.05);
}

.method-icon {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  margin-right: 12px;
  flex-shrink: 0;
}

.contact-method.whatsapp .method-icon {
  background: #25D366;
  color: white;
}

.contact-method.instagram .method-icon {
  background: linear-gradient(45deg, #405DE6, #E4405F);
  color: white;
}

.contact-method.facebook .method-icon {
  background: #1877F2;
  color: white;
}

.method-info {
  flex: 1;
}

.method-info h4 { margin: 0 0 3px 0; color: var(--brand-strong); font-size: 1rem; }

.method-info p {
  margin: 0 0 2px 0;
  color: #666;
  font-size: 0.85rem;
}

.method-status { font-size: 0.75rem; padding: 2px 6px; border-radius: 8px; background: #4CAF50; color: #fff; }

/* Responsive */
@media (max-width: 968px) {
  .quick-contact-card { max-width: 520px; margin: 0 auto; }
}

@media (max-width: 480px) {
  .contact-method { padding: 14px; gap: 10px; }
  
  .method-icon { width: 42px; height: 42px; font-size: 1.15rem; margin-right: 10px; }
  
  .method-info h4 { font-size: 1rem; }
  
  .method-info p { font-size: 0.85rem; }
}
</style>