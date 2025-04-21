<template>
    <section class="product-section">
      <h2>Más Comprados</h2>
      <div class="product-grid">
        <div class="product-card" v-for="(product, index) in products" :key="index">
          <img :src="product.image" :alt="product.name">
          <h3>{{ product.name }}</h3>
          <p class="price">{{ product.price }} GTQ</p>
          <button class="add-to-cart">Añadir al carrito</button>
        </div>
      </div>
    </section>
  </template>
  
  <script>
  export default {
    name: 'BestSellers',
    data() {
      return {
        products: [
          { name: 'Cáscara de Dragon Ball', price: '275.00', image: 'https://via.placeholder.com/200x200' },
          { name: 'Llavero Creeper', price: '95.00', image: 'https://via.placeholder.com/200x200' },
          { name: 'Camiseta Anime', price: '175.00', image: 'https://via.placeholder.com/200x200' }
        ]
      }
    }
  }
  </script>
  
  <style scoped>
  .product-section {
    margin: 50px 0;
  }
  
  .product-section h2 {
    color: #333;
    margin-bottom: 20px;
    font-size: 24px;
    border-bottom: 2px solid #780116;
    padding-bottom: 10px;
  }
  
  .product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
  }
  
  .product-card {
    border: 1px solid #eee;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
  }
  
  .product-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 4px;
    margin-bottom: 15px;
  }
  
  .price {
    color: #780116;
    font-weight: bold;
    font-size: 18px;
    margin: 10px 0;
  }
  
  .add-to-cart {
    width: 100%;
    padding: 10px;
    background: #780116;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 10px;
  }
  
  @media (max-width: 768px) {
    .product-grid {
      grid-template-columns: 1fr 1fr;
    }
  }
  
  @media (max-width: 480px) {
    .product-grid {
      grid-template-columns: 1fr;
    }
  }
  </style>
    