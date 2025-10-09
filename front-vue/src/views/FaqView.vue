<template>
  <div class="faq-container">
    <div class="faq-header">
      <h1>¿Necesitas ayuda?</h1>
      <p>Estamos aquí para ayudarte...</p>
    </div>

    <div class="faq-content">
      <aside class="faq-categories">
        <h2>TEMAS DE AYUDA</h2>
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
      </aside>

      <section class="faq-main">
        <!-- Barra de búsqueda en el lado derecho x2 -->
        <div class="search-container right-search">
          <input 
            type="text" 
            v-model="searchQuery" 
            placeholder="Buscar en todas las preguntas..." 
            @input="handleSearch"
            class="search-input"
          >
          <span class="search-icon">🔍</span>
        </div>

        <!-- Mostrar resultados de búsqueda o preguntas de la categoría seleccionada -->
        <div v-if="searchQuery && filteredFAQs.length > 0">
          <h2>Resultados de búsqueda</h2>
          <div
            v-for="result in filteredFAQs"
            :key="result.id"
            class="faq-item"
          >
            <div class="faq-question">
              <strong>{{ result.question }}</strong>
              <span class="faq-category">({{ result.category }})</span>
            </div>
          </div>
        </div>
        <div v-else-if="selectedCategory">
          <h2>{{ selectedCategory.category.toUpperCase() }}</h2>
          <div
            v-for="faq in selectedCategory.faqs"
            :key="faq.id"
            class="faq-item"
          >
            <div class="faq-question" @click="toggleAnswer(faq.id)">
              {{ faq.question }}
              <span>{{ openFaqId === faq.id ? '−' : '+' }}</span>
            </div>

            <transition name="accordion">
              <div
                v-show="openFaqId === faq.id"
                class="faq-answer"
              >
                {{ faq.answer }}
              </div>
            </transition>
          </div>
        </div>
        <div v-else>
          <p>No se encontraron resultados para "{{ searchQuery }}"</p>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import http from '@/http'

const categories = ref([])
const selectedCategoryName = ref(null)
const openFaqId = ref(null)
const searchQuery = ref('') // Estado para la barra de búsqueda

const fetchFAQs = async () => {
  try {
    const { data } = await http.get('/faqs')
    categories.value = data
    if (categories.value.length > 0) {
      selectedCategoryName.value = categories.value[0].category
    }
  } catch (error) {
    console.error('Error al cargar las FAQs:', error)
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

// Computed para filtrar las preguntas según la búsqueda
const filteredFAQs = computed(() => {
  if (!searchQuery.value.trim()) return []
  const query = searchQuery.value.toLowerCase()
  return categories.value
    .flatMap(category => category.faqs.map(faq => ({ ...faq, category: category.category })))
    .filter(faq => faq.question.toLowerCase().includes(query))
})

const handleSearch = () => {
  // Lógica adicional si es necesario
}

onMounted(fetchFAQs)
</script>

<style scoped>
.faq-container {
  font-family: sans-serif;
  padding: 10rem 2rem 2rem 2rem; 
}

.faq-header {
  text-align: center;
  color: #7a0019;
}

.faq-header h1 {
  font-size: 2rem;
  font-weight: bold;
}

.search-container {
  position: relative;
  margin-bottom: 25px;
}

.right-search {
  margin-left: auto;
  width: 100%;
  max-width: 400px;
}

.search-input {
  width: 100%;
  padding: 12px 20px 12px 45px;
  border: 2px solid #F7B538;
  border-radius: 30px;
  font-size: 0.95rem;
  outline: none;
  transition: all 0.3s;
  box-shadow: 0 2px 10px rgba(120, 1, 22, 0.1);
}

.search-input:focus {
  border-color: #780116;
  box-shadow: 0 4px 15px rgba(120, 1, 22, 0.2);
}

.search-icon {
  position: absolute;
  left: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: #780116;
  font-size: 1.1rem;
}

.faq-content {
  display: flex;
  gap: 2rem;
  margin-top: 2rem;
}

.faq-categories {
  width: 25%;
  border: 2px solid #f0c040;
  border-radius: 10px;
  padding: 1rem;
  background-color: #fdf9f2;
}

.faq-categories h2 {
  font-size: 1rem;
  color: #7a0019;
  margin-bottom: 1rem;
}

.faq-categories ul {
  list-style: none;
  padding: 0;
}

.faq-categories li {
  padding: 0.5rem 1rem;
  margin-bottom: 0.5rem;
  cursor: pointer;
  border-left: 3px solid transparent;
}

.faq-categories li.active {
  background-color: #7a0019;
  color: white;
  border-left: 3px solid #f0c040;
}

.faq-main {
  flex: 1;
}

.faq-main h2 {
  font-size: 1.3rem;
  color: #7a0019;
  border-bottom: 2px solid #f0c040;
  padding-bottom: 0.5rem;
  margin-bottom: 1rem;
}

.faq-item {
  background-color: #fdf9f2;
  border: 1px solid #f0c040;
  border-radius: 8px;
  margin-bottom: 1rem;
  overflow: hidden;
}

.faq-question {
  padding: 1rem;
  font-weight: bold;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.faq-answer {
  padding: 1rem;
  background-color: white;
  border-top: 1px solid #f0c040;
}

.faq-search-results {
  margin-top: 2rem;
}

.faq-search-results h2 {
  font-size: 1.3rem;
  color: #7a0019;
  margin-bottom: 1rem;
}

.faq-category {
  font-size: 0.9rem;
  color: #7a0019;
  margin-left: 0.5rem;
}
</style>
