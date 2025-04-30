<template>
    <div class="pagina-ecommerce" v-if="product">
      <!-- Banner -->
      <div class="banner-promocional">
        Obtén 25% DE DESCUENTO en tu primer pedido.
        <button class="boton-ordenar">Ordenar Ahora</button>
      </div>
  
      <!-- Ruta tipo migas de pan -->
      <div class="migas-pan">Inicio > {{ product.name }}</div>
  
      <!-- Sección de producto -->
      <div class="contenedor-producto">
        <div class="galeria-producto">
          <div class="imagen-principal">
            <img :src="product.imagen_url || 'https://via.placeholder.com/500x600'" :alt="product.name" />
          </div>
        </div>
  
        <div class="detalles-producto">
          <h1>{{ product.name }}</h1>
          <div class="precio">Q{{ product.price.toFixed(2) }}</div>
  
          <!-- Cantidad -->
          <div class="seleccion-cantidad">
            <h3>CANTIDAD</h3>
            <div class="control-cantidad">
              <button class="boton-cantidad" @click="cantidad > 1 && cantidad--">-</button>
              <span class="cantidad">{{ cantidad }}</span>
              <button class="boton-cantidad" @click="cantidad++">+</button>
            </div>
          </div>
  
          <!-- Botón -->
          <button class="boton-agregar-carrito" @click="agregarAlCarrito">
            Añadir al carrito
          </button>
          <div class="info-envio">ENVÍO GRATIS EN PEDIDOS SUPERIORES A Q100.*</div>
        </div>
      </div>
  
      <!-- Descripción -->
      <div class="descripcion-producto">
        <h2>Detalles</h2>
        <p>{{ product.descripcion }}</p>
        <ul class="lista-caracteristicas">
          <li>Categoría: {{ product.category }}</li>
          <li>Temática: {{ product.theme }}</li>
          <li>Precio base: Q{{ product.price.toFixed(2) }}</li>
          <li>Stock: {{ product.stock }}</li>
        </ul>
      </div>
    </div>
  
    <div v-else class="loading">
      <p>Cargando producto...</p>
    </div>
</template>
  
<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const product = ref(null)
const cantidad = ref(1)

onMounted(async () => {
  const id = route.params.id
  try {
    const response = await fetch(`http://localhost:8000/api/productos/${id}`)
    const data = await response.json()
    product.value = {
      ...data,
      name: data.nombre,
      price: parseFloat(data.precio_base),
      category: data.category?.name || 'Sin categoría',
      image: data.imagen ? `/storage/${data.imagen}` : 'https://via.placeholder.com/500x600'
    }
    } catch (error) {
        console.error('Error al cargar producto:', error)
    }
})

function aumentarCantidad() {
  cantidad.value++
}

function disminuirCantidad() {
  if (cantidad.value > 1) cantidad.value--
}
</script>
  
<style scoped>
  .pagina-ecommerce {
    font-family: 'Arial', sans-serif;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
  }
  
  .banner-promocional {
    background-color: #780116;
    color: white;
    text-align: center;
    padding: 10px;
    font-weight: bold;
  }
  
  .boton-ordenar {
    background: none;
    border: none;
    color: white;
    text-decoration: underline;
    cursor: pointer;
    margin-left: 5px;
    font-weight: bold;
  }
  
  .navegacion-principal {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 0;
    border-bottom: 1px solid #eee;
  }
  
  .logo-tienda {
    font-weight: bold;
    font-size: 1.5rem;
    color: #780116;
  }
  
  .enlaces-navegacion a {
    margin-left: 20px;
    text-decoration: none;
    color: #333;
    transition: color 0.2s;
  }
  
  .enlaces-navegacion a:hover {
    color: #780116;
  }
  
  .migas-pan {
    padding: 15px 0;
    color: #666;
  }
  
  .contenedor-producto {
    display: flex;
    gap: 50px;
    margin: 30px 0;
  }
  
  .galeria-producto {
    flex: 1;
  }
  
  .imagen-principal img {
    width: 100%;
    height: auto;
    border-radius: 8px;
  }
  
  .detalles-producto {
    flex: 1;
  }
  
  .detalles-producto h1 {
    font-size: 2rem;
    margin-bottom: 10px;
    color: #333;
  }
  
  .precio {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 30px;
    color: #780116;
  }
  
  .seleccion-color, .seleccion-talla, .seleccion-cantidad {
    margin-bottom: 25px;
  }
  
  .opciones-color {
    display: flex;
    gap: 15px;
    margin-top: 10px;
  }
  
  .opcion-color {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid transparent;
    transition: transform 0.2s;
  }
  
  .opcion-color:hover {
    transform: scale(1.1);
  }
  
  .opcion-color.activo {
    border-color: #780116;
  }
  
  .opcion-color.negro {
    background-color: #000;
  }
  
  .opcion-color.blanco {
    background-color: #fff;
    border: 1px solid #eee;
  }
  
  .opcion-color.gris {
    background-color: #808080;
  }
  
  .opciones-talla {
    display: flex;
    gap: 10px;
    margin-top: 10px;
  }
  
  .opcion-talla {
    padding: 8px 15px;
    background: white;
    border: 1px solid #ddd;
    cursor: pointer;
    transition: all 0.2s;
  }
  
  .opcion-talla:hover {
    border-color: #780116;
  }
  
  .opcion-talla.activo {
    background: #780116;
    color: white;
    border-color: #780116;
  }
  
  .control-cantidad {
    display: flex;
    align-items: center;
    margin-top: 10px;
  }
  
  .boton-cantidad {
    padding: 5px 15px;
    background: #f5f5f5;
    border: 1px solid #ddd;
    cursor: pointer;
    transition: all 0.2s;
  }
  
  .boton-cantidad:hover {
    background: #780116;
    color: white;
    border-color: #780116;
  }
  
  .cantidad {
    padding: 0 15px;
  }
  
  .boton-agregar-carrito {
    background: #780116;
    color: white;
    border: none;
    padding: 15px 40px;
    font-size: 1rem;
    cursor: pointer;
    margin-top: 20px;
    width: 100%;
    border-radius: 4px;
    transition: background-color 0.2s;
  }
  
  .boton-agregar-carrito:hover {
    background-color: #5c0114;
  }
  
  .info-envio {
    margin-top: 10px;
    font-size: 0.9rem;
    color: #666;
  }
  
  .descripcion-producto {
    margin: 50px 0;
    padding-top: 30px;
    border-top: 1px solid #eee;
  }
  
  .descripcion-producto h2 {
    margin-bottom: 20px;
    color: #333;
  }
  
  .descripcion-producto p {
    margin-bottom: 15px;
    line-height: 1.6;
    color: #555;
  }
  
  .lista-caracteristicas {
    margin-top: 20px;
    padding-left: 20px;
    color: #555;
  }
  
  .lista-caracteristicas li {
    margin-bottom: 10px;
  }
  
  @media (max-width: 768px) {
    .contenedor-producto {
      flex-direction: column;
      gap: 30px;
    }
  }
</style>