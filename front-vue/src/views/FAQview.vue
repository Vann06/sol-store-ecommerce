<template>
  <div class="faq-container">
    <div class="faq-header">
      <h1>¿Necesitas ayuda?</h1>
      <p class="subtitle">Estamos aquí para ayudarte...</p>
    </div>

    <div class="faq-content">
      <div class="faq-sidebar">
        <div class="help-section">
          <h3>TEMAS DE AYUDA</h3>
          <ul>
            <li
              v-for="(item, index) in categories"
              :key="index"
              :class="{ active: selectedCategoryName === item.category }"
              @click="selectCategory(item.category)"
            >
              {{ item.category }}
            </li>
          </ul>
        </div>

        <div class="contact-promo">
          <h3>¿AÚN NECESITAS AYUDA?</h3>
          <p>Contáctanos directamente:</p>
          <button class="contact-button">CONTÁCTANOS AQUÍ</button>
        </div>
      </div>

      <div class="faq-main">
        <!-- Mostrar loading mientras se cargan los datos -->
        <div v-if="loading" class="loading-container">
          <div class="loading-spinner"></div>
          <p>Cargando preguntas frecuentes...</p>
        </div>

        <!-- Mostrar error si falla la carga -->
        <div v-else-if="error" class="error-container">
          <p>Error al cargar las preguntas: {{ error }}</p>
          <button @click="fetchFAQs" class="retry-button">Reintentar</button>
        </div>

        <!-- Mostrar contenido normal -->
        <div v-else-if="selectedCategory" class="faq-section">
          <h2>{{ selectedCategory.category.toUpperCase() }}</h2>

          <div
            v-for="faq in selectedCategory.faqs"
            :key="faq.id"
            class="faq-item"
          >
            <div class="faq-question" @click="toggleAnswer(faq.id)">
              <span class="question-text">{{ faq.question }}</span>
              <span class="icon">{{ openFaqId === faq.id ? '−' : '+' }}</span>
            </div>

            <transition name="slide">
              <div
                v-show="openFaqId === faq.id"
                class="faq-answer"
              >
                <p>{{ faq.answer }}</p>
              </div>
            </transition>
          </div>
        </div>
      </div>
    </div>

    <div class="policies-section">
      <h3>POLÍTICAS</h3>
      <div class="policies-grid">
        <div class="policy-item">Política de Devoluciones Online</div>
        <div class="policy-item">Política de Devoluciones en Tienda</div>
        <div class="policy-item">Política de Envíos</div>
        <div class="policy-item">Política de Privacidad</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const categories = ref([])
const selectedCategoryName = ref(null)
const openFaqId = ref(null)
const loading = ref(true)
const error = ref(null)

const fetchFAQs = async () => {
  loading.value = true
  error.value = null
  try {
    const { data } = await axios.get('http://localhost:8000/api/faqs') // Cambia por tu endpoint real
    categories.value = data
    if (categories.value.length > 0) {
      selectedCategoryName.value = categories.value[0].category
    }
  } catch (err) {
    error.value = err.message || 'Error al cargar las preguntas frecuentes'
    console.error('Error fetching FAQs:', err)
  } finally {
    loading.value = false
  }
}

const selectedCategory = computed(() => {
  return categories.value.find(cat => cat.category === selectedCategoryName.value)
})

const selectCategory = (categoryName) => {
  selectedCategoryName.value = categoryName
  openFaqId.value = null
}

const toggleAnswer = (id) => {
  openFaqId.value = openFaqId.value === id ? null : id
}

onMounted(fetchFAQs)
</script>

<style scoped>
.faq-container {
  font-family: 'Montserrat', sans-serif;
  max-width: 1200px;
  margin: 0 auto;
  padding: 40px 20px;
  background-color: #FFFFFF;
}

.faq-header {
  text-align: center;
  margin-bottom: 50px;
  padding-bottom: 20px;
  border-bottom: 2px solid #780116;
}

.faq-header h1 {
  font-size: 2.5rem;
  color: #780116;
  margin-bottom: 15px;
  font-weight: 700;
}

.subtitle {
  font-size: 1.2rem;
  color: #6c757d;
}

.faq-content {
  display: flex;
  gap: 30px;
  background-color: #FFFFFF;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 4px 20px rgba(120, 1, 22, 0.1);
}

.faq-sidebar {
  width: 280px;
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  gap: 25px;
}

.help-section {
  background-color: #FFF9F0;
  padding: 25px;
  border-radius: 8px;
  border: 1px solid #F7B538;
}

.help-section h3 {
  color: #780116;
  font-size: 1.1rem;
  text-transform: uppercase;
  margin-bottom: 20px;
  letter-spacing: 1px;
}

