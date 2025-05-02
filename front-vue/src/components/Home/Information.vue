<template>
    <section class="info-section">
      <div class="info-item">
        <div class="icon-circle">
            <img src="/img/truck.svg" alt="Envíos" class="icon-svg" />
        </div>
        <h3>Envíos a toda Guatemala</h3>
        <p>Recibe tus pedidos a la puerta de tu hogar</p>
      </div>
  
      <div class="info-item">
        <div class="icon-circle">
            <img src="/img/medal.svg" alt="Envíos" class="icon-svg" />
        </div>
        <h3>Satisfacción Garantizada</h3>
        <p>
          Compre con confianza con nuestra Garantía de satisfacción: le encantará o le reembolsaremos su dinero.
        </p>
      </div>
  
      <div class="info-item">
        <div class="icon-circle">
            <img src="/img/shield.svg" alt="Envíos" class="icon-svg" />
        </div>
        <h3>Pago Seguro</h3>
        <p>
          Tu seguridad es nuestra prioridad. Tus pagos están seguros con nosotros.
        </p>
      </div>
    </section>
  </template>
  
  <style scoped>
  .icon-svg{
    filter: invert(16%) sepia(97%) saturate(744%) hue-rotate(353deg) brightness(88%) contrast(106%);
    width: 70%;

  }
  .info-section {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    padding: 40px 20px;
    background-color: #fff;
    text-align: center;
    gap: 20px;
  }
  
  .info-item {
    flex: 1 1 250px;
    max-width: 300px;
    padding: 10px;
  }
  
  .icon-circle {
    width: 50px;
    height: 50px;
    margin: 0 auto 10px;
    background-color: #fde2b0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .icon-circle i {
    color: #8b0000;
    font-size: 20px;
  }
  
  h3 {
    font-size: 1.1rem;
    color: #8b0000;
    font-weight: bold;
    margin-bottom: 8px;
  }
  
  p {
    font-size: 0.9rem;
    color: #555;
  }
  </style>
  