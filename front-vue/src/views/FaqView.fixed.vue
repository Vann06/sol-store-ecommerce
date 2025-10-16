<template>
  <main class="faq-page under-fixed-header bg-page-soft">
    <PageHero title="¿Necesitas ayuda?" subtitle="Encuentra respuestas a preguntas frecuentes" variant="red" />

    <div class="faq-shell gradient-outline">
      <aside class="faq-categories">
        <h2 class="aside-title">Temas de ayuda</h2>
        <ul class="aside-list">
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
        <div class="search-container right-search">
          <i class="fa-solid fa-magnifying-glass search-icon" aria-hidden="true"></i>
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Buscar en todas las preguntas..."
            @input="handleSearch"
            class="search-input"
            aria-label="Buscar preguntas frecuentes"
          />
        </div>

        <div v-if="searchQuery && filteredFAQs.length > 0" class="faq-search-results">
          <h2 class="section-title">Resultados de búsqueda</h2>
          <div v-for="result in filteredFAQs" :key="result.id" class="faq-item">
            <div class="faq-question">
              <strong>{{ result.question }}</strong>
              <span class="faq-category">({{ result.category }})</span>
            </div>
          </div>
        </div>

        <div v-else-if="selectedCategory">
          <h2 class="section-title">{{ selectedCategory.category }}</h2>
          <div v-for="faq in selectedCategory.faqs" :key="faq.id" class="faq-item">
            <button
              class="faq-question"
              @click="toggleAnswer(faq.id)"
              :aria-expanded="openFaqId === faq.id"
              :aria-controls="`faq-panel-${faq.id}`"
            >
              <i class="fa-regular fa-circle-question" aria-hidden="true"></i>
              <span class="question-text">{{ faq.question }}</span>
              <i
                class="fa-solid"
                :class="openFaqId === faq.id ? 'fa-chevron-up' : 'fa-chevron-down'"
                aria-hidden="true"
              ></i>
            </button>
            <transition name="accordion">
              <div
                v-show="openFaqId === faq.id"
                class="faq-answer"
                :id="`faq-panel-${faq.id}`"
                role="region"
                :aria-labelledby="`faq-button-${faq.id}`"
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
  </main>
  
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import http from '@/http'
import PageHero from '@/components/PageHero.vue'

const categories = ref([])
const selectedCategoryName = ref(null)
const openFaqId = ref(null)
const searchQuery = ref('')

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

const selectedCategory = computed(() =>
  categories.value.find((cat) => cat.category === selectedCategoryName.value)
)

const selectCategory = (categoryName) => {
  selectedCategoryName.value = categoryName
  openFaqId.value = null
}

const toggleAnswer = (id) => {
  openFaqId.value = openFaqId.value === id ? null : id
}

const filteredFAQs = computed(() => {
  if (!searchQuery.value.trim()) return []
  const query = searchQuery.value.toLowerCase()
  return categories.value
    .flatMap((category) =>
      category.faqs.map((faq) => ({ ...faq, category: category.category }))
    )
    .filter((faq) => faq.question.toLowerCase().includes(query))
})

const handleSearch = () => {}

onMounted(fetchFAQs)
</script>

<style scoped>
.faq-shell {
  display: grid;
  grid-template-columns: 320px 1fr;
  gap: 24px;
  padding: 24px;
  border-radius: 16px;
  background-color: var(--surface);
  box-shadow: 0 10px 30px rgba(31, 41, 55, 0.06);
  max-width: 1100px;
  margin: 0 auto 48px;
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
  border: 2px solid var(--accent);
  border-radius: 30px;
  font-size: 0.95rem;
  outline: none;
  transition: all 0.3s;
  box-shadow: 0 2px 10px rgba(122, 0, 25, 0.1);
}

.search-input:focus {
  border-color: var(--brand-strong);
  box-shadow: 0 4px 15px rgba(122, 0, 25, 0.2);
}

.search-icon {
  position: absolute;
  left: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: #780116;
  font-size: 1.1rem;
}

.faq-categories {
  position: sticky;
  top: calc(var(--header-height, 96px) + 16px);
  align-self: start;
  border: 1px solid transparent;
  border-radius: 12px;
  background: linear-gradient(var(--surface), var(--surface)) padding-box,
    linear-gradient(135deg, rgba(122, 0, 25, 0.25), rgba(240, 192, 64, 0.35))
      border-box;
  padding: 16px;
}

.aside-title {
  font-size: 1rem;
  color: var(--brand-strong);
  margin-bottom: 1rem;
  text-transform: uppercase;
  letter-spacing: 0.03em;
}

.aside-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.faq-categories li {
  padding: 10px 12px;
  cursor: pointer;
  border-radius: 8px;
  border: 1px solid var(--ink-5);
  transition: all 0.2s;
}

.faq-categories li:hover {
  background: rgba(122, 0, 25, 0.06);
}

.faq-categories li.active {
  background: var(--brand-strong);
  color: #fff;
  border-color: var(--accent);
}

.faq-main {
  flex: 1;
}

.section-title {
  font-size: 1.3rem;
  color: var(--brand-strong);
  border-bottom: 2px solid var(--accent-2);
  padding-bottom: 0.5rem;
  margin-bottom: 1rem;
}

.faq-item {
  background-color: var(--surface);
  border: 1px solid var(--ink-5);
  border-radius: 12px;
  margin-bottom: 1rem;
  overflow: hidden;
}

.faq-question {
  padding: 14px 16px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  text-align: left;
  background: transparent;
  border: 0;
  color: var(--ink-1);
}

.faq-answer {
  padding: 1rem;
  background-color: #fff;
  border-top: 1px solid var(--ink-5);
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
  color: var(--brand-strong);
  margin-left: 0.5rem;
}

@media (max-width: 1024px) {
  .faq-shell {
    grid-template-columns: 1fr;
    padding: 16px;
  }
  .faq-categories {
    position: static;
  }
}
</style>