.help-section ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.help-section li {
  padding: 12px 0;
  cursor: pointer;
  border-bottom: 1px solid #F7B538;
  transition: all 0.3s;
  font-size: 0.95rem;
}

.help-section li:hover {
  color: #780116;
  background-color: #FEF0D7;
}

.help-section li.active {
  color: #FFFFFF;
  background-color: #780116;
  font-weight: 600;
  padding-left: 15px;
}

.contact-promo {
  background-color: #780116;
  color: white;
  padding: 25px;
  border-radius: 8px;
  text-align: center;
  border: 1px solid #5A0113;
}

.contact-promo h3 {
  font-size: 1.1rem;
  text-transform: uppercase;
  margin-bottom: 15px;
  letter-spacing: 1px;
}

.contact-promo p {
  margin-bottom: 20px;
  font-size: 0.95rem;
}

.contact-button {
  background-color: #F7B538;
  color: #780116;
  border: none;
  padding: 12px 25px;
  font-weight: 600;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s;
  font-size: 0.95rem;
  box-shadow: 0 2px 10px rgba(247, 181, 56, 0.3);
}

.contact-button:hover {
  background-color: #F5A90E;
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(247, 181, 56, 0.4);
}

.faq-main {
  flex-grow: 1;
}

.faq-section h2 {
  color: #780116;
  font-size: 1.8rem;
  margin-bottom: 25px;
  padding-bottom: 10px;
  border-bottom: 2px solid #F7B538;
}

.faq-item {
  margin-bottom: 20px;
  border: 1px solid #F7B538;
  border-radius: 8px;
  overflow: hidden;
  transition: all 0.3s;
}

.faq-item:hover {
  box-shadow: 0 4px 12px rgba(120, 1, 22, 0.1);
}

.faq-question {
  padding: 18px 25px;
  background-color: #FFF9F0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  cursor: pointer;
  transition: all 0.3s;
}

.faq-question:hover {
  background-color: #FEF0D7;
}

.faq-question .icon {
  font-weight: bold;
  font-size: 1.3rem;
  color: #780116;
  transition: all 0.3s;
  flex-shrink: 0;
  margin-left: 15px;
}

.faq-question .question-text {
  flex-grow: 1;
  margin-right: 15px;
}

.faq-answer {
  padding: 25px;
  background-color: white;
  color: #495057;
  line-height: 1.7;
  border-left: 4px solid #780116;
}

.slide-enter-active, .slide-leave-active {
  transition: all 0.4s ease;
  max-height: 500px;
  overflow: hidden;
}

.slide-enter, .slide-leave-to {
  max-height: 0;
  opacity: 0;
  padding: 0 25px;
}

.policies-section {
  margin-top: 60px;
  padding-top: 40px;
  border-top: 2px solid #780116;
}

.policies-section h3 {
  color: #780116;
  font-size: 1.2rem;
  text-transform: uppercase;
  margin-bottom: 25px;
  letter-spacing: 1px;
  text-align: center;
}

.policies-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 20px;
}

.policy-item {
  padding: 20px;
  background-color: #FFF9F0;
  border-radius: 8px;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s;
  border: 1px solid #F7B538;
  font-weight: 500;
}

.policy-item:hover {
  background-color: #780116;
  color: white;
  transform: translateY(-3px);
  box-shadow: 0 6px 15px rgba(120, 1, 22, 0.2);
}

.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px;
  text-align: center;
}

.loading-spinner {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #780116;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
  margin-bottom: 20px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-container {
  padding: 20px;
  background-color: #fff9f0;
  border: 1px solid #ff6b6b;
  border-radius: 8px;
  text-align: center;
  color: #780116;
}

.retry-button {
  background-color: #780116;
  color: white;
  border: none;
  padding: 10px 20px;
  margin-top: 15px;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.retry-button:hover {
  background-color: #5a0113;
}

@media (max-width: 992px) {
  .faq-content {
    flex-direction: column;
  }
  
  .faq-sidebar {
    width: 100%;
    margin-bottom: 30px;
  }
}

@media (max-width: 768px) {
  .faq-header h1 {
    font-size: 2.2rem;
  }
  
  .faq-section h2 {
    font-size: 1.6rem;
  }

  .faq-content {
    padding: 20px;
  }
}

@media (max-width: 576px) {
  .policies-grid {
    grid-template-columns: 1fr;
  }
  
  .faq-header h1 {
    font-size: 2rem;
  }
  
  .help-section, .contact-promo {
    padding: 20px;
  }
  
  .faq-question {
    padding: 15px 20px;
  }
}
</style>
