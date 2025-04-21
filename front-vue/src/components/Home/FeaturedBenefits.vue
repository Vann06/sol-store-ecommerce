<template>
    <div class="benefits-section">
      <div class="benefit-card">
        <h3 class="benefit-title">Envíos a toda Guatemala</h3>
        <p class="benefit-text">Recibe tus pedidos a la puerta de tu hogar</p>
      </div>
      <div class="benefit-card">
        <h3 class="benefit-title">Satisfacción Garantizada</h3>
        <p class="benefit-text">Compra con confianza con nuestra garantía</p>
      </div>
      <div class="benefit-card">
        <h3 class="benefit-title">Pago Seguro</h3>
        <p class="benefit-text">Tus pagos están seguros con nosotros</p>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'BenefitsSection'
  }
  </script>
  
  <style scoped>
  .benefits-section {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin: 30px 0;
    text-align: center;
  }
  
  .benefit-card {
    padding: 15px;
    border: 1px solid #eee;
    border-radius: 8px;
  }
  
  .benefit-title {
    color: #780116;
    margin-bottom: 8px;
    font-size: 16px;
    font-weight: 600;
  }
  
  .benefit-text {
    font-size: 14px;
    color: #666;
    margin: 0;
  }
  
  @media (max-width: 768px) {
    .benefits-section {
      grid-template-columns: 1fr;
    }
  }
  </style>
