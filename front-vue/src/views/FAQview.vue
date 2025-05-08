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
            :class="{ active: selectedCategoryId === item.id }"
            @click="selectCategory(item.id)"
          >
            {{ item.category }}
          </li>
        </ul>
      </aside>

      <section class="faq-questions">
        <h2>{{ selectedCategory?.category?.toUpperCase() }}</h2>

        <div
          v-for="faq in selectedCategory?.faqs"
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
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const categories = ref([])
const selectedCategoryId = ref(null)
const openFaqId = ref(null)

const fetchFAQs = async () => {
  try {
    const { data } = await axios.get('/api/faqs') // Cambia por tu endpoint real
    categories.value = data.filter(c => c.id !== undefined) // Solo con ID válido
    if (categories.value.length > 0) {
      selectedCategoryId.value = categories.value[0].id
    }
  } catch (error) {
    console.error('Error al cargar las FAQs:', error)
  }
}

const selectedCategory = computed(() => {
  return categories.value.find(cat => cat.id === selectedCategoryId.value)
})

const selectCategory = (id) => {
  selectedCategoryId.value = id
  openFaqId.value = null
}

const toggleAnswer = (id) => {
  openFaqId.value = openFaqId.value === id ? null : id
}

onMounted(fetchFAQs)
</script>

<style scoped>
.faq-container {
  font-family: sans-serif;
  padding: 2rem;
}

.faq-header {
  text-align: center;
  color: #7a0019;
}

.faq-header h1 {
  font-size: 2rem;
  font-weight: bold;
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

.faq-questions {
  flex: 1;
}

.faq-questions h2 {
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

.accordion-enter-active,
.accordion-leave-active {
  transition: all 0.3s ease;
}
.accordion-enter-from,
.accordion-leave-to {
  opacity: 0;
  max-height: 0;
}
</style>
